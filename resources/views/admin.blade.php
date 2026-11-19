<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ดผู้ดูแลระบบ (Admin) - สำนักงานเทศบาล</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@500;600&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #F5F7F6; color: #334155; }
        h1, h2, h3, .font-heading { font-family: 'Prompt', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex flex-col lg:flex-row">
    <aside class="w-full lg:w-64 bg-white border-b lg:border-b-0 lg:border-r border-[#E3E8E6] flex flex-col justify-between shrink-0 sticky top-0 z-30">
        <div>
            <div class="px-6 py-5 flex items-center justify-between border-b border-[#F0F4F3]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-800 text-white flex items-center justify-center"><i class="fas fa-user-shield"></i></div>
                    <div><span class="font-heading font-semibold text-base text-slate-800 block leading-tight">Admin Console</span><span class="text-xs text-slate-400">จัดการระบบส่วนกลาง</span></div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="lg:hidden">@csrf<button type="submit" class="text-xs font-semibold text-rose-600 bg-rose-50 px-3 py-1.5 rounded-lg">ออก</button></form>
            </div>
            <nav class="p-3 lg:p-4 flex lg:flex-col gap-1 overflow-x-auto">
                <button onclick="switchAdmTab('bookings')" id="tab-bookings" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold bg-[#EBF3F1] text-[#2E6F65] whitespace-nowrap"><i class="fas fa-calendar-check w-4 text-center"></i><span>รายการจองทั้งหมด</span></button>
                <button onclick="switchAdmTab('rooms')" id="tab-rooms" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 whitespace-nowrap"><i class="fas fa-door-open w-4 text-center"></i><span>ห้องประชุม</span></button>
                <button onclick="switchAdmTab('vehicles')" id="tab-vehicles" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 whitespace-nowrap"><i class="fas fa-car w-4 text-center"></i><span>รถส่วนกลาง</span></button>
                <button onclick="switchAdmTab('agendas')" id="tab-agendas" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 whitespace-nowrap"><i class="fas fa-clipboard-list w-4 text-center"></i><span>วาระการประชุม</span></button>
                <button onclick="switchAdmTab('users')" id="tab-users" class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 whitespace-nowrap"><i class="fas fa-users w-4 text-center"></i><span>รายชื่อเจ้าหน้าที่</span></button>
            </nav>
        </div>
        <div class="hidden lg:block p-4 border-t border-[#F0F4F3] bg-[#FAFBFB]">
            <p class="text-sm font-semibold text-slate-800 mb-3 truncate">{{ auth()->user()->name }}</p>
            <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="w-full py-2 rounded-xl border border-slate-200 bg-white hover:bg-rose-50 hover:text-rose-600 text-xs font-semibold text-slate-600">ออกจากระบบ</button></form>
        </div>
    </aside>

    <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full space-y-6">
        @if(session('success'))<div class="p-4 rounded-2xl bg-[#ECFDF5] border border-[#A7F3D0] text-[#065F46] text-sm font-medium">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm font-medium">{{ session('error') }}</div>@endif

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-[#E3E8E6]"><p class="text-xs text-slate-400">รายการจองทั้งหมด</p><p class="text-2xl font-bold text-slate-800 mt-1">{{ $bookings->count() }}</p></div>
            <div class="bg-white p-5 rounded-2xl border border-[#E3E8E6]"><p class="text-xs text-slate-400">ห้องประชุม</p><p class="text-2xl font-bold text-emerald-700 mt-1">{{ $rooms->count() }}</p></div>
            <div class="bg-white p-5 rounded-2xl border border-[#E3E8E6]"><p class="text-xs text-slate-400">รถส่วนกลาง</p><p class="text-2xl font-bold text-amber-700 mt-1">{{ $vehicles->count() }}</p></div>
            <div class="bg-white p-5 rounded-2xl border border-[#E3E8E6]"><p class="text-xs text-slate-400">เจ้าหน้าที่ในระบบ</p><p class="text-2xl font-bold text-sky-700 mt-1">{{ $users->count() }}</p></div>
        </div>

        <div id="sec-bookings" class="space-y-4">
            <h2 class="text-lg font-semibold text-slate-800">จัดการคิวการจองทั้งหมด</h2>
            <div class="bg-white rounded-2xl border border-[#E3E8E6] overflow-x-auto">
                <table class="min-w-full divide-y divide-[#EAEFEF] text-sm">
                    <thead class="bg-[#F9FBFA] text-slate-600">
                        <tr><th class="px-4 py-3 text-left">รหัส</th><th class="px-4 py-3 text-left">ผู้จอง</th><th class="px-4 py-3 text-left">ประเภท</th><th class="px-4 py-3 text-left">รายการ</th><th class="px-4 py-3 text-left">วัน-เวลา</th><th class="px-4 py-3 text-left">สถานะ</th><th class="px-4 py-3 text-right">จัดการ</th></tr>
                    </thead>
                    <tbody class="divide-y divide-[#F1F5F4]">
                        @foreach($bookings as $b)
                            <tr>
                                <td class="px-4 py-3 font-mono text-xs">{{ $b->booking_code }}</td>
                                <td class="px-4 py-3 font-medium">{{ $b->booker_name }}</td>
                                <td class="px-4 py-3">{{ $b->booking_type }}</td>
                                <td class="px-4 py-3">{{ $b->item_detail }}</td>
                                <td class="px-4 py-3 text-xs text-slate-500">{{ \Carbon\Carbon::parse($b->start_datetime)->format('d/m/Y H:i') }} - {{ \Carbon\Carbon::parse($b->end_datetime)->format('H:i') }}</td>
                                <td class="px-4 py-3 font-semibold text-xs">{{ $b->status }}</td>
                                <td class="px-4 py-3 text-right space-x-2">
                                    <button type="button" onclick='openEditModal(@json($b))' class="text-xs font-semibold text-[#2E6F65] hover:underline">แก้ไข</button>
                                    <form method="POST" action="{{ route('admin.bookings.destroy', $b->id) }}" class="inline" onsubmit="return confirm('ยืนยันลบการจองนี้?')">@csrf @method('DELETE')<button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">ลบ</button></form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="sec-rooms" class="hidden space-y-4">
            <div class="flex justify-between"><h2 class="text-lg font-semibold">ฐานข้อมูลห้องประชุม</h2><button onclick="document.getElementById('modal-add-room').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-[#2E6F65] text-white text-sm font-semibold">+ เพิ่มห้องประชุม</button></div>
            <div class="bg-white rounded-2xl border border-[#E3E8E6] overflow-hidden">
                <table class="min-w-full divide-y divide-[#EAEFEF] text-sm">
                    <thead class="bg-[#F9FBFA]"><tr><th class="px-5 py-3 text-left">ชื่อห้อง</th><th class="px-5 py-3 text-left">ความจุ</th><th class="px-5 py-3 text-left">รูปแบบโต๊ะ</th><th class="px-5 py-3 text-right">จัดการ</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($rooms as $r)
                            <tr><td class="px-5 py-3 font-semibold">{{ $r->name }}</td><td class="px-5 py-3">{{ $r->capacity }} คน</td><td class="px-5 py-3">{{ $r->layouts }}</td><td class="px-5 py-3 text-right"><form method="POST" action="{{ route('admin.rooms.destroy', $r->id) }}">@csrf @method('DELETE')<button class="text-rose-600 text-xs font-semibold">ลบ</button></form></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="sec-vehicles" class="hidden space-y-4">
            <div class="flex justify-between"><h2 class="text-lg font-semibold">ฐานข้อมูลรถส่วนกลาง</h2><button onclick="document.getElementById('modal-add-vehicle').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-[#2E6F65] text-white text-sm font-semibold">+ เพิ่มรถส่วนกลาง</button></div>
            <div class="bg-white rounded-2xl border border-[#E3E8E6] overflow-hidden">
                <table class="min-w-full divide-y divide-[#EAEFEF] text-sm">
                    <thead class="bg-[#F9FBFA]"><tr><th class="px-5 py-3 text-left">ป้ายทะเบียน</th><th class="px-5 py-3 text-left">รุ่นรถ</th><th class="px-5 py-3 text-left">ที่นั่ง</th><th class="px-5 py-3 text-right">จัดการ</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($vehicles as $v)
                            <tr><td class="px-5 py-3 font-semibold">{{ $v->license_plate }}</td><td class="px-5 py-3">{{ $v->model }}</td><td class="px-5 py-3">{{ $v->seats }}</td><td class="px-5 py-3 text-right"><form method="POST" action="{{ route('admin.vehicles.destroy', $v->id) }}">@csrf @method('DELETE')<button class="text-rose-600 text-xs font-semibold">ลบ</button></form></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="sec-agendas" class="hidden space-y-4">
            <div class="flex justify-between"><h2 class="text-lg font-semibold">ฐานข้อมูลวาระการประชุม</h2><button onclick="document.getElementById('modal-add-agenda').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-[#2E6F65] text-white text-sm font-semibold">+ เพิ่มวาระใหม่</button></div>
            <div class="bg-white rounded-2xl border border-[#E3E8E6] overflow-hidden">
                <table class="min-w-full divide-y divide-[#EAEFEF] text-sm">
                    <thead class="bg-[#F9FBFA]"><tr><th class="px-5 py-3 text-left">หัวข้อวาระ</th><th class="px-5 py-3 text-left">ประเภท</th><th class="px-5 py-3 text-left">กองรับผิดชอบ</th><th class="px-5 py-3 text-right">จัดการ</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($agendas as $a)
                            <tr><td class="px-5 py-3 font-semibold">{{ $a->title }}</td><td class="px-5 py-3">{{ $a->meeting_type }}</td><td class="px-5 py-3">{{ $a->department }}</td><td class="px-5 py-3 text-right"><form method="POST" action="{{ route('admin.agendas.destroy', $a->id) }}">@csrf @method('DELETE')<button class="text-rose-600 text-xs font-semibold">ลบ</button></form></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div id="sec-users" class="hidden space-y-4">
            <div class="flex justify-between"><h2 class="text-lg font-semibold">ฐานข้อมูลผู้ใช้งาน</h2><button onclick="document.getElementById('modal-add-user').classList.remove('hidden')" class="px-4 py-2 rounded-xl bg-[#2E6F65] text-white text-sm font-semibold">+ เพิ่มเจ้าหน้าที่</button></div>
            <div class="bg-white rounded-2xl border border-[#E3E8E6] overflow-hidden">
                <table class="min-w-full divide-y divide-[#EAEFEF] text-sm">
                    <thead class="bg-[#F9FBFA]"><tr><th class="px-5 py-3 text-left">Username</th><th class="px-5 py-3 text-left">ชื่อ-นามสกุล</th><th class="px-5 py-3 text-left">สังกัด</th><th class="px-5 py-3 text-left">เบอร์โทร</th><th class="px-5 py-3 text-right">จัดการ</th></tr></thead>
                    <tbody class="divide-y">
                        @foreach($users as $u)
                            <tr><td class="px-5 py-3 font-mono text-xs">{{ $u->username }}</td><td class="px-5 py-3 font-medium">{{ $u->name }}</td><td class="px-5 py-3">{{ $u->department }}</td><td class="px-5 py-3">{{ $u->phone }}</td><td class="px-5 py-3 text-right"><form method="POST" action="{{ route('admin.users.destroy', $u->id) }}">@csrf @method('DELETE')<button class="text-rose-600 text-xs font-semibold">ลบ</button></form></td></tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modals -->
    <div id="edit-modal" class="fixed inset-0 bg-slate-900/40 hidden z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-lg p-6 space-y-4 text-sm">
            <h3 class="text-base font-semibold border-b pb-2">แก้ไขรายละเอียดการจอง</h3>
            <form id="edit-form" method="POST" class="space-y-3">@csrf @method('PUT')
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block font-semibold mb-1">ผู้จอง</label><input type="text" name="booker_name" id="ed-user" required class="w-full border rounded-xl px-3 py-2"></div>
                    <div><label class="block font-semibold mb-1">สถานะ</label><select name="status" id="ed-status" class="w-full border rounded-xl px-3 py-2"><option value="Approved">Approved</option><option value="Pending">Pending</option><option value="Rejected">Rejected</option><option value="Cancelled">Cancelled</option></select></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block font-semibold mb-1">ประเภท</label><input type="text" name="booking_type" id="ed-type" required class="w-full border rounded-xl px-3 py-2"></div>
                    <div><label class="block font-semibold mb-1">รายการ</label><input type="text" name="item_detail" id="ed-item" required class="w-full border rounded-xl px-3 py-2"></div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="block font-semibold mb-1">เริ่ม</label><input type="datetime-local" name="start_datetime" id="ed-start" required class="w-full border rounded-xl px-3 py-2"></div>
                    <div><label class="block font-semibold mb-1">สิ้นสุด</label><input type="datetime-local" name="end_datetime" id="ed-end" required class="w-full border rounded-xl px-3 py-2"></div>
                </div>
                <div><label class="block font-semibold mb-1">หมายเหตุ</label><input type="text" name="remarks" id="ed-remarks" class="w-full border rounded-xl px-3 py-2"></div>
                <div class="flex justify-end gap-2 pt-2"><button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 rounded-xl">ปิด</button><button type="submit" class="px-5 py-2 bg-[#2E6F65] text-white rounded-xl font-semibold">บันทึก</button></div>
            </form>
        </div>
    </div>

    <div id="modal-add-room" class="fixed inset-0 bg-slate-900/40 hidden z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md p-6 space-y-3 text-sm">
            <h3 class="font-semibold border-b pb-2">เพิ่มห้องประชุมใหม่</h3>
            <form method="POST" action="{{ route('admin.rooms.store') }}" class="space-y-3">@csrf
                <input type="text" name="name" required placeholder="ชื่อห้องประชุม" class="w-full border rounded-xl px-3 py-2">
                <input type="number" name="capacity" required placeholder="ความจุ (คน)" class="w-full border rounded-xl px-3 py-2">
                <input type="text" name="layouts" required placeholder="รูปแบบการจัดโต๊ะ" class="w-full border rounded-xl px-3 py-2">
                <div class="flex justify-end gap-2"><button type="button" onclick="document.getElementById('modal-add-room').classList.add('hidden')" class="px-4 py-2 bg-slate-100 rounded-xl">ยกเลิก</button><button type="submit" class="px-5 py-2 bg-[#2E6F65] text-white rounded-xl font-semibold">บันทึก</button></div>
            </form>
        </div>
    </div>

    <div id="modal-add-vehicle" class="fixed inset-0 bg-slate-900/40 hidden z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md p-6 space-y-3 text-sm">
            <h3 class="font-semibold border-b pb-2">เพิ่มรถส่วนกลางใหม่</h3>
            <form method="POST" action="{{ route('admin.vehicles.store') }}" class="space-y-3">@csrf
                <input type="text" name="license_plate" required placeholder="ป้ายทะเบียน" class="w-full border rounded-xl px-3 py-2">
                <input type="text" name="model" required placeholder="ยี่ห้อ / รุ่น" class="w-full border rounded-xl px-3 py-2">
                <input type="number" name="seats" required placeholder="จำนวนที่นั่ง" class="w-full border rounded-xl px-3 py-2">
                <div class="flex justify-end gap-2"><button type="button" onclick="document.getElementById('modal-add-vehicle').classList.add('hidden')" class="px-4 py-2 bg-slate-100 rounded-xl">ยกเลิก</button><button type="submit" class="px-5 py-2 bg-[#2E6F65] text-white rounded-xl font-semibold">บันทึก</button></div>
            </form>
        </div>
    </div>

    <div id="modal-add-agenda" class="fixed inset-0 bg-slate-900/40 hidden z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md p-6 space-y-3 text-sm">
            <h3 class="font-semibold border-b pb-2">เพิ่มวาระการประชุมใหม่</h3>
            <form method="POST" action="{{ route('admin.agendas.store') }}" class="space-y-3">@csrf
                <input type="text" name="title" required placeholder="หัวข้อวาระ" class="w-full border rounded-xl px-3 py-2">
                <input type="text" name="meeting_type" required placeholder="ประเภทการประชุม" class="w-full border rounded-xl px-3 py-2">
                <input type="text" name="department" required placeholder="กองที่รับผิดชอบ" class="w-full border rounded-xl px-3 py-2">
                <div class="flex justify-end gap-2"><button type="button" onclick="document.getElementById('modal-add-agenda').classList.add('hidden')" class="px-4 py-2 bg-slate-100 rounded-xl">ยกเลิก</button><button type="submit" class="px-5 py-2 bg-[#2E6F65] text-white rounded-xl font-semibold">บันทึก</button></div>
            </form>
        </div>
    </div>

    <div id="modal-add-user" class="fixed inset-0 bg-slate-900/40 hidden z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-3xl w-full max-w-md p-6 space-y-3 text-sm">
            <h3 class="font-semibold border-b pb-2">เพิ่มบัญชีเจ้าหน้าที่ใหม่</h3>
            <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-3">@csrf
                <input type="text" name="username" required placeholder="Username" class="w-full border rounded-xl px-3 py-2">
                <input type="text" name="password" required placeholder="Password" class="w-full border rounded-xl px-3 py-2">
                <input type="text" name="name" required placeholder="ชื่อ-นามสกุล" class="w-full border rounded-xl px-3 py-2">
                <input type="text" name="department" required placeholder="สังกัด/กอง" class="w-full border rounded-xl px-3 py-2">
                <input type="text" name="phone" placeholder="เบอร์โทร" class="w-full border rounded-xl px-3 py-2">
                <div class="flex justify-end gap-2"><button type="button" onclick="document.getElementById('modal-add-user').classList.add('hidden')" class="px-4 py-2 bg-slate-100 rounded-xl">ยกเลิก</button><button type="submit" class="px-5 py-2 bg-[#2E6F65] text-white rounded-xl font-semibold">บันทึก</button></div>
            </form>
        </div>
    </div>

    <script>
        function switchAdmTab(tab) {
            ['bookings', 'rooms', 'vehicles', 'agendas', 'users'].forEach(t => {
                document.getElementById('sec-' + t).classList.add('hidden');
                document.getElementById('tab-' + t).className = 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 hover:bg-slate-50 whitespace-nowrap';
            });
            document.getElementById('sec-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).className = 'flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold bg-[#EBF3F1] text-[#2E6F65] whitespace-nowrap';
        }
        function openEditModal(b) {
            document.getElementById('edit-form').action = '/admin/bookings/' + b.id;
            document.getElementById('ed-user').value = b.booker_name;
            document.getElementById('ed-status').value = b.status;
            document.getElementById('ed-type').value = b.booking_type;
            document.getElementById('ed-item').value = b.item_detail;
            document.getElementById('ed-start').value = b.start_datetime.replace(' ', 'T').slice(0, 16);
            document.getElementById('ed-end').value = b.end_datetime.replace(' ', 'T').slice(0, 16);
            document.getElementById('ed-remarks').value = b.remarks || '';
            document.getElementById('edit-modal').classList.remove('hidden');
        }
    </script>
</body>
</html>