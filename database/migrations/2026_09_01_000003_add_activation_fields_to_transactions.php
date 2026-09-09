<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A paid transaction now sits "unstarted" until the merchant activates their public website.
 * `activated_at` records the moment the clock started; `expire_date` stays NULL until then, which
 * is why helper::checkplan() has to distinguish "not started yet" from "lifetime plan".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $t) {
            if (!Schema::hasColumn('transactions', 'system'))       $t->string('system', 20)->nullable()->after('plan_name');
            if (!Schema::hasColumn('transactions', 'start_date'))   $t->date('start_date')->nullable()->after('purchase_date');
            if (!Schema::hasColumn('transactions', 'activated_at')) $t->timestamp('activated_at')->nullable()->after('start_date');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $t) {
            foreach (['system', 'start_date', 'activated_at'] as $c) {
                if (Schema::hasColumn('transactions', $c)) $t->dropColumn($c);
            }
        });
    }
};
