<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจองส่วนกลาง - สำนักงานเทศบาล</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@500;600&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #F5F7F6; color: #334155; }
        h1, h2, h3, .font-heading { font-family: 'Prompt', sans-serif; }
        .fc { --fc-border-color: #EAEFEF; --fc-today-bg-color: #F2F8F6; --fc-page-bg-color: #FFFFFF; }
        .fc-toolbar-title { font-family: 'Prompt', sans-serif !important; font-size: 1.1rem !important; font-weight: 600 !important; color: #1E293B; }
        .fc-button { background-color: #FFFFFF !important; border: 1px solid #DCE3E1 !important; color: #475569 !important; border-radius: 0.65rem !important; font-size: 0.85rem !important; font-weight: 500 !important; box-shadow: none !important; padding: 0.4rem 0.85rem !important; }
        .fc-button:hover { background-color: #F4F7F6 !important; }
        .fc-button-active { background-color: #2E6F65 !important; border-color: #2E6F65 !important; color: #FFFFFF !important; }
        .fc-col-header-cell { background-color: #F9FBFA; padding: 0.5rem 0 !important; font-weight: 600; color: #475569; }
        .fc-event { border-radius: 0.45rem !important; border: none !important; padding: 3px 6px !important; font-size: 0.78rem !important; font-weight: 500; cursor: pointer; }
    </style>
</head>
<body class="min-h-screen flex flex-col lg:flex-row">
    <aside class="w-full lg:w-64 bg-white border-b lg:border-b-0 lg:border-r border-[#E3E8E6] flex flex-col justify-between shrink-0 sticky top-0 z-30">
        <div>
            <div class="px-6 py-5 flex items-center justify-between border-b border-[#F0F4F3]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#2E6F65] text-white flex items-center justify-center shadow-sm"><i class="fas fa-landmark"></i></div>
                    <div>
                        <span class="font-heading font-semibold text-base text-slate-800 block leading-tight">ระบบจองส่วนกลาง</span>
                        <span class="text-xs text-slate-400">สำนักงานเทศบาล</span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="lg:hidden">@csrf<button type="submit" class="text-xs font-semibold text-rose-600 bg-rose-50 px-3 py-1.5 rounded-lg">ออก</button></form>
            </div>
            <nav class="p-3 lg:p-4 flex lg:flex-col gap-1.5 overflow-x-auto">
                <button onclick="switchTab('booking')" id="tab-booking" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold bg-[#EBF3F1] text-[#2E6F65] whitespace-nowrap">
                    <i class="fas fa-calendar-alt w-4 text-center"></i><span>ปฏิทินและจองคิว</span>
                </button>
                <button onclick="switchTab('list')" id="tab-list" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 whitespace-nowrap">
                    <i class="fas fa-list-check w-4 text-center"></i><span>รายการจองทั้งหมด</span>
                </button>
                <button onclick="switchTab('profile')" id="tab-profile" class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 whitespace-nowrap">
                    <i class="fas fa-user-circle w-4 text-center"></i><span>โปรไฟล์ส่วนตัว</span>
                </button>
            </nav>
        </div>
        <div class="hidden lg:block p-4 border-t border-[#F0F4F3] bg-[#FAFBFB]">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-[#DCE8E5] text-[#2E6F65] font-bold flex items-center justify-center text-sm">{{ mb_substr($user->name, 0, 1) }}</div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-slate-800 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $user->department }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="w-full py-2 rounded-xl border border-slate-200 bg-white hover:bg-rose-50 hover:text-rose-600 text-xs font-semibold text-slate-600"><i class="fas fa-sign-out-alt mr-1.5"></i> ออกจากระบบ</button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] text-sm font-medium flex items-center justify-between">
                <span><i class="fas fa-check-circle mr-2"></i> {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif
        @if(session('error') || $errors->any())
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium flex items-center justify-between">
                <span><i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') ?? $errors->first() }}</span>
                <button onclick="this.parentElement.remove()">&times;</button>
            </div>
        @endif

        <div id="view-booking" class="space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">ตารางการใช้ทรัพยากรส่วนกลาง</h1>
                    <p class="text-sm text-slate-500 mt-0.5">คลิกหรือลากเลือกช่วงเวลาบนปฏิทินเพื่อจองคิวได้ทันที</p>
                </div>
                <button onclick="openBookingModal()" class="bg-[#2E6F65] hover:bg-[#245850] text-white px-5 py-2.5 rounded-xl shadow-sm font-semibold text-sm flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i><span>สร้างการจองใหม่</span>
                </button>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-[#E3E8E6]">
                <div class="flex flex-wrap items-center gap-1.5 text-xs font-semibold">
                    <button onclick="filterCalendar('all')" id="cal-filter-all" class="px-3.5 py-1.5 rounded-xl bg-[#2E6F65] text-white">ทั้งหมด</button>
                    <button onclick="filterCalendar('ห้องประชุม')" id="cal-filter-room" class="px-3.5 py-1.5 rounded-xl text-slate-600 hover:bg-slate-100 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> ห้องประชุม</button>
                    <button onclick="filterCalendar('รถส่วนกลาง')" id="cal-filter-vehicle" class="px-3.5 py-1.5 rounded-xl text-slate-600 hover:bg-slate-100 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-amber-500"></span> รถส่วนกลาง</button>
                    <button onclick="filterCalendar('วาระ')" id="cal-filter-agenda" class="px-3.5 py-1.5 rounded-xl text-slate-600 hover:bg-slate-100 flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sky-500"></span> วาระการประชุม</button>
                </div>
                <span class="text-xs text-slate-400 hidden sm:inline"><i class="fas fa-shield-alt mr-1 text-[#2E6F65]"></i> ระบบตรวจสอบคิวซ้ำอัตโนมัติ</span>
            </div>

            <div class="bg-white rounded-2xl border border-[#E3E8E6] p-4 sm:p-6">
                <div id="calendar"></div>
            </div>
        </div>

        <div id="view-list" class="hidden space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">รายการติดตามสถานะการจอง</h1>
                    <p class="text-sm text-slate-500 mt-0.5">ตรวจสอบคิวทั้งหมดและจัดการรายการจองของคุณ</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <input type="text" id="list-search" oninput="applyListFilters()" placeholder="ค้นหารายการ / ผู้จอง..." class="px-4 py-2 rounded-xl border border-[#DCE3E1] bg-white text-sm outline-none focus:border-[#2E6F65]">
                    <div class="flex bg-white p-1 rounded-xl border border-[#DCE3E1] text-xs font-semibold">
                        <button onclick="setOwnerFilter('all')" id="btn-filter-all" class="px-3 py-1.5 rounded-lg bg-[#2E6F65] text-white">ทั้งหมด</button>
                        <button onclick="setOwnerFilter('mine')" id="btn-filter-mine" class="px-3 py-1.5 rounded-lg text-slate-600 hover:bg-slate-50">เฉพาะของฉัน</button>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-[#E3E8E6] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#EAEFEF] text-sm">
                        <thead class="bg-[#F9FBFA] text-slate-600">
                            <tr>
                                <th class="px-5 py-3.5 text-left font-semibold">ประเภท</th>
                                <th class="px-5 py-3.5 text-left font-semibold">รายละเอียดรายการ</th>
                                <th class="px-5 py-3.5 text-left font-semibold">ผู้จอง / สังกัด</th>
                                <th class="px-5 py-3.5 text-left font-semibold">วัน-เวลาที่ใช้งาน</th>
                                <th class="px-5 py-3.5 text-left font-semibold">สถานะ</th>
                                <th class="px-5 py-3.5 text-right font-semibold">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#F1F5F4]">
                            @forelse($bookings as $b)
                                <tr class="booking-row hover:bg-[#FAFBFB]" data-mine="{{ $b->user_id === $user->id ? '1' : '0' }}" data-search="{{ mb_strtolower($b->booking_type . ' ' . $b->item_detail . ' ' . $b->booker_name) }}">
                                    <td class="px-5 py-3.5 whitespace-nowrap font-semibold">{{ $b->booking_type }}</td>
                                    <td class="px-5 py-3.5 font-medium text-slate-800">{{ $b->item_detail }}</td>
                                    <td class="px-5 py-3.5 text-slate-600">{{ $b->booker_name }}</td>
                                    <td class="px-5 py-3.5 text-xs text-slate-500 whitespace-nowrap">{{ \Carbon\Carbon::parse($b->start_datetime)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($b->end_datetime)->format('H:i') }} น.</td>
                                    <td class="px-5 py-3.5 whitespace-nowrap"><span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $b->status === 'Approved' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $b->status }}</span></td>
                                    <td class="px-5 py-3.5 text-right whitespace-nowrap">
                                        @if($b->user_id === $user->id && in_array($b->status, ['Approved', 'Pending']))
                                            <form method="POST" action="{{ route('user.bookings.cancel', $b->id) }}" onsubmit="return confirm('ยืนยันการยกเลิกรายการจองนี้?')">@csrf @method('PATCH')
                                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:bg-rose-50 px-3 py-1.5 rounded-lg">ยกเลิกคิว</button>
                                            </form>
                                        @else - @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-10 text-slate-400">ยังไม่มีรายการจองในระบบ</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="view-profile" class="hidden space-y-5">
            <h1 class="text-xl sm:text-2xl font-semibold text-slate-800">ข้อมูลส่วนตัวผู้ใช้งาน</h1>
            <div class="bg-white rounded-2xl border border-[#E3E8E6] p-6 sm:p-8 max-w-2xl">
                <form method="POST" action="{{ route('user.profile.update') }}" class="space-y-4 text-sm">
                    @csrf @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block font-semibold text-slate-700 mb-1.5">ชื่อผู้ใช้งาน (Username)</label><input type="text" name="username" value="{{ $user->username }}" required class="w-full rounded-xl px-4 py-2.5 border border-slate-200 bg-[#FAFBFB]"></div>
                        <div><label class="block font-semibold text-slate-700 mb-1.5">เบอร์โทรศัพท์ติดต่อ</label><input type="text" name="phone" value="{{ $user->phone }}" required class="w-full rounded-xl px-4 py-2.5 border border-slate-200 bg-[#FAFBFB]"></div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div><label class="block font-semibold text-slate-700 mb-1.5">ชื่อ - นามสกุล</label><input type="text" name="name" value="{{ $user->name }}" required class="w-full rounded-xl px-4 py-2.5 border border-slate-200 bg-[#FAFBFB]"></div>
                        <div><label class="block font-semibold text-slate-700 mb-1.5">สังกัด / กอง</label><input type="text" name="department" value="{{ $user->department }}" required class="w-full rounded-xl px-4 py-2.5 border border-slate-200 bg-[#FAFBFB]"></div>
                    </div>
                    <div><label class="block font-semibold text-slate-700 mb-1.5">รหัสผ่านใหม่ (เว้นว่างไว้หากใช้รหัสผ่านเดิม)</label><input type="password" name="password" placeholder="••••••••" class="w-full rounded-xl px-4 py-2.5 border border-slate-200 bg-[#FAFBFB]"></div>
                    <div class="pt-2 flex justify-end"><button type="submit" class="px-6 py-2.5 rounded-xl bg-[#2E6F65] hover:bg-[#245850] text-white font-semibold">บันทึกการเปลี่ยนแปลง</button></div>
                </form>
            </div>
        </div>
    </main>

    <div id="booking-modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] hidden z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-3xl shadow-xl border border-[#E3E8E6] w-full max-w-lg overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b border-[#F0F4F3] bg-[#FAFBFB]">
                <h3 class="text-base font-semibold text-slate-800">สร้างรายการจองใหม่</h3>
                <button onclick="closeBookingModal()" class="text-slate-400 text-xl">&times;</button>
            </div>
            <form method="POST" action="{{ route('user.bookings.store') }}" class="p-6 space-y-4 text-sm">
                @csrf
                <div><label class="block font-semibold text-slate-700 mb-1">ผู้ขอใช้บริการ / สังกัด</label><input type="text" name="booker_name" value="{{ $user->name }} ({{ $user->department }})" required class="w-full rounded-xl border border-slate-200 bg-[#FAFBFB] px-4 py-2.5"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">ประเภทการจอง</label>
                        <select name="booking_type" id="bk-type" onchange="handleTypeChange()" required class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5">
                            <option value="ห้องประชุม">ห้องประชุม</option>
                            <option value="รถส่วนกลาง">รถส่วนกลาง</option>
                            <option value="วาระ">วาระการประชุม</option>
                        </select>
                    </div>
                    <div>
                        <label id="bk-item-label" class="block font-semibold text-slate-700 mb-1">เลือกห้องประชุม</label>
                        <select name="item_detail" id="bk-item-select" required class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5"></select>
                    </div>
                </div>
                <div id="bk-location-box" class="hidden">
                    <label class="block font-semibold text-[#2E6F65] mb-1">สถานที่จัดการประชุม (ระบุเอง)</label>
                    <input type="text" name="location" id="bk-location" placeholder="เช่น ห้องประชุมสภาเทศบาล ชั้น 3 หรือ ประชุมออนไลน์" class="w-full rounded-xl border border-[#B8D4CE] bg-[#F4F9F8] px-4 py-2.5">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div><label class="block font-semibold text-slate-700 mb-1">วัน-เวลาเริ่มต้น</label><input type="datetime-local" name="start_datetime" id="bk-start" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5"></div>
                    <div><label class="block font-semibold text-slate-700 mb-1">วัน-เวลาสิ้นสุด</label><input type="datetime-local" name="end_datetime" id="bk-end" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5"></div>
                </div>
                <div><label class="block font-semibold text-slate-700 mb-1">รายละเอียดเพิ่มเติม / รูปแบบจัดโต๊ะ</label><textarea name="remarks" rows="2" class="w-full rounded-xl border border-slate-200 px-4 py-2.5"></textarea></div>
                <div class="flex justify-end gap-2.5 pt-3 border-t">
                    <button type="button" onclick="closeBookingModal()" class="px-4 py-2.5 rounded-xl bg-slate-100 font-semibold text-slate-600">ยกเลิก</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#2E6F65] text-white font-semibold">ยืนยันการจอง</button>
                </div>
            </form>
        </div>
    </div>

    <div id="event-detail-modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-[2px] hidden z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-3xl shadow-xl border border-[#E3E8E6] w-full max-w-md overflow-hidden">
            <div class="flex justify-between items-center px-6 py-4 border-b bg-[#FAFBFB]">
                <h3 class="text-base font-semibold text-slate-800">รายละเอียดรายการจอง</h3>
                <button onclick="document.getElementById('event-detail-modal').classList.add('hidden')" class="text-slate-400 text-xl">&times;</button>
            </div>
            <div class="p-6 space-y-3 text-sm">
                <div class="flex justify-between py-1.5 border-b"><span class="text-slate-400">ประเภท</span><span id="det-type" class="font-semibold"></span></div>
                <div class="flex justify-between py-1.5 border-b"><span class="text-slate-400">รายการ</span><span id="det-item" class="font-semibold text-[#2E6F65] text-right"></span></div>
                <div class="flex justify-between py-1.5 border-b"><span class="text-slate-400">ผู้จอง</span><span id="det-booker"></span></div>
                <div class="flex justify-between py-1.5 border-b"><span class="text-slate-400">ช่วงเวลา</span><span id="det-time"></span></div>
                <div class="flex justify-between py-1.5 border-b"><span class="text-slate-400">สถานะ</span><span id="det-status" class="font-semibold text-emerald-700"></span></div>
                <div><span class="text-slate-400 block mb-1">หมายเหตุ</span><p id="det-remarks" class="p-3 rounded-xl bg-[#F7FAF9] text-xs"></p></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/th.js"></script>
    <script>
        const masterData = {
            rooms: @json($rooms),
            vehicles: @json($vehicles),
            agendas: @json($agendas),
            events: @json($events).map(ev => {
                const t = ev.extendedProps.bookingType;
                if (t === 'ห้องประชุม') { ev.backgroundColor = '#D1FAE5'; ev.textColor = '#065F46'; }
                else if (t === 'รถส่วนกลาง') { ev.backgroundColor = '#FEF3C7'; ev.textColor = '#92400E'; }
                else { ev.backgroundColor = '#E0F2FE'; ev.textColor = '#075985'; }
                return ev;
            })
        };
        let calendar, ownerFilter = 'all';

        document.addEventListener('DOMContentLoaded', () => {
            handleTypeChange();
            calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                initialView: 'timeGridWeek',
                locale: 'th',
                slotMinTime: '08:00:00',
                slotMaxTime: '18:00:00',
                allDaySlot: false,
                contentHeight: 'auto',
                selectable: true,
                selectMirror: true,
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
                events: masterData.events,
                select: function(info) {
                    document.getElementById('bk-start').value = toLocalInput(info.start);
                    document.getElementById('bk-end').value = toLocalInput(info.end);
                    openBookingModal();
                    calendar.unselect();
                },
                eventClick: function(info) {
                    const p = info.event.extendedProps;
                    document.getElementById('det-type').innerText = p.bookingType;
                    document.getElementById('det-item').innerText = p.itemDetail;
                    document.getElementById('det-booker').innerText = p.bookerName;
                    document.getElementById('det-status').innerText = p.status;
                    document.getElementById('det-remarks').innerText = p.remarks || '-';
                    const s = info.event.start.toLocaleString('th-TH', { dateStyle: 'short', timeStyle: 'short' });
                    const e = info.event.end ? info.event.end.toLocaleString('th-TH', { timeStyle: 'short' }) : '';
                    document.getElementById('det-time').innerText = `${s} - ${e} น.`;
                    document.getElementById('event-detail-modal').classList.remove('hidden');
                }
            });
            calendar.render();
        });

        function switchTab(tab) {
            ['booking', 'list', 'profile'].forEach(t => {
                document.getElementById('view-' + t).classList.add('hidden');
                document.getElementById('tab-' + t).className = 'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 whitespace-nowrap';
            });
            document.getElementById('view-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).className = 'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold bg-[#EBF3F1] text-[#2E6F65] whitespace-nowrap';
            if (tab === 'booking' && calendar) setTimeout(() => calendar.updateSize(), 50);
        }

        function filterCalendar(type) {
            const ids = { 'all': 'cal-filter-all', 'ห้องประชุม': 'cal-filter-room', 'รถส่วนกลาง': 'cal-filter-vehicle', 'วาระ': 'cal-filter-agenda' };
            Object.values(ids).forEach(id => document.getElementById(id).className = 'px-3.5 py-1.5 rounded-xl text-slate-600 hover:bg-slate-100 flex items-center gap-1.5');
            document.getElementById(ids[type]).className = 'px-3.5 py-1.5 rounded-xl bg-[#2E6F65] text-white flex items-center gap-1.5';
            calendar.removeAllEvents();
            calendar.addEventSource(type === 'all' ? masterData.events : masterData.events.filter(e => e.extendedProps.bookingType === type));
        }

        function handleTypeChange() {
            const type = document.getElementById('bk-type').value;
            const label = document.getElementById('bk-item-label');
            const select = document.getElementById('bk-item-select');
            const locBox = document.getElementById('bk-location-box');
            const locInput = document.getElementById('bk-location');
            select.innerHTML = '';

            if (type === 'ห้องประชุม') {
                label.innerText = 'เลือกห้องประชุม'; locBox.classList.add('hidden'); locInput.required = false;
                masterData.rooms.forEach(r => { const opt = document.createElement('option'); opt.value = r.name; opt.textContent = `${r.name} (จุ ${r.capacity} คน)`; select.appendChild(opt); });
            } else if (type === 'รถส่วนกลาง') {
                label.innerText = 'เลือกป้ายทะเบียนรถ'; locBox.classList.add('hidden'); locInput.required = false;
                masterData.vehicles.forEach(v => { const opt = document.createElement('option'); opt.value = `${v.license_plate} (${v.model})`; opt.textContent = `${v.license_plate} - ${v.model}`; select.appendChild(opt); });
            } else if (type === 'วาระ') {
                label.innerText = 'เลือกวาระการประชุม'; locBox.classList.remove('hidden'); locInput.required = true;
                masterData.agendas.forEach(a => { const opt = document.createElement('option'); opt.value = a.title; opt.textContent = a.title; select.appendChild(opt); });
                const optOther = document.createElement('option'); optOther.value = 'เสนอวาระทั่วไป'; optOther.textContent = '+ เสนอวาระทั่วไป'; select.appendChild(optOther);
            }
        }

        function setOwnerFilter(mode) {
            ownerFilter = mode;
            document.getElementById('btn-filter-all').className = mode === 'all' ? 'px-3 py-1.5 rounded-lg bg-[#2E6F65] text-white' : 'px-3 py-1.5 rounded-lg text-slate-600';
            document.getElementById('btn-filter-mine').className = mode === 'mine' ? 'px-3 py-1.5 rounded-lg bg-[#2E6F65] text-white' : 'px-3 py-1.5 rounded-lg text-slate-600';
            applyListFilters();
        }
        function applyListFilters() {
            const q = document.getElementById('list-search').value.toLowerCase();
            document.querySelectorAll('.booking-row').forEach(tr => {
                tr.style.display = ((ownerFilter === 'all' || tr.dataset.mine === '1') && tr.dataset.search.includes(q)) ? '' : 'none';
            });
        }
        function openBookingModal() { document.getElementById('booking-modal').classList.remove('hidden'); }
        function closeBookingModal() { document.getElementById('booking-modal').classList.add('hidden'); }
        function toLocalInput(dateObj) { const d = new Date(dateObj); return new Date(d.getTime() - (d.getTimezoneOffset() * 60000)).toISOString().slice(0, 16); }
    </script>
</body>
</html>