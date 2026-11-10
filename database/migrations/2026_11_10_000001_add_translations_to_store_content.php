<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Store content in every website language (en / ar / de / fr). The owner types each service,
 * product, category or FAQ once, in any language; `translations` holds the other languages:
 * {"_src": "<hash of the original text>", "ar": {"name": "…"}, "de": {…}, "fr": {…}}.
 * The hash makes an edited original fall back to itself until it is translated again.
 */
return new class extends Migration
{
    private array $tables = ['items', 'categories', 'booking_services', 'faqs', 'settings'];

    public function up(): void
    {
        foreach ($this->tables as $t) {
            if (Schema::hasTable($t) && !Schema::hasColumn($t, 'translations')) {
                Schema::table($t, function (Blueprint $table) {
                    $table->longText('translations')->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $t) {
            if (Schema::hasTable($t) && Schema::hasColumn($t, 'translations')) {
                Schema::table($t, function (Blueprint $table) {
                    $table->dropColumn('translations');
                });
            }
        }
    }
};
