<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'city_name')) $table->string('city_name')->nullable()->after('city_id');
            if (!Schema::hasColumn('users', 'area_name')) $table->string('area_name')->nullable()->after('area_id');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'city_name')) $table->dropColumn('city_name');
            if (Schema::hasColumn('users', 'area_name')) $table->dropColumn('area_name');
        });
    }
};
