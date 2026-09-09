<?php

namespace App\Helpers;

use App\Models\Payment;
use App\Models\PricingPlan;

/**
 * Subscription payments: which methods are accepted, what a payment's real status is, and the
 * plan snapshot frozen at purchase time.
 *
 * Kept separate from order payments on purpose — a customer buying a pizza and a merchant buying
 * a subscription are not the same transaction, and COD/wallet/dine-in methods make no sense here.
 */
class Subscriptions
{
    /**
     * The payment methods approved for Order Click subscriptions. Anything not listed here is
     * never shown to a merchant, even if it is switched on in Payment Methods — those other
     * gateways belong to the vendors' own storefronts, not to our subscription checkout.
     *
     * The admin can still switch any of these OFF; this list only caps what may appear.
     */
    public const APPROVED = [
        Payment::TYPE_STRIPE,         // 3  · card
        Payment::TYPE_BENEFITPAY,     // 19 · Benefit
        Payment::TYPE_BANK_TRANSFER,  // 6  · bank transfer, reviewed by an admin
        Payment::TYPE_BANK_QR,        // 20 · used for the Al Salam Bank account
    ];

    /** Methods that have no gateway redirect: the merchant pays offline and an admin approves. */
    public const MANUAL = [
        Payment::TYPE_BANK_TRANSFER,
        Payment::TYPE_BENEFITPAY,
        Payment::TYPE_BANK_QR,
        Payment::TYPE_PAYMENT_LINK,
        Payment::TYPE_CASH,
        Payment::TYPE_CASH_PICKUP,
        Payment::TYPE_COD,
    ];

    public static function isManual($paymentType): bool
    {
        return in_array((int) $paymentType, self::MANUAL, true);
    }

    /** Approved AND switched on in Payment Methods — what a merchant actually sees. */
    public static function availableMethods()
    {
        return Payment::where('vendor_id', 1)
            ->where('is_available', 1)
            ->where('is_activate', 1)
            ->whereIn('payment_type', self::APPROVED)
            ->orderBy('reorder_id')
            ->get();
    }

    /** The payment_type ids a subscription may currently be paid with. */
    public static function methodIds(): array
    {
        return self::availableMethods()->pluck('payment_type')->map(fn($t) => (int) $t)->all();
    }

    /**
     * A method is allowed when the admin has enabled it. Switching a method off in the panel is
     * what removes it — there is no second, hidden list to keep in sync.
     */
    public static function isAllowedMethod($paymentType): bool
    {
        return in_array((int) $paymentType, self::methodIds(), true);
    }

    /**
     * Methods to offer in the Transactions filter: everything currently enabled, plus anything
     * older transactions were actually paid with, so historic rows stay filterable.
     */
    public static function filterMethods(): array
    {
        $ids = self::methodIds();

        $used = \App\Models\Transaction::whereNull('transaction_type')
            ->whereNotNull('payment_type')->where('payment_type', '!=', '')
            ->distinct()->pluck('payment_type')->map(fn($t) => (int) $t)->all();

        $all = array_values(array_unique(array_filter(array_merge($ids, $used))));
        sort($all);

        return $all;
    }

    /**
     * Display name for a payment method — taken from Payment Methods in the admin panel, so a
     * rename there flows through everywhere. Only a couple of stored names are tidied for display.
     */
    public static function methodName($paymentType): string
    {
        if ((string) $paymentType === '' || (int) $paymentType === 0) {
            return 'Free / Manual';
        }

        $name = optional(helper::getpayment($paymentType, 1))->payment_name;
        if (empty($name)) {
            return '—';
        }

        // One name per method across the whole system — the video showed "Benefit" in one
        // place and "BenefitPay" in another.
        $pretty = [
            'Banktransfer' => 'Bank Transfer',
            'BenefitPay'   => 'Benefit',
            'Bank QR Code' => 'Al Salam Bank',
        ];

        return $pretty[$name] ?? $name;
    }

