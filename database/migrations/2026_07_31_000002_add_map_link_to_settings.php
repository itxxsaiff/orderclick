<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * V2 Store Configuration: the store's Google Maps location link. GPS lat/lng is
 * already stored on the users table (captured at registration); this holds an
 * optional pasted Google Maps place URL for display / pickup / the order message.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('settings', 'map_link')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->text('map_link')->nullable()->after('whatsapp_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('settings', 'map_link')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('map_link');
            });
        }
    }
};
