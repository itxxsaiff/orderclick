<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AI store design: the design plan a store is rendered with (ai_design), a draft the merchant is
 * previewing (ai_design_draft), and the answers they gave the AI designer (ai_design_brief).
 * All three are JSON written only by App\Services\StoreDesign after validation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $t) {
            if (!Schema::hasColumn('settings', 'ai_design'))       $t->longText('ai_design')->nullable();
            if (!Schema::hasColumn('settings', 'ai_design_draft')) $t->longText('ai_design_draft')->nullable();
            if (!Schema::hasColumn('settings', 'ai_design_brief')) $t->text('ai_design_brief')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $t) {
            foreach (['ai_design', 'ai_design_draft', 'ai_design_brief'] as $c) {
                if (Schema::hasColumn('settings', $c)) $t->dropColumn($c);
            }
        });
    }
};
