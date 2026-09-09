<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorAddon extends Model
{
    use HasFactory;

    protected $table = 'vendor_addons';
    protected $guarded = [];
}
