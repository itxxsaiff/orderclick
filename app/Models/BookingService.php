<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    use \App\Models\Concerns\Translatable;

    public const TRANSLATABLE = ['name', 'category', 'description'];

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
