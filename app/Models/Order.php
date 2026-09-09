<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'orders';
    protected $fillable = [
        'vendor_id',
        'user_id',
        'order_number_digit',
        'order_number_start',
        'order_number',
        'payment_type',
        'payment_id',
        'sub_total',
        'tax',
        'tax_name',
        'grand_total',
        'tips',
        'order_type',
        'table_id',
        'address',
        'pincode',
        'building',
        'landmark',
        'delivery_area',
        'delivery_charge',
        'discount_amount',
        'offer_type',
        'couponcode',
        'order_notes',
        'customer_name',
        'customer_email',
        'mobile',
        'delivery_date',
        'delivery_time',
        'order_from',
        'status',
        'status_type',
        'payment_status',
        'is_notification',
        'loyalty_amount',
        'screenshot'
    ];

    public function vendorinfo()
    {
        return $this->hasOne('App\Models\User', 'id', 'vendor_id')->select('id', 'name');
    }

    public function tableqr()
    {
        return $this->hasOne('App\Models\TableQR', 'id', 'table_id');
    }
}
