<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorWhatsappNumber extends Model
{
    use HasFactory;

    protected $table = 'vendor_whatsapp_numbers';
    protected $guarded = [];
    protected $casts = ['days' => 'array'];

    public function branch()
    {
        return $this->belongsTo(VendorBranch::class, 'branch_id');
    }
}
