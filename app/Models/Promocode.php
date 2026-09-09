<?php

namespace App\Models;

use App\Helpers\Systems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A coupon CODE. Automatic offers live on the plan (plans.plan_offer) and are deliberately not
 * modelled here — the two must not be duplicated.
 */
class Promocode extends Model
{
    use HasFactory;

    protected $table = 'promocodes';

    public const TYPE_FIXED = 1;
    public const TYPE_PERCENTAGE = 2;

    public const USAGE_ONCE_PER_CUSTOMER = 1;
    public const USAGE_MULTIPLE = 2;

    /** Plan ids this coupon is limited to. Empty means every plan in the chosen system. */
    public function planIds(): array
    {
        return array_values(array_filter(explode('|', (string) $this->applicable_plans)));
    }

    public function systemLabel(): string
    {
        return ($this->applicable_system ?: 'all') === 'all'
            ? 'All systems'
            : Systems::label($this->applicable_system);
    }

    /** How many times this code has been redeemed on settled subscription payments. */
    public function timesUsed(?int $vendorId = null): int
    {
        $q = Transaction::where('offer_code', $this->offer_code)->whereNull('transaction_type');
        if ($vendorId) {
            $q->where('vendor_id', $vendorId);
        }

        return $q->count();
    }

    /**
     * Validate this coupon against a subscription purchase.
     *
     * Checks, in order: status, date window, system, plan, minimum amount, per-customer usage and
     * the total usage cap. Returns null when the coupon is valid, or a translated error message.
     */
    public function validateForPlan(?PricingPlan $plan, float $subtotal, ?int $vendorId): ?string
    {
        if ((int) $this->is_available !== 1) {
            return trans('messages.invalid_promocode');
        }

        $today = date('Y-m-d');
        if ($this->start_date && $today < $this->start_date) {
            return trans('messages.coupon_not_started');
        }
        if ($this->exp_date && $today > $this->exp_date) {
            return trans('messages.coupon_expired');
        }

        if ($plan) {
            $scope = $this->applicable_system ?: 'all';
            if ($scope !== 'all' && Systems::normalise($plan->system) !== $scope) {
                return trans('messages.coupon_wrong_system');
            }

            $planIds = $this->planIds();
            if (!empty($planIds) && !in_array((string) $plan->id, $planIds, true)) {
                return trans('messages.coupon_wrong_plan');
            }
        }

        if ($subtotal < (float) $this->min_amount) {
            $min = trim(strip_tags(\App\Helpers\helper::currency_formate($this->min_amount, '')));
            return trans('messages.order_amount_greater_then') . ' ' . $min;
        }

        // Once per customer.
        if ((int) $this->usage_type === self::USAGE_ONCE_PER_CUSTOMER && $vendorId) {
            $limit = max(1, (int) $this->usage_limit);
            if ($this->timesUsed($vendorId) >= $limit) {
                return trans('messages.usage_limit_exceeded');
            }
        }

        // Total redemptions across everyone.
        if ((int) $this->max_total_uses > 0 && $this->timesUsed() >= (int) $this->max_total_uses) {
            return trans('messages.coupon_fully_redeemed');
        }

        return null;
    }

    /** Discount this coupon produces on a given subtotal, never more than the subtotal itself. */
    public function discountOn(float $subtotal): float
    {
        $amount = (int) $this->offer_type === self::TYPE_PERCENTAGE
            ? $subtotal * (float) $this->offer_amount / 100
            : (float) $this->offer_amount;

        return round(min($amount, $subtotal), 2);
    }
}
