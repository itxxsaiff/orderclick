<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $table = 'service_requests';

    protected $fillable = [
        'vendor_id',
        'request_number',
        'service_id',
        'service_name',
        'customer_name',
        'mobile',
        'email',
        'address',
        'preferred_date',
        'preferred_time',
        'notes',
        'status',
        'is_notification',
    ];

    const STATUS = [
        1 => 'Pending',
        2 => 'Accepted',
        3 => 'In Progress',
        4 => 'Completed',
        5 => 'Cancelled',
    ];
}
