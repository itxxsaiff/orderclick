<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Subscription coupon scope.
 *
 * Coupons are CODES ONLY — automatic offers stay in Subscription Plans and are not duplicated
 * here. These four columns let a code be limited to one system, to specific plans, to a total
 * number of uses, and to be switched off.
 *
 * `is_available` already exists and is the Active/Inactive status, so it is reused rather than
 * duplicated — the form simply exposes it now.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promocodes', function (Blueprint $t) {
            // 'all' | orders | booking | service
            if (!Schema::hasColumn('promocodes', 'applicable_system')) $t->string('applicable_system', 20)->nullable()->after('offer_code');
            // Pipe-joined plan ids, matching how plans.tax / plans.themes_id already store lists.
            if (!Schema::hasColumn('promocodes', 'applicable_plans'))  $t->string('applicable_plans', 500)->nullable()->after('applicable_system');
            // Total redemptions allowed across ALL customers. NULL / 0 = unlimited.
            if (!Schema::hasColumn('promocodes', 'max_total_uses'))    $t->unsignedInteger('max_total_uses')->nullable()->after('usage_limit');
        });

        // Existing coupons keep working exactly as before: every system, every plan.
        DB::table('promocodes')->whereNull('applicable_system')->update(['applicable_system' => 'all']);
    }

    public function down(): void
    {
        Schema::table('promocodes', function (Blueprint $t) {
            foreach (['applicable_system', 'applicable_plans', 'max_total_uses'] as $c) {
                if (Schema::hasColumn('promocodes', $c)) $t->dropColumn($c);
            }
        });
    }
};
