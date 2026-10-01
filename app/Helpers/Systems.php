<?php

namespace App\Helpers;

use App\Models\Activity;
use App\Models\PricingPlan;
use App\Models\Settings;
use App\Models\Specialization;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * The V2 "System" layer: Orders & Stores, Booking, Service Marketplace.
 *
 * Deliberately a new class rather than more static methods on helper.php — that file is already
 * 1,800 lines and every request loads it. Everything about the System -> Activity -> Specialization
 * hierarchy and the account lifecycle lives here so there is one place to change it.
 */
class Systems
{
    public const ORDERS  = 'orders';
    public const BOOKING = 'booking';
    public const SERVICE = 'service';

    /** Account lifecycle states, in the order an account moves through them. */
    public const PENDING_PAYMENT       = 'pending_payment';
    public const PAID_SETUP_INCOMPLETE = 'paid_setup_incomplete';
    public const PROVISIONALLY_ACTIVE  = 'provisionally_active';
    public const CORRECTION_REQUIRED   = 'correction_required';
    public const VERIFIED_ACTIVE       = 'verified_active';
    public const RESTRICTED            = 'restricted';

    /** The three systems. Fixed by the client — a fourth must not be added. */
    public static function all(): array
    {
        return [
            self::ORDERS => [
                'key'   => self::ORDERS,
                'name'  => trans('labels.system_orders_stores'),
                'desc'  => trans('labels.system_orders_stores_desc'),
                'icon'  => '🛍️',
            ],
            self::BOOKING => [
                'key'   => self::BOOKING,
                'name'  => trans('labels.system_booking'),
                'desc'  => trans('labels.system_booking_desc'),
                'icon'  => '📅',
            ],
            self::SERVICE => [
                'key'   => self::SERVICE,
                'name'  => trans('labels.system_service_marketplace'),
                'desc'  => trans('labels.system_service_marketplace_desc'),
                'icon'  => '🧰',
            ],
        ];
    }

    public static function keys(): array
    {
        return array_keys(self::all());
    }

    public static function isValid(?string $system): bool
    {
        return $system !== null && in_array($system, self::keys(), true);
    }

    /** Normalise anything into one of the three systems, defaulting to Orders & Stores. */
    public static function normalise(?string $system): string
    {
        return self::isValid($system) ? $system : self::ORDERS;
    }

    /**
     * What a plan's two count limits are called in each system. A Booking plan must read
     * "20 Services / 200 Bookings", not the Orders & Stores wording, and the same strings are
     * used on the plan cards, the checkout summary, the plan details page and the PDF.
     */
    public const ENTITY_LABELS = [
        'orders' => [
            'primary'   => ['one' => 'entity_product', 'many' => 'entity_products'],
            'secondary' => ['one' => 'entity_order', 'many' => 'entity_orders'],
        ],
        'booking' => [
            'primary'   => ['one' => 'entity_service', 'many' => 'entity_services'],
            'secondary' => ['one' => 'entity_booking', 'many' => 'entity_bookings'],
        ],
        'service' => [
            'primary'   => ['one' => 'entity_service_listing', 'many' => 'entity_service_listings'],
            'secondary' => ['one' => 'entity_service_request', 'many' => 'entity_service_requests'],
        ],
    ];

    /** $which: 'primary' (products/services/listings) or 'secondary' (orders/bookings/requests). */
    public static function entityLabel(?string $system, string $which = 'primary', $count = 2): string
    {
        $set = self::ENTITY_LABELS[self::normalise($system)][$which] ?? self::ENTITY_LABELS['orders'][$which];
        $form = ((int) $count === 1) ? 'one' : 'many';   // -1 = unlimited reads as plural

        return trans('labels.' . $set[$form]);
    }

    public static function label(?string $system): string
    {
        $s = self::all()[self::normalise($system)];

        return $s['name'];
    }

    /**
     * The legacy business types that belong to each system. Registration still asks for one on
     * its own step, so this is the server-side guard that a merchant cannot post, say, 'clinic'
     * against the Orders & Stores system.
     */
    public static function businessTypes(?string $system): array
    {
        $map = [
            self::ORDERS  => ['food', 'grocery', 'pharmacy', 'retail'],
            self::BOOKING => ['clinic', 'salon', 'booking'],
            self::SERVICE => ['service'],
        ];

        return $map[self::normalise($system)];
    }

