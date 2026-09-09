<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * V2 WhatsApp flow (no API keys): the merchant just stores the WhatsApp number
 * orders should be sent to. Checkout builds a wa.me deep-link with the order
 * details — no Business API, no tokens.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('settings', 'whatsapp_number')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->string('whatsapp_number', 30)->nullable()->after('contact');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'whatsapp_number')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('whatsapp_number');
            });
        }
    }
};
