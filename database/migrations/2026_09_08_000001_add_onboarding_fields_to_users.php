<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vendor onboarding wizard.
 *
 * Steps 2–4 (Verification & Licensing, Account Authorization, Agreement) collect the fields the
 * client specified. Document FILES live in vendor_documents — one row per document type with its
 * own review status — so only the scalar fields sit here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            // Step 2 — Verification & Licensing
            if (!Schema::hasColumn('users', 'license_number'))         $t->string('license_number')->nullable()->after('cr_number');
            if (!Schema::hasColumn('users', 'license_expiry_date'))    $t->date('license_expiry_date')->nullable()->after('license_number');

            // Step 3 — Account Authorization
            if (!Schema::hasColumn('users', 'authorized_person'))      $t->string('authorized_person')->nullable()->after('license_expiry_date');
            if (!Schema::hasColumn('users', 'authorized_position'))    $t->string('authorized_position')->nullable()->after('authorized_person');
            if (!Schema::hasColumn('users', 'authorized_id_number'))   $t->string('authorized_id_number')->nullable()->after('authorized_position');

            // How far through the wizard the merchant has got (1..6).
            if (!Schema::hasColumn('users', 'setup_step'))             $t->unsignedTinyInteger('setup_step')->default(1)->after('setup_completed');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            foreach (['license_number', 'license_expiry_date', 'authorized_person', 'authorized_position', 'authorized_id_number', 'setup_step'] as $c) {
                if (Schema::hasColumn('users', $c)) $t->dropColumn($c);
            }
        });
    }
};
