<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** The customer's GPS pin for delivery orders, set on the checkout map. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            if (!Schema::hasColumn('orders', 'latitude'))  $t->decimal('latitude', 10, 7)->nullable();
            if (!Schema::hasColumn('orders', 'longitude')) $t->decimal('longitude', 10, 7)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $t) {
            foreach (['latitude', 'longitude'] as $c) {
                if (Schema::hasColumn('orders', $c)) $t->dropColumn($c);
            }
        });
    }
};
