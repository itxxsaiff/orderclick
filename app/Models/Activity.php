<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';
    protected $guarded = [];

    public function specializations()
    {
        return $this->hasMany(Specialization::class, 'activity_id')->orderBy('reorder_id');
    }

    /** Localised name — Arabic when the store is running in Arabic and a translation exists. */
    public function getDisplayNameAttribute(): string
    {
        return app()->getLocale() === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }
}
