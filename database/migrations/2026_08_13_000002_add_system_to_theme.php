<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        if (!Schema::hasColumn('theme', 'system')) {
            Schema::table('theme', function (Blueprint $t) { $t->string('system', 20)->default('orders')->after('name'); });
        }
        // Map existing themes to their system (by image slug).
        $booking = ['theme-booking.png', 'theme-clinic.png', 'theme-salon.png'];
        DB::table('theme')->whereIn('image', $booking)->update(['system' => 'booking']);
        DB::table('theme')->whereNotIn('image', $booking)->update(['system' => 'orders']);
    }
    public function down(): void {
        if (Schema::hasColumn('theme', 'system')) {
            Schema::table('theme', function (Blueprint $t) { $t->dropColumn('system'); });
        }
    }
};
