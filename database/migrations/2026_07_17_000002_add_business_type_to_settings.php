<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Order Click V2 onboarding: remember which kind of business a merchant runs
 * (food / grocery / retail / booking / service) so the storefront and dashboard
 * can adapt. Minor additive change — no existing column is touched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('settings', 'business_type')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('business_type', 32)->nullable()->after('template');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'business_type')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('business_type');
            });
        }
    }
};
