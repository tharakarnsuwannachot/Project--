<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $bookings = Booking::orderByDesc('id')->get();
        $rooms = Room::all();
        $vehicles = Vehicle::all();
        $agendas = Agenda::all();
        $users = User::where('role', 'user')->get();

        return view('admin', compact('bookings', 'rooms', 'vehicles', 'agendas', 'users'));
    }

    public function updateBooking(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $start = Carbon::parse($request->start_datetime);
        $end = Carbon::parse($request->end_datetime);

        if ($end <= $start) {
            return back()->with('error', 'เวลาสิ้นสุดต้องมากกว่าเวลาเริ่มต้น');
        }

        if (in_array($request->status, ['Approved', 'Pending']) && in_array($request->booking_type, ['ห้องประชุม', 'รถส่วนกลาง'])) {
            $conflict = Booking::where('id', '!=', $id)
                ->where('booking_type', $request->booking_type)
                ->where('item_detail', $request->item_detail)
                ->whereIn('status', ['Approved', 'Pending'])
                ->where('start_datetime', '<', $end)
                ->where('end_datetime', '>', $start)
                ->first();

            if ($conflict) {
                return back()->with('error', "เวลาที่แก้ไขชนกับรหัสการจอง {$conflict->booking_code}");
            }
        }

        $booking->update([
            'booker_name' => $request->booker_name,
            'booking_type' => $request->booking_type,
            'item_detail' => $request->item_detail,
            'start_datetime' => $start,
            'end_datetime' => $end,
            'status' => $request->status,
            'remarks' => $request->remarks ?? '-',
        ]);

        return back()->with('success', 'อัปเดตข้อมูลการจองเรียบร้อยแล้ว');
    }

    public function destroyBooking($id)
    {
        Booking::findOrFail($id)->delete();
        return back()->with('success', 'ลบรายการจองเรียบร้อยแล้ว');
    }

    public function storeRoom(Request $request)
    {
        Room::create([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'layouts' => $request->layouts,
            'status' => 'เปิดใช้งาน',
        ]);
        return back()->with('success', 'เพิ่มห้องประชุมเรียบร้อยแล้ว');
    }

    public function destroyRoom($id)
    {
        Room::findOrFail($id)->delete();
        return back()->with('success', 'ลบห้องประชุมเรียบร้อยแล้ว');
    }

    public function storeVehicle(Request $request)
    {
        Vehicle::create([
            'license_plate' => $request->license_plate,
            'model' => $request->model,
            'seats' => $request->seats,
            'status' => 'พร้อมใช้งาน',
        ]);
        return back()->with('success', 'เพิ่มรถส่วนกลางเรียบร้อยแล้ว');
    }

    public function destroyVehicle($id)
    {
        Vehicle::findOrFail($id)->delete();
        return back()->with('success', 'ลบรถส่วนกลางเรียบร้อยแล้ว');
    }

    public function storeAgenda(Request $request)
    {
        Agenda::create([
            'title' => $request->title,
            'meeting_type' => $request->meeting_type,
            'department' => $request->department,
            'target_date' => now()->toDateString(),
        ]);
        return back()->with('success', 'เพิ่มวาระการประชุมเรียบร้อยแล้ว');
    }

    public function destroyAgenda($id)
    {
        Agenda::findOrFail($id)->delete();
        return back()->with('success', 'ลบวาระการประชุมเรียบร้อยแล้ว');
    }

    public function storeUser(Request $request)
    {
        User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->username . '@municipality.local',
            'department' => $request->department,
            'phone' => $request->phone ?? '-',
            'role' => 'user',
            'password' => Hash::make($request->password),
        ]);
        return back()->with('success', 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว');
    }

    public function destroyUser($id)
    {
        User::findOrFail($id)->delete();
        return back()->with('success', 'ลบผู้ใช้งานเรียบร้อยแล้ว');
    }
}