<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Store categories, grouped by system and linked to the activity list.
 *
 * `activity_id` is what makes registration automatic: pick a system + activity and the vendor's
 * marketplace category is assigned from that link — no separate category question.
 *
 * Images stay optional. Existing categories keep the images they already have; the new ones fall
 * back to a placeholder icon until the client uploads real artwork.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_category', function (Blueprint $t) {
            if (!Schema::hasColumn('store_category', 'system'))      $t->string('system', 20)->default('orders')->after('name');
            if (!Schema::hasColumn('store_category', 'activity_id')) $t->unsignedBigInteger('activity_id')->nullable()->index()->after('system');
            // The catch-all under each system.
            if (!Schema::hasColumn('store_category', 'is_other'))    $t->tinyInteger('is_other')->default(2)->after('activity_id');
        });

        // Everything that already exists is an Orders & Stores category.
        DB::table('store_category')->update(['system' => 'orders']);

        // Link the existing categories (which already have artwork) to the activity they belong to,
        // so nothing has to be recreated and no images are lost.
        $byActivityName = DB::table('activities')->pluck('id', 'name');
        $existingMap = [
            'Restaurants'               => 'Restaurants & Cafés',
            'Cafés'                     => 'Restaurants & Cafés',
            'Cafes'                     => 'Restaurants & Cafés',
            'Bakeries'                  => 'Restaurants & Cafés',
            'Ice Cream Shops'           => 'Restaurants & Cafés',
            'Grocery Stores'            => 'Grocery & Supermarkets',
            'Fruit & Vegetable Markets' => 'Grocery & Supermarkets',
            'Dairy Stores'              => 'Grocery & Supermarkets',
            'Pharmacies'                => 'Pharmacies & Medical Supplies',
            'Flower Shops'              => 'Retail & Shops',
            'Clothing Stores'           => 'Retail & Shops',
            'Gift Shops'                => 'Retail & Shops',
            'Books'                     => 'Retail & Shops',
        ];
        foreach ($existingMap as $category => $activity) {
            if (isset($byActivityName[$activity])) {
                DB::table('store_category')->where('name', $category)
                    ->update(['activity_id' => $byActivityName[$activity], 'system' => 'orders']);
            }
        }

        // Every activity needs at least one category, otherwise registration has nothing to assign.
        $order = (int) DB::table('store_category')->max('reorder_id');
        foreach (DB::table('activities')->orderBy('reorder_id')->get() as $activity) {
            $covered = DB::table('store_category')->where('activity_id', $activity->id)->exists();
            if ($covered) {
                continue;
            }
            DB::table('store_category')->insert([
                'reorder_id'   => ++$order,
                'name'         => $activity->name,
                'system'       => $activity->system,
                'activity_id'  => $activity->id,
                'is_other'     => 2,
                'image'        => null,   // placeholder until the client uploads artwork
                'is_available' => 1,
                'is_deleted'   => 2,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // One "Other" catch-all per system.
        foreach (['orders' => 'Other — Orders & Stores', 'booking' => 'Other — Booking', 'service' => 'Other — Service Marketplace'] as $system => $name) {
            $exists = DB::table('store_category')->where('system', $system)->where('is_other', 1)->exists();
            if ($exists) {
                continue;
            }
            DB::table('store_category')->insert([
                'reorder_id'   => ++$order,
                'name'         => $name,
                'system'       => $system,
                'activity_id'  => null,
                'is_other'     => 1,
                'image'        => null,
                'is_available' => 1,
                'is_deleted'   => 2,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('store_category')->where('is_other', 1)->orWhereNotNull('activity_id')->whereNull('image')->delete();
        Schema::table('store_category', function (Blueprint $t) {
            foreach (['system', 'activity_id', 'is_other'] as $c) {
                if (Schema::hasColumn('store_category', $c)) $t->dropColumn($c);
            }
        });
    }
};
