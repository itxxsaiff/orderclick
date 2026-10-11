<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds the currencies stores actually trade in (Gulf first). Insert-only: existing rows and any
 * rates the admin already edited are left untouched. Rates are per 1 USD and can be edited in
 * Currency Settings; they only matter when a store offers more than one currency.
 */
return new class extends Migration
{
    public function up(): void
    {
        // code, name, symbol, rate per USD, position (1 before / 2 after), decimals
        $list = [
            ['bhd', 'Bahraini Dinar', 'BD', 0.376, 1, 3],
            ['sar', 'Saudi Riyal', 'SAR', 3.75, 2, 2],
            ['aed', 'UAE Dirham', 'AED', 3.6725, 2, 2],
            ['kwd', 'Kuwaiti Dinar', 'KD', 0.307, 1, 3],
            ['omr', 'Omani Rial', 'OMR', 0.385, 2, 3],
            ['qar', 'Qatari Riyal', 'QAR', 3.64, 2, 2],
            ['jod', 'Jordanian Dinar', 'JD', 0.709, 1, 3],
            ['egp', 'Egyptian Pound', 'EGP', 48.5, 2, 2],
            ['eur', 'Euro', '€', 0.92, 1, 2],
            ['gbp', 'British Pound', '£', 0.79, 1, 2],
            ['inr', 'Indian Rupee', '₹', 83.5, 1, 2],
            ['pkr', 'Pakistani Rupee', 'Rs', 278, 1, 2],
        ];
        $now = now();
        foreach ($list as [$code, $name, $symbol, $rate, $position, $decimals]) {
            if (!DB::table('currencies')->where('code', $code)->exists()) {
                DB::table('currencies')->insert([
                    'currency' => strtoupper($code), 'code' => $code, 'currency_symbol' => $symbol,
                    'is_available' => 1, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
            if (!DB::table('currency_settings')->where('code', $code)->exists()) {
                DB::table('currency_settings')->insert([
                    'code' => $code, 'name' => $name, 'currency' => $symbol, 'exchange_rate' => $rate,
                    'currency_position' => (string) $position, 'currency_space' => 1, 'currency_formate' => $decimals,
                    'decimal_separator' => 1, 'is_available' => 1, 'created_at' => $now, 'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        // Rows may already be in use by stores; nothing is removed.
    }
};
