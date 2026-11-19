<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UserBookingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $bookings = Booking::orderByDesc('start_datetime')->get();
        $rooms = Room::where('status', 'เปิดใช้งาน')->get();
        $vehicles = Vehicle::where('status', 'พร้อมใช้งาน')->get();
        $agendas = Agenda::all();

        $events = $bookings->whereNotIn('status', ['Rejected', 'Cancelled'])->map(function ($b) {
            return [
                'id' => $b->booking_code,
                'title' => "[{$b->booking_type}] {$b->item_detail} ({$b->booker_name})",
                'start' => Carbon::parse($b->start_datetime)->toIso8601String(),
                'end' => Carbon::parse($b->end_datetime)->toIso8601String(),
                'extendedProps' => [
                    'bookingType' => $b->booking_type,
                    'itemDetail' => $b->item_detail,
                    'bookerName' => $b->booker_name,
                    'status' => $b->status,
                    'remarks' => $b->remarks ?? '-',
                ],
            ];
        })->values();

        return view('user', compact('user', 'bookings', 'rooms', 'vehicles', 'agendas', 'events'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booker_name' => 'required|string|max:255',
            'booking_type' => 'required|in:ห้องประชุม,รถส่วนกลาง,วาระ',
            'item_detail' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_datetime' => 'required|date',
            'end_datetime' => 'required|date|after:start_datetime',
            'remarks' => 'nullable|string',
        ], [
            'end_datetime.after' => 'เวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น',
        ]);

        $itemDetail = ($request->booking_type === 'วาระ' && $request->filled('location'))
            ? "{$request->item_detail} (สถานที่: {$request->location})"
            : $request->item_detail;

        $start = Carbon::parse($request->start_datetime);
        $end = Carbon::parse($request->end_datetime);

        try {
            $booking = DB::transaction(function () use ($request, $itemDetail, $start, $end) {
                if (in_array($request->booking_type, ['ห้องประชุม', 'รถส่วนกลาง'])) {
                    $conflict = Booking::where('booking_type', $request->booking_type)
                        ->where('item_detail', $itemDetail)
                        ->whereIn('status', ['Approved', 'Pending'])
                        ->where('start_datetime', '<', $end)
                        ->where('end_datetime', '>', $start)
                        ->first();

                    if ($conflict) {
                        $sStr = Carbon::parse($conflict->start_datetime)->format('d/m/Y H:i');
                        $eStr = Carbon::parse($conflict->end_datetime)->format('H:i');
                        throw new \Exception("ไม่สามารถจองได้! \"{$itemDetail}\" ถูกจองแล้วในช่วง {$sStr} - {$eStr} น. (โดย {$conflict->booker_name})");
                    }
                }

                return Booking::create([
                    'booking_code' => 'BK' . now()->format('ymdHis') . rand(10, 99),
                    'user_id' => Auth::id(),
                    'booker_name' => $request->booker_name,
                    'booking_type' => $request->booking_type,
                    'item_detail' => $itemDetail,
                    'start_datetime' => $start,
                    'end_datetime' => $end,
                    'status' => 'Approved',
                    'remarks' => $request->remarks ?? '-',
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        $this->syncCalendarAndNotifyLine($booking);

        return back()->with('success', 'บันทึกการจอง ลงปฏิทิน และส่งแจ้งเตือน LINE เรียบร้อยแล้ว!');
    }

    public function cancel($id)
    {
        $booking = Booking::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $booking->update(['status' => 'Cancelled']);
        return back()->with('success', 'ยกเลิกรายการจองเรียบร้อยแล้ว');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'username' => 'required|string|unique:users,username,' . $user->id,
            'name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'password' => 'nullable|string|min:4',
        ]);

        $oldName = $user->name;
        $user->username = $request->username;
        $user->name = $request->name;
        $user->department = $request->department;
        $user->phone = $request->phone;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        if ($oldName !== $request->name) {
            Booking::where('user_id', $user->id)->update([
                'booker_name' => "{$request->name} ({$request->department})",
            ]);
        }

        return back()->with('success', 'อัปเดตข้อมูลโปรไฟล์ใหม่เรียบร้อยแล้ว');
    }

    private function syncCalendarAndNotifyLine(Booking $booking): void
    {
        $startStr = Carbon::parse($booking->start_datetime)->format('d/m/Y H:i');
        $endStr = Carbon::parse($booking->end_datetime)->format('d/m/Y H:i');

        $gasUrl = env('GAS_CALENDAR_WEBHOOK_URL');
        if ($gasUrl && !str_contains($gasUrl, 'ใส่_WEB_APP_URL')) {
            try {
                $res = Http::timeout(8)->post($gasUrl, [
                    'title' => "[จอง{$booking->booking_type}] {$booking->item_detail} - {$booking->booker_name}",
                    'startDateTime' => Carbon::parse($booking->start_datetime)->toIso8601String(),
                    'endDateTime' => Carbon::parse($booking->end_datetime)->toIso8601String(),
                    'description' => "รหัสการจอง: {$booking->booking_code}\nผู้จอง: {$booking->booker_name}\nหมายเหตุ: {$booking->remarks}",
                ]);
                if ($res->successful() && isset($res->json()['eventId'])) {
                    $booking->update(['calendar_event_id' => $res->json()['eventId']]);
                }
            } catch (\Throwable $e) {
            }
        }

        $lineToken = env('LINE_CHANNEL_ACCESS_TOKEN');
        $lineTarget = env('LINE_TARGET_ID');
        if ($lineToken && $lineTarget && !str_contains($lineToken, 'ใส่_CHANNEL')) {
            $message = "🔔 มีการจองใหม่ในระบบ!\n"
                . "🔖 รหัส: {$booking->booking_code}\n"
                . "👤 ผู้จอง: {$booking->booker_name}\n"
                . "📌 ประเภท: {$booking->booking_type}\n"
                . "📍 รายการ: {$booking->item_detail}\n"
                . "🕒 เริ่ม: {$startStr} น.\n"
                . "🕔 สิ้นสุด: {$endStr} น.\n"
                . "📝 หมายเหตุ: {$booking->remarks}";

            try {
                Http::withToken($lineToken)->timeout(8)->post('https://api.line.me/v2/bot/message/push', [
                    'to' => $lineTarget,
                    'messages' => [['type' => 'text', 'text' => $message]],
                ]);
            } catch (\Throwable $e) {
            }
        }
    }
}