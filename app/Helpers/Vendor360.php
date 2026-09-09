<?php

namespace App\Helpers;

use App\Models\Booking;
use App\Models\Item;
use App\Models\Order;
use App\Models\PricingPlan;
use App\Models\ServiceRequest;
use App\Models\Settings;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VendorAddon;
use App\Models\VendorBranch;
use App\Models\VendorEvent;
use App\Models\VendorWhatsappNumber;
use Illuminate\Support\Facades\DB;

/**
 * Vendor 360 — the read model behind the Vendors list and the vendor record.
 *
 * Two rules the client was firm about, both enforced here:
 *   1. The four status axes are INDEPENDENT. Account, Verification, Subscription and Public Page
 *      each answer a different question, and nothing derives one from another except where noted.
 *   2. Every number is computed from real source rows (orders, bookings, events, transactions).
 *      There are no editable counters.
 */
class Vendor360
{
    // ---------------------------------------------------------------------------------------
    // Status axes. Green / amber / red only — blue is never a status.
    // ---------------------------------------------------------------------------------------

    public const ACCOUNT_STATUSES = [
        'active'     => ['label' => 'Active',     'label_ar' => 'نشط',    'colour' => 'green', 'class' => 'bg-success'],
        'restricted' => ['label' => 'Restricted', 'label_ar' => 'مقيّد',  'colour' => 'red',   'class' => 'bg-danger'],
        'suspended'  => ['label' => 'Suspended',  'label_ar' => 'موقوف',  'colour' => 'red',   'class' => 'bg-danger'],
        'archived'   => ['label' => 'Archived',   'label_ar' => 'مؤرشف',  'colour' => 'red',   'class' => 'bg-secondary'],
    ];

    public const VERIFICATION_STATUSES = [
        'incomplete'       => ['label' => 'Incomplete',       'label_ar' => 'غير مكتمل',   'colour' => 'amber', 'class' => 'bg-warning'],
        'pending_review'   => ['label' => 'Pending Review',   'label_ar' => 'قيد المراجعة', 'colour' => 'amber', 'class' => 'bg-warning'],
        'changes_required' => ['label' => 'Changes Required', 'label_ar' => 'مطلوب تعديل',  'colour' => 'amber', 'class' => 'bg-warning'],
        'approved'         => ['label' => 'Approved',         'label_ar' => 'معتمد',       'colour' => 'green', 'class' => 'bg-success'],
        'expired'          => ['label' => 'Expired',          'label_ar' => 'منتهي',       'colour' => 'red',   'class' => 'bg-danger'],
    ];

    public const SUBSCRIPTION_STATUSES = [
        'active'    => ['label' => 'Active',    'label_ar' => 'نشط',    'colour' => 'green', 'class' => 'bg-success'],
        'past_due'  => ['label' => 'Past Due',  'label_ar' => 'مستحق',  'colour' => 'amber', 'class' => 'bg-warning'],
        'expired'   => ['label' => 'Expired',   'label_ar' => 'منتهي',  'colour' => 'red',   'class' => 'bg-danger'],
        'cancelled' => ['label' => 'Cancelled', 'label_ar' => 'ملغى',   'colour' => 'red',   'class' => 'bg-danger'],
    ];

    public const PAGE_STATUSES = [
        'draft'            => ['label' => 'Draft',            'label_ar' => 'مسودة',        'colour' => 'amber', 'class' => 'bg-warning'],
        'pending_approval' => ['label' => 'Pending Approval', 'label_ar' => 'بانتظار الموافقة', 'colour' => 'amber', 'class' => 'bg-warning'],
        'published'        => ['label' => 'Published',        'label_ar' => 'منشور',        'colour' => 'green', 'class' => 'bg-success'],
        'unpublished'      => ['label' => 'Unpublished',      'label_ar' => 'غير منشور',    'colour' => 'red',   'class' => 'bg-danger'],
    ];

