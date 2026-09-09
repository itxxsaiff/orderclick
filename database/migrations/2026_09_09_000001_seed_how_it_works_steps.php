<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * How It Works — one unified three-step flow for all systems.
 *
 * Replaces the old Portuguese, restaurant-only copy. Idempotent: it only writes the heading when
 * it is still the old Portuguese text, and only seeds steps when none exist, so an admin's later
 * edits are never overwritten by a re-run.
 */
return new class extends Migration
{
    public function up(): void
    {
        $title    = 'How Order Click Works';
        $subtitle = 'Choose your system, select a plan, and complete your business setup.';

        $settings = DB::table('settings')->where('vendor_id', 1)->first();
        if ($settings) {
            $stale = ['Como funciona?', '', null];
            DB::table('settings')->where('vendor_id', 1)->update([
                'work_title'    => in_array($settings->work_title, $stale, true) ? $title : $settings->work_title,
                'work_subtitle' => in_array($settings->work_subtitle, $stale, true) || str_contains((string) $settings->work_subtitle, 'restaurante')
                    ? $subtitle : $settings->work_subtitle,
            ]);
        }

        if (DB::table('works')->where('vendor_id', 1)->exists()) {
            return;
        }

        $steps = [
            ['Choose System & Activity', 'Select the system and activity that match your business.'],
            ['Select Plan & Payment',    'Choose the suitable plan and complete payment.'],
            ['Complete Dashboard Setup', 'Complete setup and submit the business for activation.'],
        ];

        foreach ($steps as $i => [$stepTitle, $stepSubtitle]) {
            DB::table('works')->insert([
                'vendor_id'  => 1,
                'reorder_id' => $i + 1,
                'title'      => $stepTitle,
                'sub_title'  => $stepSubtitle,
                'image'      => null,   // optional — a default icon shows until artwork is uploaded
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /** Content the admin may have edited — a rollback must not delete it. */
    public function down(): void
    {
    }
};
