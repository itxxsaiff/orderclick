<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    protected $table = 'booking_services';

    protected $fillable = [
        'vendor_id',
        'name',
        'category',
        'price',
        'duration',
        'description',
        'image',
        'is_available',
        'reorder_id',
    ];
}
