<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Subscription invoice tax.
 *
 * A tax rule now says WHAT it applies to (platform subscription invoices vs a vendor's own
 * customer sales), WHICH systems it covers, and whether the price already includes it.
 *
 * The company's tax registration number lives in General Settings, not on each rule — it is the
 * same number on every invoice, so repeating it per rule would only let the two drift apart.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tax', function (Blueprint $t) {
            // subscription = Order Click's own plan invoices · vendor_sales = the vendor's storefront tax
            if (!Schema::hasColumn('tax', 'applies_to'))  $t->string('applies_to', 20)->default('vendor_sales')->after('name');
            // all | orders | booking | service
            if (!Schema::hasColumn('tax', 'systems'))     $t->string('systems', 20)->default('all')->after('applies_to');
            // inclusive = the plan price already contains the tax · exclusive = added on top
            if (!Schema::hasColumn('tax', 'price_type'))  $t->string('price_type', 20)->default('exclusive')->after('tax');
        });

        // Every rule that already exists is a vendor's own sales tax — none of them were ever
        // meant to tax an Order Click subscription invoice.
        DB::table('tax')->whereNull('applies_to')->orWhere('applies_to', '')->update(['applies_to' => 'vendor_sales']);

        Schema::table('settings', function (Blueprint $t) {
            // Company identity for the subscription invoice header.
            if (!Schema::hasColumn('settings', 'company_legal_name'))       $t->string('company_legal_name')->nullable()->after('website_title');
            if (!Schema::hasColumn('settings', 'tax_registration_number'))  $t->string('tax_registration_number')->nullable()->after('company_legal_name');
        });
    }

    public function down(): void
    {
        Schema::table('tax', function (Blueprint $t) {
            foreach (['applies_to', 'systems', 'price_type'] as $c) {
                if (Schema::hasColumn('tax', $c)) $t->dropColumn($c);
            }
        });
        Schema::table('settings', function (Blueprint $t) {
            foreach (['company_legal_name', 'tax_registration_number'] as $c) {
                if (Schema::hasColumn('settings', $c)) $t->dropColumn($c);
            }
        });
    }
};
