<?php

namespace App\Models;

use App\Helpers\Systems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    use HasFactory;

    protected $table = 'tax';

    public const APPLIES_SUBSCRIPTION = 'subscription';
    public const APPLIES_VENDOR_SALES = 'vendor_sales';

    public const TYPE_FIXED = 1;
    public const TYPE_PERCENTAGE = 2;

    /** What a rule can be attached to. */
    public static function appliesToOptions(): array
    {
        return [
            self::APPLIES_SUBSCRIPTION => 'Platform Subscription Invoices',
            self::APPLIES_VENDOR_SALES => 'Vendor Customer Sales',
        ];
    }

    /** Which systems a rule covers. */
    public static function systemOptions(): array
    {
        $out = ['all' => 'All Order Click Systems'];
        foreach (Systems::all() as $s) {
            $out[$s['key']] = $s['name'];
        }

        return $out;
    }

    public static function priceTypeOptions(): array
    {
        return [
            'inclusive' => 'Tax Inclusive',
            'exclusive' => 'Tax Exclusive',
        ];
    }

    /**
     * Active platform tax rules that apply to a subscription invoice for one system.
     *
     * Returns nothing while every rule is inactive — which is the state the client asked us to
     * ship in, so no tax is calculated or charged until they confirm the registered rate.
     */
    public static function forSubscription(?string $system)
    {
        $system = Systems::normalise($system);

        return static::where('applies_to', self::APPLIES_SUBSCRIPTION)
            ->where('is_available', 1)
            ->where('is_deleted', 2)
            ->where('tax', '>', 0)
            ->where(function ($q) use ($system) {
                $q->where('systems', 'all')->orWhere('systems', $system);
            })
            ->orderBy('reorder_id')
            ->get();
    }

    /** The tax amount this rule produces on a given net amount. */
    public function amountOn(float $base): float
    {
        return (int) $this->type === self::TYPE_PERCENTAGE
            ? ((float) $this->tax / 100) * $base
            : (float) $this->tax;
    }

    public function isInclusive(): bool
    {
        return $this->price_type === 'inclusive';
    }
}