    /** Activities belonging to one system. */
    public static function activities(?string $system)
    {
        return Activity::where('system', self::normalise($system))
            ->where('is_available', 1)
            ->orderBy('reorder_id')
            ->get();
    }

    /** Specializations belonging to one activity — never crosses into another system. */
    public static function specializations($activityId)
    {
        if (empty($activityId)) {
            return collect();
        }

        return Specialization::where('activity_id', $activityId)
            ->where('is_available', 1)
            ->orderBy('reorder_id')
            ->get();
    }

    /**
     * True when the activity really belongs to the system — the guard that stops a merchant
     * posting an activity from a system they did not pay for.
     */
    public static function activityBelongsTo($activityId, ?string $system): bool
    {
        return Activity::where('id', $activityId)->where('system', self::normalise($system))->exists();
    }

    public static function specializationBelongsTo($specializationId, $activityId): bool
    {
        return Specialization::where('id', $specializationId)->where('activity_id', $activityId)->exists();
    }

    /** Plans on sale for one system. */
    public static function plans(?string $system)
    {
        return PricingPlan::where('is_available', 1)
            ->where(function ($q) use ($system) {
                $q->where('system', self::normalise($system));
                // Plans created before the system column existed default to Orders & Stores.
                if (self::normalise($system) === self::ORDERS) {
                    $q->orWhereNull('system')->orWhere('system', '');
                }
            })
            ->orderBy('reorder_id')
            ->get();
    }

    /**
     * Status metadata: label plus the colour the client specified.
     * Amber = pending/correction, red = restricted, green = verified. No blue.
     */
    public static function statuses(): array
    {
        return [
            self::PENDING_PAYMENT => [
                'label' => trans('labels.status_awaiting_payment'),
                'colour' => 'amber', 'class' => 'bg-warning', 'hex' => '#d98a0b',
            ],
            self::PAID_SETUP_INCOMPLETE => [
                'label' => trans('labels.status_paid_setup_incomplete'),
                'colour' => 'amber', 'class' => 'bg-warning', 'hex' => '#d98a0b',
            ],
            self::PROVISIONALLY_ACTIVE => [
                'label' => trans('labels.status_provisionally_active'),
                'colour' => 'amber', 'class' => 'bg-warning', 'hex' => '#d98a0b',
            ],
            self::CORRECTION_REQUIRED => [
                'label' => trans('labels.status_correction_required'),
                'colour' => 'amber', 'class' => 'bg-warning', 'hex' => '#d98a0b',
            ],
            self::VERIFIED_ACTIVE => [
                'label' => trans('labels.status_verified_active'),
                'colour' => 'green', 'class' => 'bg-success', 'hex' => '#1f9d55',
            ],
            self::RESTRICTED => [
                'label' => trans('labels.status_restricted'),
                'colour' => 'red', 'class' => 'bg-danger', 'hex' => '#d64545',
            ],
        ];
    }

    public static function status(?string $key): array
    {
        $all = self::statuses();
        $meta = $all[$key] ?? $all[self::PENDING_PAYMENT];
        $meta['key'] = $key ?: self::PENDING_PAYMENT;
        $meta['text'] = $meta['label'];

        return $meta;
    }

    /** Has the merchant paid? Everything past pending_payment means yes. */
    public static function hasPaid($user): bool
    {
        return !empty($user) && ($user->account_status ?? self::PENDING_PAYMENT) !== self::PENDING_PAYMENT;
    }

    /** Is the public storefront live? Only true once the website has been activated. */
    public static function isLive($user): bool
    {
        return !empty($user)
            && in_array($user->account_status ?? '', [self::PROVISIONALLY_ACTIVE, self::CORRECTION_REQUIRED, self::VERIFIED_ACTIVE], true)
            && !empty($user->website_activated_date);
    }

    /** Still filling in the dashboard before the site goes public. */
    public static function inSetup($user): bool
    {
        return !empty($user) && ($user->account_status ?? '') === self::PAID_SETUP_INCOMPLETE;
    }

