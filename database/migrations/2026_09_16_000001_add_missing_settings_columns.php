<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * `darklogo` and `viewallpage_banner` are used by SettingsController, by the settings form and
 * by the storefront (dark mode swaps the logo in the header, footer and auth pages), but the
 * columns are missing from the database this install was built from. Uploading a dark logo
 * failed with "Unknown column 'darklogo' in 'field list'".
 *
 * Additive and nullable: no existing column is dropped, renamed or rewritten.
 */
return new class extends Migration
{
    /**
     * TEXT rather than VARCHAR on purpose. `settings` already carries ~145 columns, 58 of them
     * VARCHAR, putting the row at roughly 56 KB of MySQL's hard 65,535-byte limit — adding these
     * as VARCHAR(255) fails outright with "Row size too large" (almost certainly why they were
     * never added). The table is InnoDB ROW_FORMAT=Dynamic, so a TEXT column is stored off-page
     * and costs about 20 bytes in the row instead of ~1,022. The table already uses TEXT for 37
     * other columns, so this matches its existing shape.
     */
    private array $columns = ['darklogo', 'viewallpage_banner'];

    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            foreach ($this->columns as $name) {
                if (!Schema::hasColumn('settings', $name)) {
                    $table->text($name)->nullable();
                }
            }
        });

        // Without a dark logo the storefront's dark mode renders the "no image" placeholder.
        // Seeding the existing light logo keeps every site looking exactly as it does today
        // until someone uploads a proper dark version.
        DB::table('settings')
            ->whereNull('darklogo')
            ->whereNotNull('logo')
            ->where('logo', '!=', '')
            ->update(['darklogo' => DB::raw('`logo`')]);
    }

    /**
     * Deliberately empty. Dropping these would delete the uploaded dark logo and banner
     * filenames, and the application code expects the columns to exist.
     */
    public function down(): void
    {
        //
    }
};
