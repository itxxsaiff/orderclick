<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * V2 manual payment methods (no gateway keys): store a QR-code image (Bank QR /
 * BenefitPay QR) and a payment link (Payment Link method). Customer pays offline;
 * order stays unpaid until the merchant confirms.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'qr_image')) {
                $table->string('qr_image')->nullable()->after('image');
            }
            if (!Schema::hasColumn('payments', 'payment_link')) {
                $table->text('payment_link')->nullable()->after('qr_image');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            foreach (['qr_image', 'payment_link'] as $col) {
                if (Schema::hasColumn('payments', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
