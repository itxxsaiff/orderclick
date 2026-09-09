<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Testimonials extends Model
{
    use HasFactory;
    protected $table = 'testimonials';

    public function user_info()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id')->select('id', 'name', 'email', 'mobile', 'token', 'image', DB::raw("CONCAT('" . url(env('ASSETSPATHURL') . 'admin-assets/images/profile/') . "/', users.image) AS profile_image"));
    }
    public function item_info()
    {
        return $this->hasOne('App\Models\Item', 'id', 'item_id')->select('id', 'item_name');
    }
}
