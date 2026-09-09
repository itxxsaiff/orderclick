<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Register the custom Restaurant/Café storefront theme in the `theme` table so it
     * appears on the landing "Ready templates" section. Idempotent.
     */
    public function up(): void
    {
        $exists = DB::table('theme')->where('vendor_id', 1)->where('image', 'theme-restaurant.png')->exists();
        if (! $exists) {
            $nextOrder = (int) DB::table('theme')->where('vendor_id', 1)->max('reorder_id') + 1;
            DB::table('theme')->insert([
                'reorder_id' => $nextOrder,
                'vendor_id'  => 1,
                'name'       => 'Restaurant',
                'image'      => 'theme-restaurant.png',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('theme')->where('vendor_id', 1)->where('image', 'theme-restaurant.png')->delete();
    }
};
