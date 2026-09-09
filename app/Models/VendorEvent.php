<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorEvent extends Model
{
    use HasFactory;

    protected $table = 'vendor_events';
    protected $guarded = [];
    protected $casts = ['meta' => 'array'];
}