    /**
     * Payment status, resolved from the stored status plus the method and the dates.
     *
     * The stored `status` column only knows 1 pending / 2 approved / 3 rejected / 4 failed, so the
     * two extra states the client asked for are derived:
     *   · a pending BANK TRANSFER reads as "Bank Transfer Under Review"
     *   · a paid subscription past its expiry reads as "Expired"
     */
    public static function paymentStatus($transaction): array
    {
        $status = (int) $transaction->status;
        $method = (int) $transaction->payment_type;

        if ($status === 3) {
            return ['key' => 'rejected', 'label' => 'Rejected', 'label_ar' => 'مرفوض', 'class' => 'bg-danger'];
        }
        if ($status === 4) {
            return ['key' => 'failed', 'label' => 'Failed', 'label_ar' => 'فشل', 'class' => 'bg-danger'];
        }
        if ($status === 1) {
            return $method === Payment::TYPE_BANK_TRANSFER
                ? ['key' => 'under_review', 'label' => 'Bank Transfer Under Review', 'label_ar' => 'حوالة قيد المراجعة', 'class' => 'bg-warning']
                : ['key' => 'pending', 'label' => 'Pending', 'label_ar' => 'قيد الانتظار', 'class' => 'bg-warning'];
        }

        // Settled. An expired term is still worth calling out.
        if (!empty($transaction->expire_date) && strtotime($transaction->expire_date) < strtotime(date('Y-m-d'))) {
            return ['key' => 'expired', 'label' => 'Expired', 'label_ar' => 'منتهي', 'class' => 'bg-danger'];
        }

        return ['key' => 'paid', 'label' => 'Paid', 'label_ar' => 'مدفوع', 'class' => 'bg-success'];
    }

    /** All status keys, for the filter dropdown. */
    public static function statuses(): array
    {
        return [
            'paid'         => 'Paid',
            'pending'      => 'Pending',
            'under_review' => 'Bank Transfer Under Review',
            'failed'       => 'Failed',
            'rejected'     => 'Rejected',
            'expired'      => 'Expired',
        ];
    }

    /**
     * Where the subscription term stands. The term starts at ACTIVATION, never at payment, so a
     * paid-but-not-yet-activated subscription reports "not started".
     */
    public static function term($transaction): array
    {
        if ((int) $transaction->status !== 2) {
            return ['key' => 'not_started', 'label' => 'Not started', 'note' => 'Waiting for payment approval', 'class' => 'text-muted'];
        }
        if (empty($transaction->activated_at)) {
            return ['key' => 'pending_activation', 'label' => 'Pending activation', 'note' => 'Term not started', 'class' => 'text-warning'];
        }
        if (empty($transaction->expire_date)) {
            return ['key' => 'lifetime', 'label' => 'Lifetime', 'note' => 'No expiry', 'class' => 'text-success'];
        }

        $expired = strtotime($transaction->expire_date) < strtotime(date('Y-m-d'));

        return [
            'key'   => $expired ? 'expired' : 'active',
            'label' => date('d M', strtotime($transaction->start_date ?: $transaction->activated_at)) . ' → ' . date('d M', strtotime($transaction->expire_date)),
            'note'  => $expired ? 'Expired' : 'Active',
            'class' => $expired ? 'text-danger' : 'text-success',
        ];
    }

    /**
     * Freeze what was bought. Plans get edited later; an invoice must not change with them.
     */
    public static function snapshot(PricingPlan $plan, array $extra = []): array
    {
        $decode = function ($v) {
            if (empty($v)) return null;
            return is_array($v) ? $v : json_decode($v, true);
        };

        return array_merge([
            'plan_id'      => $plan->id,
            'name'         => $plan->name,
            'system'       => $plan->system,
            'price'        => (float) $plan->price,
            'currency'     => $plan->currency ?: 'USD',
            'duration'     => $plan->duration,
            'days'         => $plan->days,
            'order_limit'  => $plan->order_limit,
            'appointment_limit' => $plan->appointment_limit,
            'limits'       => $decode($plan->plan_limits ?? null),
            'offer'        => $decode($plan->plan_offer ?? null),
            'addons'       => $decode($plan->plan_addons ?? null),
            'features'     => $decode($plan->plan_extra_features ?? null),
            'captured_at'  => now()->toDateTimeString(),
        ], $extra);
    }


