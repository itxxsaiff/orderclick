<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Guarantee that every activity has a category named after it.
 *
 * Legacy categories stay exactly as they are, images included — they remain linked to their
 * activity as additional marketplace sections. This migration only fills the gaps, so the
 * automatic assignment at registration always has an unambiguous, representative category to
 * pick (a generic "Retail & Shops" merchant must not land in "Flower Shops").
 *
 * Idempotent: safe to run repeatedly.
 */
return new class extends Migration
{
    public function up(): void
    {
        $order = (int) DB::table('store_category')->max('reorder_id');

        foreach (DB::table('activities')->orderBy('reorder_id')->get() as $activity) {
            $exists = DB::table('store_category')
                ->where('name', $activity->name)
                ->where('is_deleted', 2)
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('store_category')->insert([
                'reorder_id'   => ++$order,
                'name'         => $activity->name,
                'system'       => $activity->system,
                'activity_id'  => $activity->id,
                'is_other'     => 2,
                'image'        => null,  // placeholder until artwork is uploaded
                'is_available' => 1,
                'is_deleted'   => 2,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        // The catch-all under each system, in case it is missing.
        foreach (['orders' => 'Other — Orders & Stores', 'booking' => 'Other — Booking', 'service' => 'Other — Service Marketplace'] as $system => $name) {
            if (DB::table('store_category')->where('system', $system)->where('is_other', 1)->exists()) {
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

    /**
     * Intentionally a no-op. Categories are content the admin edits and vendors are assigned to;
     * deleting them on a rollback would orphan those vendors. Remove them by hand if ever needed.
     */
    public function down(): void
    {
    }
};
