<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Template images: classify each template by system + applicable activity.
 *
 * `template` moves the storefront template number into the database. It was previously a
 * name → number map duplicated in three blades (register, settings, plans), which broke silently
 * whenever a template was renamed. One column, one source of truth.
 *
 * `activity_ids` is a pipe-joined list; empty means "all activities in this system".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theme', function (Blueprint $t) {
            if (!Schema::hasColumn('theme', 'activity_ids')) $t->string('activity_ids', 500)->nullable()->after('system');
            if (!Schema::hasColumn('theme', 'template'))     $t->unsignedTinyInteger('template')->nullable()->after('activity_ids');
        });

        // Storefront template number + the activity each existing template belongs to.
        // Keyed on the image slug, which is stable even if an admin renames the template.
        $activityByName = DB::table('activities')->pluck('id', 'name');
        $map = [
            'theme-restaurant.png' => ['template' => 3,  'activity' => 'Restaurants & Cafés'],
            'theme-grocery.png'    => ['template' => 5,  'activity' => 'Grocery & Supermarkets'],
            'theme-retail.png'     => ['template' => 6,  'activity' => 'Retail & Shops'],
            'theme-pharmacy.png'   => ['template' => 7,  'activity' => 'Pharmacies & Medical Supplies'],
            'theme-booking.png'    => ['template' => 8,  'activity' => null], // fits every booking activity
            'theme-clinic.png'     => ['template' => 9,  'activity' => 'Medical'],
            'theme-salon.png'      => ['template' => 10, 'activity' => 'Beauty & Wellness'],
        ];

        foreach ($map as $image => $meta) {
            $activityId = $meta['activity'] && isset($activityByName[$meta['activity']])
                ? (string) $activityByName[$meta['activity']]
                : null;

            DB::table('theme')->where('image', $image)->update([
                'template'     => $meta['template'],
                'activity_ids' => $activityId,
            ]);
        }

        // Anything unmapped (a custom upload) defaults to the classic template, all activities.
        DB::table('theme')->whereNull('template')->update(['template' => 2]);
    }

    public function down(): void
    {
        Schema::table('theme', function (Blueprint $t) {
            foreach (['activity_ids', 'template'] as $c) {
                if (Schema::hasColumn('theme', $c)) $t->dropColumn($c);
            }
        });
    }
};
