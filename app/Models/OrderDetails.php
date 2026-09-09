<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    use HasFactory;
    protected $table = 'order_details';
    
    protected $fillable = [
        'order_id',
        'item_id',
        'item_name',
        'item_image',
        'extras_id',
        'extras_name',
        'extras_price',
        'price',
        'variants_id',
        'variants_name',
        'variants_price',
        'qty'
    ];

}
