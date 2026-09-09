<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'amount')) {
                $table->decimal('amount', 12, 2)->default(0)->after('service_name');
            }
            if (!Schema::hasColumn('bookings', 'payment_type')) {
                $table->string('payment_type')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('bookings', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('payment_type');
            }
            if (!Schema::hasColumn('bookings', 'payment_status')) {
                // 1 = unpaid / pay at location, 2 = paid
                $table->tinyInteger('payment_status')->default(1)->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            foreach (['amount', 'payment_type', 'payment_method', 'payment_status'] as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
