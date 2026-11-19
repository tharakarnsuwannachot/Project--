<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Room;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'ผู้ดูแลระบบกลาง',
                'email' => 'admin@municipality.local',
                'department' => 'ศูนย์เทคโนโลยีสารสนเทศ',
                'phone' => '055-000-000',
                'role' => 'admin',
                'password' => Hash::make('admin1234'),
            ]
        );

        User::updateOrCreate(
            ['username' => 'somchai'],
            [
                'name' => 'นายสมชาย ใจดี',
                'email' => 'somchai@municipality.local',
                'department' => 'กองช่าง',
                'phone' => '081-111-1111',
                'role' => 'user',
                'password' => Hash::make('1234'),
            ]
        );

        User::updateOrCreate(
            ['username' => 'somsri'],
            [
                'name' => 'นางสาวสมศรี มีสุข',
                'email' => 'somsri@municipality.local',
                'department' => 'สำนักปลัด',
                'phone' => '082-222-2222',
                'role' => 'user',
                'password' => Hash::make('1234'),
            ]
        );

        if (Room::count() === 0) {
            Room::insert([
                ['name' => 'ห้องประชุมสภาเทศบาล (ชั้น 3)', 'capacity' => 100, 'layouts' => 'แบบตัวยู (U-Shape), แบบชั้นเรียน', 'status' => 'เปิดใช้งาน', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'ห้องประชุม 1 (ชั้น 2)', 'capacity' => 30, 'layouts' => 'แบบบอร์ดรูม (โต๊ะยาว)', 'status' => 'เปิดใช้งาน', 'created_at' => now(), 'updated_at' => now()],
                ['name' => 'ห้องประชุมเล็ก สำนักปลัด (ชั้น 1)', 'capacity' => 15, 'layouts' => 'แบบบอร์ดรูม (โต๊ะยาว)', 'status' => 'เปิดใช้งาน', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (Vehicle::count() === 0) {
            Vehicle::insert([
                ['license_plate' => 'กค 1234 พิษณุโลก', 'model' => 'กระบะ 4 ประตู (Toyota Revo)', 'seats' => 5, 'status' => 'พร้อมใช้งาน', 'created_at' => now(), 'updated_at' => now()],
                ['license_plate' => 'นข 9999 พิษณุโลก', 'model' => 'รถตู้ VIP (Toyota Commuter)', 'seats' => 11, 'status' => 'พร้อมใช้งาน', 'created_at' => now(), 'updated_at' => now()],
                ['license_plate' => 'กท 5678 พิษณุโลก', 'model' => 'รถเก๋งส่วนกลาง (Honda Civic)', 'seats' => 4, 'status' => 'พร้อมใช้งาน', 'created_at' => now(), 'updated_at' => now()],
            ]);
        }

        if (Agenda::count() === 0) {
            Agenda::insert([
                ['title' => 'พิจารณางบประมาณประจำปี', 'meeting_type' => 'ประชุมสภาเทศบาลสามัญ', 'department' => 'กองคลัง', 'target_date' => now()->addDays(7)->toDateString(), 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'ติดตามโครงการปรับปรุงภูมิทัศน์', 'meeting_type' => 'ประชุมหัวหน้าส่วนราชการ', 'department' => 'กองช่าง', 'target_date' => now()->addDays(10)->toDateString(), 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}