<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('plans', function (Blueprint $table) {
            if (!Schema::hasColumn('plans', 'system'))              $table->string('system', 20)->default('orders')->after('name'); // orders | booking | service
            if (!Schema::hasColumn('plans', 'currency'))            $table->string('currency', 10)->default('USD')->after('price');
            if (!Schema::hasColumn('plans', 'recommended'))         $table->tinyInteger('recommended')->default(2)->after('is_available'); // 1 on, 2 off
            if (!Schema::hasColumn('plans', 'visibility'))          $table->tinyInteger('visibility')->default(1)->after('recommended');   // 1 show, 2 hide
            if (!Schema::hasColumn('plans', 'plan_limits'))         $table->json('plan_limits')->nullable()->after('visibility');          // product/order/branch/whatsapp/team {type,count}
            if (!Schema::hasColumn('plans', 'plan_offer'))          $table->json('plan_offer')->nullable()->after('plan_limits');          // enable/type/values/dates/applies/after
            if (!Schema::hasColumn('plans', 'plan_addons'))         $table->json('plan_addons')->nullable()->after('plan_offer');          // allow + price per extra unit
            if (!Schema::hasColumn('plans', 'plan_extra_features')) $table->json('plan_extra_features')->nullable()->after('plan_addons'); // system-specific feature toggles
        });
    }
    public function down(): void {
        Schema::table('plans', function (Blueprint $table) {
            foreach (['system','currency','recommended','visibility','plan_limits','plan_offer','plan_addons','plan_extra_features'] as $c) {
                if (Schema::hasColumn('plans', $c)) $table->dropColumn($c);
            }
        });
    }
};
