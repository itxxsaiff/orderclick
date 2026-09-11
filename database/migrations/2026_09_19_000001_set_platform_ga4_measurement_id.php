<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Point the platform's Google Analytics at Click & Kick Digital's own GA4 property.
 *
 * The installer's demo data shipped tracking_id "G-WK8L1X80R2" — a property belonging to the
 * original script author — in every settings row, and no admin screen exposes the field, so all
 * Order Click traffic was being reported to someone else's Google Analytics.
 */
return new class extends Migration
{
    private const PLATFORM_ID = 'G-H7FDH9YJNY';
    private const DEMO_ID = 'G-WK8L1X80R2';

    public function up(): void
    {
        // Platform row: set the client's ID unless someone already put a different real one in.
        DB::table('settings')
            ->where('vendor_id', 1)
            ->where(function ($q) {
                $q->whereNull('tracking_id')->orWhere('tracking_id', '')->orWhere('tracking_id', self::DEMO_ID);
            })
            ->update(['tracking_id' => self::PLATFORM_ID]);

        // Merchant rows only ever held the copied demo ID; remove it so it can't leak anywhere.
        DB::table('settings')
            ->where('vendor_id', '!=', 1)
            ->where('tracking_id', self::DEMO_ID)
            ->update(['tracking_id' => null]);
    }

    public function down(): void
    {
        //
    }
};
