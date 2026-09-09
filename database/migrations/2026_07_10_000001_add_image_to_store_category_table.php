<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('store_category', 'image')) {
            Schema::table('store_category', function (Blueprint $table) {
                $table->string('image')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('store_category', 'image')) {
            Schema::table('store_category', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
