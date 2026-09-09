<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Transactions: currency + a frozen snapshot of what was actually bought.
 *
 * The snapshot matters because plans get edited. Without it, reopening an old invoice shows
 * today's plan, not the one the vendor paid for. It stores name, price, currency, duration,
 * limits, offer/coupon and add-ons as they were at the moment of purchase.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $t) {
            if (!Schema::hasColumn('transactions', 'currency'))       $t->string('currency', 10)->default('USD')->after('amount');
            if (!Schema::hasColumn('transactions', 'plan_snapshot'))  $t->json('plan_snapshot')->nullable()->after('addons');
        });

        // Backfill the system on transactions that pre-date the column, from the plan first and
        // the vendor second, so the new System column/filter is never blank.
        DB::statement("
            UPDATE transactions t
            JOIN plans p ON p.id = t.plan_id
            SET t.system = p.system
            WHERE (t.system IS NULL OR t.system = '') AND p.system IS NOT NULL AND p.system <> ''
        ");
        DB::statement("
            UPDATE transactions t
            JOIN users u ON u.id = t.vendor_id
            SET t.system = COALESCE(NULLIF(u.system, ''), 'orders')
            WHERE t.system IS NULL OR t.system = ''
        ");

        // Backfill currency from the plan that was bought.
        DB::statement("
            UPDATE transactions t
            JOIN plans p ON p.id = t.plan_id
            SET t.currency = COALESCE(NULLIF(p.currency, ''), 'USD')
            WHERE t.currency IS NULL OR t.currency = ''
        ");
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $t) {
            foreach (['currency', 'plan_snapshot'] as $c) {
                if (Schema::hasColumn('transactions', $c)) $t->dropColumn($c);
            }
        });
    }
};
