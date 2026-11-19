<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->string('department')->default('-')->after('name');
            $table->string('phone')->default('-')->after('department');
            $table->string('role')->default('user')->after('phone');
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('capacity')->default(20);
            $table->string('layouts')->default('แบบมาตรฐาน');
            $table->string('status')->default('เปิดใช้งาน');
            $table->timestamps();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate');
            $table->string('model');
            $table->integer('seats')->default(4);
            $table->string('status')->default('พร้อมใช้งาน');
            $table->timestamps();
        });

        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('meeting_type');
            $table->string('department');
            $table->date('target_date')->nullable();
            $table->timestamps();
        });

        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('booker_name');
            $table->string('booking_type');
            $table->string('item_detail');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('status')->default('Approved');
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