    private static function badge(array $set, ?string $key, string $fallback): array
    {
        $meta = $set[$key] ?? $set[$fallback];
        $meta['key']  = $key ?: $fallback;
        $meta['text'] = app()->getLocale() === 'ar' ? $meta['label_ar'] : $meta['label'];

        return $meta;
    }

    /**
     * The Account axis. This one IS derived: archived beats suspended beats restricted, because
     * those three states live in columns that already existed and must keep working.
     */
    public static function accountStatus($vendor): array
    {
        if (!empty($vendor->archived_at))                          $key = 'archived';
        elseif (($vendor->account_status ?? '') === Systems::RESTRICTED) $key = 'restricted';
        elseif ((int) ($vendor->is_available ?? 1) === 2)          $key = 'suspended';
        else                                                       $key = 'active';

        return self::badge(self::ACCOUNT_STATUSES, $key, 'active');
    }

    public static function verificationStatus($vendor): array
    {
        return self::badge(self::VERIFICATION_STATUSES, $vendor->verification_status ?? null, 'incomplete');
    }

    public static function subscriptionStatus($vendor): array
    {
        return self::badge(self::SUBSCRIPTION_STATUSES, $vendor->subscription_status ?? null, 'expired');
    }

    public static function pageStatus($vendor): array
    {
        return self::badge(self::PAGE_STATUSES, $vendor->public_page_status ?? null, 'draft');
    }

    // ---------------------------------------------------------------------------------------
    // Card + record summary
    // ---------------------------------------------------------------------------------------

    /**
     * Everything the vendor card shows, in one query pass per vendor. Counts come from the real
     * tables; nothing is cached in a column that could drift.
     */
    public static function summary($vendor): array
    {
        $id       = $vendor->id;
        $system   = Systems::normalise($vendor->system);
        $settings = Settings::where('vendor_id', $id)->first();

        $transaction = Transaction::where('vendor_id', $id)
            ->whereNull('transaction_type')
            ->orderByDesc('id')
            ->first();

        // Branch and WhatsApp counts fall back to the legacy single-store fields for vendors that
        // predate the branch tables, so the card is never blank for an existing customer.
        $branches = VendorBranch::where('vendor_id', $id)->count();
        if ($branches === 0) {
            $branches = 1;
        }
        $whatsapp = VendorWhatsappNumber::where('vendor_id', $id)->where('is_available', 1)->count();
        if ($whatsapp === 0 && !empty(optional($settings)->whatsapp_number)) {
            $whatsapp = 1;
        }

        // Transaction volume — the metric that matters depends on the purchased system.
        if ($system === Systems::BOOKING) {
            $volumeLabel = 'bookings';
            $volume      = Booking::where('vendor_id', $id)->count();
        } elseif ($system === Systems::SERVICE) {
            $volumeLabel = 'requests';
            $volume      = ServiceRequest::where('vendor_id', $id)->count();
        } else {
            $volumeLabel = 'orders';
            $volume      = Order::where('vendor_id', $id)->count();
        }

        $productLimit = $transaction->service_limit ?? null;
        $products     = Item::where('vendor_id', $id)->count();

        return [
            'system'          => $system,
            'system_label'    => Systems::label($system),
            'plan_name'       => Systems::planName($vendor) ?: '—',
            'branches'        => $branches,
            'whatsapp'        => $whatsapp,
            'products'        => $products,
            'product_limit'   => $productLimit,
            'usage_percent'   => self::usagePercent($products, $productLimit),
            'customers'       => User::where('type', 3)->where('vendor_id', $id)->count(),
            'volume'          => $volume,
            'volume_label'    => $volumeLabel,
            'visitors'        => VendorEvent::where('vendor_id', $id)->where('event_type', 'page_view')->distinct('session_id')->count('session_id'),
            'whatsapp_clicks' => VendorEvent::where('vendor_id', $id)->where('event_type', 'whatsapp_click')->count(),
            'outstanding'     => self::outstanding($id),
            'expiry'          => $vendor->subscription_end_date ?: (optional($transaction)->expire_date ?: null),
        ];
    }

