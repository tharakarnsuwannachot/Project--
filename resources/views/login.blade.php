<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - ระบบจองส่วนกลาง สำนักงานเทศบาล</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@500;600&family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Sarabun', sans-serif; background-color: #F2F5F4; }
        h1, h2, .font-heading { font-family: 'Prompt', sans-serif; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 text-slate-700">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-[0_10px_40px_rgba(46,111,101,0.08)] border border-[#E3E8E6] overflow-hidden">
        <div class="px-8 pt-8 pb-6 text-center bg-gradient-to-b from-[#EBF3F1] to-white">
            <div class="w-14 h-14 bg-[#2E6F65] text-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                <i class="fas fa-landmark text-2xl"></i>
            </div>
            <h1 class="text-xl font-semibold text-slate-800">ระบบจองทรัพยากรส่วนกลาง</h1>
            <p class="text-sm text-slate-500 mt-1">สำนักงานเทศบาล</p>
        </div>

        <div class="px-8 pb-8">
            <div class="grid grid-cols-2 p-1 rounded-2xl bg-[#F2F5F4] border border-[#E3E8E6] mb-6">
                <button type="button" onclick="selectRole('user')" id="tab-user" class="py-2.5 rounded-xl text-sm font-semibold bg-white text-[#2E6F65] shadow-sm transition-all">
                    <i class="fas fa-user mr-1.5"></i> เจ้าหน้าที่
                </button>
                <button type="button" onclick="selectRole('admin')" id="tab-admin" class="py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-slate-700 transition-all">
                    <i class="fas fa-user-shield mr-1.5"></i> ผู้ดูแลระบบ
                </button>
            </div>

            <div id="role-banner" class="mb-5 p-3.5 rounded-2xl bg-[#F7FAF9] border border-[#DCE8E5] text-xs text-slate-600 leading-relaxed">
                <span class="font-semibold text-[#2E6F65] block mb-0.5"><i class="fas fa-info-circle mr-1"></i> โหมดเจ้าหน้าที่ทั่วไป</span>
                เข้าใช้งานเพื่อจองห้องประชุม รถส่วนกลาง วาระการประชุม และแก้ไขโปรไฟล์ส่วนตัว
            </div>

            @if($errors->any())
                <div class="mb-4 p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-medium flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-sm"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4 text-sm">
                @csrf
                <input type="hidden" name="role" id="role-input" value="user">

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">ชื่อผู้ใช้งาน (Username)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fas fa-user text-xs"></i></span>
                        <input type="text" name="username" id="input-username" value="{{ old('username') }}" required placeholder="ระบุชื่อผู้ใช้งาน"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-[#FAFBFB] focus:bg-white focus:ring-2 focus:ring-[#2E6F65]/20 focus:border-[#2E6F65] outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">รหัสผ่าน (Password)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400"><i class="fas fa-lock text-xs"></i></span>
                        <input type="password" name="password" id="input-password" required placeholder="••••••••"
                            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 bg-[#FAFBFB] focus:bg-white focus:ring-2 focus:ring-[#2E6F65]/20 focus:border-[#2E6F65] outline-none transition-all">
                    </div>
                </div>

                <button type="submit" id="btn-submit" class="w-full py-3 mt-2 rounded-xl bg-[#2E6F65] hover:bg-[#245850] text-white font-semibold shadow-sm transition-all">
                    เข้าสู่ระบบเจ้าหน้าที่
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span id="hint-text">บัญชีทดสอบ: <strong class="text-slate-700">somchai</strong> / <strong class="text-slate-700">1234</strong></span>
                <button type="button" onclick="fillDemo()" class="text-[#2E6F65] hover:underline font-semibold">กรอกอัตโนมัติ</button>
            </div>
        </div>
    </div>

    <script>
        let currentRole = 'user';
        function selectRole(role) {
            currentRole = role;
            document.getElementById('role-input').value = role;
            const uTab = document.getElementById('tab-user');
            const aTab = document.getElementById('tab-admin');
            const banner = document.getElementById('role-banner');
            const btn = document.getElementById('btn-submit');
            const hint = document.getElementById('hint-text');

            if (role === 'user') {
                uTab.className = 'py-2.5 rounded-xl text-sm font-semibold bg-white text-[#2E6F65] shadow-sm transition-all';
                aTab.className = 'py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-slate-700 transition-all';
                banner.innerHTML = '<span class="font-semibold text-[#2E6F65] block mb-0.5"><i class="fas fa-info-circle mr-1"></i> โหมดเจ้าหน้าที่ทั่วไป</span>เข้าใช้งานเพื่อจองห้องประชุม รถส่วนกลาง วาระการประชุม และแก้ไขโปรไฟล์ส่วนตัว';
                btn.className = 'w-full py-3 mt-2 rounded-xl bg-[#2E6F65] hover:bg-[#245850] text-white font-semibold shadow-sm transition-all';
                btn.innerText = 'เข้าสู่ระบบเจ้าหน้าที่';
                hint.innerHTML = 'บัญชีทดสอบ: <strong class="text-slate-700">somchai</strong> / <strong class="text-slate-700">1234</strong>';
            } else {
                aTab.className = 'py-2.5 rounded-xl text-sm font-semibold bg-white text-slate-800 shadow-sm transition-all';
                uTab.className = 'py-2.5 rounded-xl text-sm font-medium text-slate-500 hover:text-slate-700 transition-all';
                banner.innerHTML = '<span class="font-semibold text-slate-800 block mb-0.5"><i class="fas fa-shield-alt mr-1"></i> โหมดผู้ดูแลระบบ (Admin)</span>จัดการอนุมัติ/แก้ไขคิวจอง และเพิ่มข้อมูลห้องประชุม ป้ายทะเบียนรถ วาระ และพนักงาน';
                btn.className = 'w-full py-3 mt-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-semibold shadow-sm transition-all';
                btn.innerText = 'เข้าสู่ระบบผู้ดูแลระบบ';
                hint.innerHTML = 'บัญชีทดสอบ: <strong class="text-slate-700">admin</strong> / <strong class="text-slate-700">admin1234</strong>';
            }
        }
        function fillDemo() {
            document.getElementById('input-username').value = currentRole === 'user' ? 'somchai' : 'admin';
            document.getElementById('input-password').value = currentRole === 'user' ? '1234' : 'admin1234';
        }
    </script>
</body>
</html>