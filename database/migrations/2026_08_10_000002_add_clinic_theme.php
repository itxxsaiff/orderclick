<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration {
    public function up(): void {
        if (! DB::table('theme')->where('vendor_id', 1)->where('image', 'theme-clinic.png')->exists()) {
            $n = (int) DB::table('theme')->where('vendor_id', 1)->max('reorder_id') + 1;
            DB::table('theme')->insert(['reorder_id' => $n, 'vendor_id' => 1, 'name' => 'Clinic', 'image' => 'theme-clinic.png', 'created_at' => now(), 'updated_at' => now()]);
        }
    }
    public function down(): void { DB::table('theme')->where('vendor_id', 1)->where('image', 'theme-clinic.png')->delete(); }
};
