<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Register the custom Retail/Store storefront theme in the `theme` table so it appears
     * on the landing themes gallery and register picker. Idempotent.
     */
    public function up(): void
    {
        $exists = DB::table('theme')->where('vendor_id', 1)->where('image', 'theme-retail.png')->exists();
        if (! $exists) {
            $nextOrder = (int) DB::table('theme')->where('vendor_id', 1)->max('reorder_id') + 1;
            DB::table('theme')->insert([
                'reorder_id' => $nextOrder,
                'vendor_id'  => 1,
                'name'       => 'Retail',
                'image'      => 'theme-retail.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('theme')->where('vendor_id', 1)->where('image', 'theme-retail.png')->delete();
    }
};
