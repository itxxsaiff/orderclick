<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Landing-page features: which system a feature belongs to, and the order it appears in.
 *
 * "all" features appear in the general block; system-specific ones appear inside that system's
 * section on the landing page. This page is marketing content only — it drives no vendor
 * workflow and no AI behaviour.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('features', function (Blueprint $t) {
            // all | orders | booking | service
            if (!Schema::hasColumn('features', 'applies_to')) $t->string('applies_to', 20)->default('all')->after('title');
        });

        // Anything that already exists is general marketing copy.
        DB::table('features')->whereNull('applies_to')->orWhere('applies_to', '')->update(['applies_to' => 'all']);

        // reorder_id is nullable here; give existing rows a sensible sequence so Display Order
        // has something to show.
        $i = 0;
        foreach (DB::table('features')->orderBy('id')->pluck('id') as $id) {
            DB::table('features')->where('id', $id)->update(['reorder_id' => ++$i]);
        }
    }

    public function down(): void
    {
        Schema::table('features', function (Blueprint $t) {
            if (Schema::hasColumn('features', 'applies_to')) $t->dropColumn('applies_to');
        });
    }
};
