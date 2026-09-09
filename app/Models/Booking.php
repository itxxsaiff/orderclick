<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'vendor_id', 'booking_number', 'service_id', 'service_name', 'amount',
        'payment_type', 'payment_method', 'payment_status', 'staff',
        'customer_name', 'mobile', 'email', 'booking_date', 'booking_time',
        'notes', 'status', 'is_notification',
    ];

    public const STATUS = [
        1 => 'Pending',
        2 => 'Confirmed',
        3 => 'Completed',
        4 => 'Cancelled',
    ];
}
