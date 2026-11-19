<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_code', 'user_id', 'booker_name', 'booking_type',
        'item_detail', 'start_datetime', 'end_datetime', 'status',
        'calendar_event_id', 'remarks',
    ];
}