    /**
     * Turn a plan duration into an expiry date measured from a chosen start date, instead of from
     * "now" the way helper::get_plan_exp_date() does. Returns '' for a lifetime plan.
     */
    public static function expiryFrom(string $startDate, $duration, $days): string
    {
        $map = ['1' => 30, '2' => 90, '3' => 180, '4' => 365];

        if (!empty($days)) {
            return date('Y-m-d', strtotime($startDate . ' + ' . (int) $days . ' days'));
        }
        if ((string) $duration === '5' || empty($duration)) {
            return ''; // lifetime
        }

        return isset($map[(string) $duration])
            ? date('Y-m-d', strtotime($startDate . ' + ' . $map[(string) $duration] . ' days'))
            : '';
    }

    /**
     * Record a successful payment: lock the system onto the account, stamp the payment date and
     * move the merchant into setup mode. The subscription clock is NOT started here.
     */
    public static function markPaid($vendorId, ?string $system = null): void
    {
        $user = User::find($vendorId);
        if (empty($user) || (int) $user->type !== 2) {
            return;
        }

        $update = ['payment_date' => now()];

        // The very first payment moves the account into setup. A renewal/upgrade on an already
        // live account must not knock the storefront back offline.
        if (!self::isLive($user)) {
            $update['account_status'] = self::PAID_SETUP_INCOMPLETE;
        }
        if (empty($user->system) && self::isValid($system)) {
            $update['system'] = $system;
        }
        if (empty($user->account_created_date)) {
            $update['account_created_date'] = $user->created_at ?: now();
        }

        User::where('id', $vendorId)->update($update);
        Vendor360::syncStatus($vendorId); // a renewal on a live store re-activates its subscription badge
    }

    /**
     * Activate the public website. This is the moment the paid subscription period starts —
     * not the payment date. Safe to call twice; it will not restart the clock.
     */
    public static function activateWebsite($vendorId): bool
    {
        $user = User::find($vendorId);
        if (empty($user) || !self::hasPaid($user)) {
            return false;
        }
        if (!empty($user->website_activated_date)) {
            return true; // already running
        }

        $transaction = Transaction::where('vendor_id', $vendorId)
            ->whereNull('transaction_type')
            ->orderByDesc('id')
            ->first();

        $start = date('Y-m-d');
        $expiry = $transaction
            ? self::expiryFrom($start, $transaction->duration, $transaction->days)
            : '';

        DB::transaction(function () use ($user, $transaction, $start, $expiry, $vendorId) {
            if ($transaction) {
                $transaction->start_date   = $start;
                $transaction->activated_at = now();
                $transaction->expire_date  = $expiry ?: null;
                $transaction->save();
            }

            User::where('id', $vendorId)->update([
                'account_status'          => self::PROVISIONALLY_ACTIVE,
                'setup_completed'         => 1,
                'website_activated_date'  => now(),
                'subscription_start_date' => $start,
                'subscription_end_date'   => $expiry ?: null,
            ]);
        });
        Vendor360::syncStatus($vendorId);

        return true;
    }

    /**
     * Apply an activity/specialization choice, keeping the legacy settings.business_type and
     * storefront template in step so none of the existing theme code has to change.
     */
    public static function applyActivity($vendorId, $activityId, $specializationId = null): void
    {
        $activity = Activity::find($activityId);
        if (empty($activity)) {
            return;
        }

        $specialization = $specializationId && self::specializationBelongsTo($specializationId, $activity->id)
            ? $specializationId
            : null;

        // The marketplace category follows the activity — the merchant never picks one by hand,
        // and this is what puts the vendor in the right Marketplace section.
        $category = \App\Models\StoreCategory::forActivity($activity->id);

        User::where('id', $vendorId)->update(array_filter([
            'activity_id'       => $activity->id,
            'specialization_id' => $specialization,
            'store_id'          => $category->id ?? null,
        ], fn($v) => $v !== null));

        $settings = Settings::where('vendor_id', $vendorId)->first();
        if (empty($settings)) {
            return;
        }

        $settings->business_type = $activity->business_type ?: $settings->business_type;
        // Only seed the template if the merchant has none — the design they picked during
        // registration (or later in Brand & Design) is never overwritten.
        if (empty($settings->template) && $activity->template) {
            $settings->template = $activity->template;
        }
        $settings->save();
    }

    /** The merchant's plan name, for the "current plan" label in the dashboard. */
    public static function planName($user): string
    {
        if (empty($user) || empty($user->plan_id)) {
            return '';
        }
        $plan = PricingPlan::find($user->plan_id);

        return $plan->name ?? '';
    }
}
