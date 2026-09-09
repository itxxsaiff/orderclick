<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * V2: give every store the manual/offline payment methods (Cash, Cash on Pickup,
 * BenefitPay, Bank QR, Payment Link). Seeded disabled (is_available = 2) so nothing
 * changes at checkout until the merchant turns them on. Idempotent per vendor+type.
 */
return new class extends Migration
{
    private array $methods = [
        ['type' => 17, 'uid' => 'cash', 'name' => 'Cash'],
        ['type' => 18, 'uid' => 'cash_pickup', 'name' => 'Cash on Pickup'],
        ['type' => 19, 'uid' => 'benefitpay', 'name' => 'BenefitPay'],
        ['type' => 20, 'uid' => 'bank_qr', 'name' => 'Bank QR Code'],
        ['type' => 21, 'uid' => 'payment_link', 'name' => 'Payment Link'],
    ];

    public function up(): void
    {
        $vendorIds = DB::table('payments')->distinct()->pluck('vendor_id');
        $now = DB::table('payments')->max('created_at') ?: '2026-01-01 00:00:00';

        foreach ($vendorIds as $vid) {
            $base = (int) DB::table('payments')->where('vendor_id', $vid)->max('reorder_id');
            foreach ($this->methods as $i => $m) {
                $exists = DB::table('payments')->where('vendor_id', $vid)->where('payment_type', $m['type'])->exists();
                if ($exists) {
                    continue;
                }
                DB::table('payments')->insert([
                    'vendor_id' => $vid,
                    'reorder_id' => $base + $i + 1,
                    'unique_identifier' => $m['uid'],
                    'payment_name' => $m['name'],
                    'payment_type' => $m['type'],
                    'currency' => '',
                    'image' => '',
                    'environment' => 1,
                    'is_available' => 2, // disabled by default
                    'is_activate' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('payments')->whereIn('payment_type', [17, 18, 19, 20, 21])->delete();
    }
};