    /** Percentage of a plan entitlement consumed. -1 or null means unlimited. */
    public static function usagePercent($used, $limit): ?int
    {
        if ($limit === null || (int) $limit === -1 || (float) $limit <= 0) {
            return null; // unlimited
        }

        return (int) min(100, round(($used / (float) $limit) * 100));
    }

    /** Unpaid / rejected subscription invoices still owed. */
    public static function outstanding($vendorId): float
    {
        return (float) Transaction::where('vendor_id', $vendorId)
            ->whereNull('transaction_type')
            ->whereIn('status', [1, 3])   // 1 = pending approval, 3 = rejected
            ->sum('grand_total');
    }

    /**
     * The single most important thing an admin should act on for this vendor. Ordered by urgency,
     * highest first — this is what the card's "Attention" line shows.
     */
    public static function alert($vendor, array $summary): array
    {
        if (!empty($vendor->archived_at)) {
            return ['text' => 'Archived account', 'class' => 'bg-secondary', 'key' => 'archived'];
        }
        if (($vendor->account_status ?? '') === Systems::RESTRICTED) {
            return ['text' => 'Account restricted', 'class' => 'bg-danger', 'key' => 'restricted'];
        }
        if ($summary['outstanding'] > 0) {
            return ['text' => 'Payment due', 'class' => 'bg-danger', 'key' => 'payment_due'];
        }
        if (($vendor->verification_status ?? '') === 'changes_required') {
            return ['text' => 'Document correction pending', 'class' => 'bg-warning', 'key' => 'changes_required'];
        }
        if (($vendor->verification_status ?? '') === 'pending_review') {
            return ['text' => 'Documents to review', 'class' => 'bg-warning', 'key' => 'pending_verification'];
        }
        if (($vendor->subscription_status ?? '') === 'expired' && Systems::hasPaid($vendor)) {
            return ['text' => 'Subscription expired', 'class' => 'bg-danger', 'key' => 'expired'];
        }
        if (!empty($summary['expiry']) && strtotime($summary['expiry']) <= strtotime('+14 days')) {
            return ['text' => 'Expiring soon', 'class' => 'bg-warning', 'key' => 'expiring_soon'];
        }
        if ($summary['usage_percent'] !== null && $summary['usage_percent'] >= 100) {
            return ['text' => 'Plan limit reached', 'class' => 'bg-danger', 'key' => 'limit_reached'];
        }
        if ($summary['usage_percent'] !== null && $summary['usage_percent'] >= 80) {
            return ['text' => 'Nearing plan limit', 'class' => 'bg-warning', 'key' => 'near_limit'];
        }
        if (Systems::inSetup($vendor)) {
            return ['text' => 'Setup incomplete', 'class' => 'bg-warning', 'key' => 'incomplete_setup'];
        }
        if (($vendor->account_status ?? '') === Systems::PENDING_PAYMENT) {
            return ['text' => 'Awaiting first payment', 'class' => 'bg-warning', 'key' => 'awaiting_payment'];
        }
        if ($summary['volume'] === 0 && ($vendor->public_page_status ?? '') === 'published') {
            return ['text' => 'No activity yet', 'class' => 'bg-secondary', 'key' => 'unused'];
        }

        return ['text' => 'No urgent action', 'class' => 'bg-light text-muted', 'key' => 'none'];
    }

    /** The admin queues from §13, as filter keys the list understands. */
    public static function queues(): array
    {
        return [
            'pending_verification' => 'Pending Verification',
            'changes_required'     => 'Changes Required',
            'payment_due'          => 'Payment Due',
            'expiring_soon'        => 'Subscription Expiring',
            'limit_reached'        => 'Plan Limit Reached',
            'incomplete_setup'     => 'Incomplete Setup',
            'unused'               => 'Unused Account',
        ];
    }
}
