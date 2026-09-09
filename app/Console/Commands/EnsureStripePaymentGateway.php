<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;

class EnsureStripePaymentGateway extends Command
{
    protected $signature = 'payments:ensure-stripe';

    protected $description = 'Ensure Stripe exists as a manageable payment gateway for the admin and vendors.';

    public function handle(): int
    {
        $adminStripe = Payment::where('vendor_id', 1)
            ->where('payment_type', Payment::TYPE_STRIPE)
            ->first();

        if (empty($adminStripe)) {
            $adminStripe = $this->createStripePayment(1, true);
            $this->info('Created Stripe payment method for admin.');
        }

        $vendorIds = User::where('type', 2)->pluck('id');
        $created = 0;
        $activated = 0;

        foreach ($vendorIds as $vendorId) {
            $payment = Payment::where('vendor_id', $vendorId)
                ->where('payment_type', Payment::TYPE_STRIPE)
                ->first();

            if (empty($payment)) {
                $this->createStripePayment((int) $vendorId, false, $adminStripe);
                $created++;
                continue;
            }

            if ((int) $payment->is_activate !== 1) {
                $payment->is_activate = 1;
                $payment->save();
                $activated++;
            }
        }

        $this->info("Stripe gateway ready. Vendor rows created: {$created}. Existing rows activated: {$activated}.");

        return self::SUCCESS;
    }

    private function createStripePayment(int $vendorId, bool $available, ?Payment $template = null): Payment
    {
        $payment = new Payment();
        $payment->reorder_id = optional($template)->reorder_id ?? 0;
        $payment->vendor_id = $vendorId;
        $payment->unique_identifier = 'stripe';
        $payment->payment_name = optional($template)->payment_name ?? 'Stripe';
        $payment->payment_type = Payment::TYPE_STRIPE;
        $payment->currency = optional($template)->currency ?? 'USD';
        $payment->image = optional($template)->image ?? 'stripe.png';
        $payment->public_key = '';
        $payment->secret_key = '';
        $payment->encryption_key = '';
        $payment->environment = optional($template)->environment ?? 1;
        $payment->base_url_by_region = '';
        $payment->is_available = $available ? 1 : 2;
        $payment->payment_description = null;
        $payment->is_activate = 1;
        $payment->save();

        return $payment;
    }
}
