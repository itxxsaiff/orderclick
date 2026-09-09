<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    use HasFactory;
    protected $table = 'plans';

    // New structured fields are stored as JSON — cast so controllers/views get arrays.
    protected $casts = [
        'plan_limits' => 'array',
        'plan_offer' => 'array',
        'plan_addons' => 'array',
        'plan_extra_features' => 'array',
    ];

    /** Currency symbol for this plan's currency code (falls back to the code). */
    public function curSymbol(): string
    {
        $m = ['USD' => '$', 'EUR' => '€', 'GBP' => '£', 'BHD' => 'BD', 'SAR' => 'SR', 'AED' => 'AED', 'JOD' => 'JD', 'KWD' => 'KD', 'QAR' => 'QR', 'OMR' => 'OMR', 'EGP' => 'EGP'];
        return $m[$this->currency] ?? ($this->currency ?: '$');
    }

    /** Format an amount in this plan's currency. */
    public function priceFmt($amount = null): string
    {
        $amount = $amount === null ? $this->price : $amount;
        return $this->curSymbol() . ' ' . number_format((float) $amount, 2);
    }

    /** none | scheduled | active | expired — based on the offer toggle + start/end dates. */
    public function offerStatus(): string
    {
        $o = $this->plan_offer ?? [];
        if (empty($o['enabled'])) {
            return 'none';
        }
        try {
            $now = now();
            $start = !empty($o['starts_at']) ? \Carbon\Carbon::parse($o['starts_at']) : null;
            $end = !empty($o['ends_at']) ? \Carbon\Carbon::parse($o['ends_at']) : null;
            if ($start && $now->lt($start)) {
                return 'scheduled';
            }
            if ($end && $now->gt($end)) {
                return 'expired';
            }
        } catch (\Throwable $e) {
            // bad dates → treat as always-on while enabled
        }
        return 'active';
    }

    /** The price a buyer pays right now (regular price unless an offer is active). */
    public function effectivePrice(): float
    {
        $p = (float) $this->price;
        if ($this->offerStatus() !== 'active') {
            return $p;
        }
        $o = $this->plan_offer;
        $type = $o['type'] ?? '';
        if ($type === 'percentage' && !empty($o['discount_percentage'])) {
            return max(0, round($p - $p * ((float) $o['discount_percentage'] / 100), 2));
        }
        if ($type === 'fixed' && isset($o['offer_amount']) && $o['offer_amount'] !== '') {
            return max(0, (float) $o['offer_amount']);
        }
        return $p; // free_duration / pay_x_get_y keep the price but change the duration/bonus
    }

    /** Short human label for an active offer (else null). */
    public function offerLabel(): ?string
    {
        if ($this->offerStatus() !== 'active') {
            return null;
        }
        $o = $this->plan_offer;
        switch ($o['type'] ?? '') {
            case 'percentage':
                return round((float) ($o['discount_percentage'] ?? 0)) . '% OFF';
            case 'fixed':
                return 'Special Price';
            case 'free_duration':
                return '+' . ($o['free_duration'] ?? '') . ' free';
            case 'pay_x_get_y':
                return 'Pay ' . ($o['paid_months'] ?? '') . ' Get ' . ($o['free_months'] ?? '') . ' Free';
        }
        return null;
    }
}