    /**
     * The company details printed on a subscription invoice. All of it lives in General Settings
     * (vendor 1) so it is entered once, not per tax rule and not per invoice.
     */
    public static function company(): array
    {
        $s = \App\Models\Settings::where('vendor_id', 1)->first();

        return [
            'name'    => optional($s)->website_title ?: 'Order Click',
            'legal'   => optional($s)->company_legal_name ?: trim(preg_replace('/^©\s*\d{4}\s*/u', '', (string) optional($s)->copyright)) ?: null,
            'tax_no'  => optional($s)->tax_registration_number ?: null,
            'address' => optional($s)->address ?: null,
            'email'   => optional($s)->email ?: null,
            'phone'   => optional($s)->contact ?: null,
            'logo'    => optional($s)->logo,
        ];
    }

    /**
     * Subscription invoice breakdown: Subtotal / Discount / Tax / Grand Total.
     *
     * Tax lines come from the transaction's own stored values where present (so an old invoice
     * never changes when a rule is edited), and otherwise from the active platform rules.
     * With every rule inactive the tax total is 0 and Grand Total == Subtotal - Discount.
     */
    public static function invoice($transaction): array
    {
        $currency = $transaction->currency ?: 'USD';
        $subtotal = (float) $transaction->amount;
        $discount = (float) $transaction->offer_amount;

        // Stored tax on the transaction: amounts and names are pipe-joined by the legacy engine.
        $lines = [];
        $amounts = array_filter(explode('|', (string) $transaction->tax), fn($v) => $v !== '');
        $names   = array_filter(explode('|', (string) $transaction->tax_name), fn($v) => $v !== '');
        foreach ($amounts as $i => $amount) {
            $lines[] = ['name' => $names[$i] ?? 'Tax', 'amount' => (float) $amount];
        }

        $taxTotal = array_sum(array_column($lines, 'amount'));
        $net = max(0, $subtotal - $discount);

        return [
            'currency'    => $currency,
            'subtotal'    => $subtotal,
            'discount'    => $discount,
            'tax_lines'   => $lines,
            'tax_total'   => $taxTotal,
            'grand_total' => (float) ($transaction->grand_total ?: $net + $taxTotal),
            'company'     => self::company(),
        ];
    }

    /**
     * Tax for a plan being purchased, as [amounts[], names[]] ready for the transaction columns.
     *
     * Combines the plan's own tax rules with any active PLATFORM subscription rule for that
     * system. Inclusive rules are backed out of the price rather than added on top.
     */
    public static function taxForPlan($plan): array
    {
        $amounts = [];
        $names   = [];
        $price   = (float) $plan->price;
        $seen    = [];

        // Rules attached to the plan itself (existing behaviour).
        if (!empty($plan->tax)) {
            foreach (helper::gettax($plan->tax) as $tax) {
                if (empty($tax)) {
                    continue;
                }
                $seen[] = $tax->id;
                $amounts[] = $tax->amountOn($price);
                $names[]   = $tax->name;
            }
        }

        // Platform rules for Order Click's own subscription invoices.
        foreach (\App\Models\Tax::forSubscription($plan->system) as $tax) {
            if (in_array($tax->id, $seen, true)) {
                continue; // already counted via the plan
            }
            $amounts[] = $tax->isInclusive()
                // Inclusive: the plan price already contains it, so back the tax out.
                ? ($tax->type == \App\Models\Tax::TYPE_PERCENTAGE
                    ? $price - ($price / (1 + ((float) $tax->tax / 100)))
                    : (float) $tax->tax)
                : $tax->amountOn($price);
            $names[] = $tax->name;
        }

        return [$amounts, $names];
    }

    /** The bank-transfer receipt a merchant uploaded, if this transaction has one. */
    public static function receiptUrl($transaction): ?string
    {
        // Every offline method takes a receipt, not just bank transfer — Benefit, Al Salam Bank
        // QR, payment link and cash all upload proof at checkout. Restricting this to type 6 hid
        // the uploaded receipt for all of them, so an admin had nothing to check before approving.
        if (!self::isManual($transaction->payment_type) || empty($transaction->screenshot)) {
            return null;
        }

        return helper::image_path($transaction->screenshot);
    }
}
