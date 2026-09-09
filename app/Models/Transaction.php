<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transactions';
    protected $casts = [
        'addons' => 'array',
        // What the vendor actually bought, frozen at purchase time — plans get edited later.
        'plan_snapshot' => 'array',
    ];

    public function vendor_info(){
        return $this->hasOne('App\Models\User','id','vendor_id')->select('id','name','email','mobile');
    }
    public function plan_info()
    {
        return $this->hasOne('App\Models\PricingPlan','id','plan_id')->select('id','name','description','features','system','currency');
    }

    /**
     * Plan details as they were when this transaction was paid. Falls back to the live plan for
     * rows created before snapshots existed.
     */
    public function planDetails(): array
    {
        if (!empty($this->plan_snapshot)) {
            return $this->plan_snapshot;
        }

        return [
            'name'     => $this->plan_name,
            'system'   => $this->system,
            'price'    => (float) $this->amount,
            'currency' => $this->currency ?: 'USD',
            'duration' => $this->duration,
            'days'     => $this->days,
        ];
    }

}
