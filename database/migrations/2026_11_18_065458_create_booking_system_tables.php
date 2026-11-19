<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ปรับตาราง users เดิมให้รองรับ username, สังกัด, เบอร์โทร และ role (user/admin)
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->string('department')->default('-')->after('name');
            $table->string('phone')->default('-')->after('department');
            $table->string('role')->default('user')->after('phone'); // 'user' หรือ 'admin'
        });

        // ตารางห้องประชุม
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('capacity')->default(20);
            $table->string('layouts')->default('แบบมาตรฐาน');
            $table->string('status')->default('เปิดใช้งาน');
            $table->timestamps();
        });

        // ตารางรถส่วนกลางและป้ายทะเบียน
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate');
            $table->string('model');
            $table->integer('seats')->default(4);
            $table->string('status')->default('พร้อมใช้งาน');
            $table->timestamps();
        });

        // ตารางวาระการประชุม
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('meeting_type');
            $table->string('department');
            $table->date('target_date')->nullable();
            $table->timestamps();
        });

        // ตารางรายการจองทั้งหมด
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('booker_name');
            $table->string('booking_type'); // ห้องประชุม, รถส่วนกลาง, วาระ
            $table->string('item_detail');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('status')->default('Approved'); // Approved, Pending, Rejected, Cancelled
            $table->string('calendar_event_id')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('agendas');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('rooms');
    }
};