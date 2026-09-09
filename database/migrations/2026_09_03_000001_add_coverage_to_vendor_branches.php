<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Locations & Marketplace coverage.
 *
 * Cities/areas are no longer typed in by hand — every branch carries the country/city/area text
 * that reverse geocoding filled in, plus its own coordinates. The `city` and `areas` tables are
 * deliberately left in place (other legacy screens still read them); they are simply no longer
 * required for a vendor to have a working location.
 *
 * Marketplace visibility is derived, never a manual flag: a branch appears publicly only once the
 * account is approved AND the branch's own review has passed.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_branches', function (Blueprint $t) {
            // How far this branch serves. Meaning depends on the system: delivery radius for
            // Orders & Stores, on-site/driving radius for Service Marketplace, unused for Booking.
            if (!Schema::hasColumn('vendor_branches', 'coverage_km'))       $t->decimal('coverage_km', 6, 2)->nullable()->after('longitude');
            // Orders & Stores fulfilment: delivery / pickup / dine_in.
            if (!Schema::hasColumn('vendor_branches', 'fulfilment'))        $t->json('fulfilment')->nullable()->after('coverage_km');
            // Online / remote providers: searchable in the Marketplace without a mandatory GPS pin.
            if (!Schema::hasColumn('vendor_branches', 'is_remote'))         $t->tinyInteger('is_remote')->default(2)->after('fulfilment');
            // Admin location review — a changed pin drops back to 'pending'.
            if (!Schema::hasColumn('vendor_branches', 'review_status'))     $t->string('review_status', 20)->default('pending')->after('is_remote'); // pending|verified|rejected
            if (!Schema::hasColumn('vendor_branches', 'reviewed_by'))       $t->unsignedBigInteger('reviewed_by')->nullable()->after('review_status');
            if (!Schema::hasColumn('vendor_branches', 'reviewed_at'))       $t->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            // Where the coordinates came from, so an admin knows how much to trust the pin.
            if (!Schema::hasColumn('vendor_branches', 'geo_source'))        $t->string('geo_source', 20)->nullable()->after('reviewed_at'); // gps|map|search|manual
            if (!Schema::hasColumn('vendor_branches', 'geocoded_at'))       $t->timestamp('geocoded_at')->nullable()->after('geo_source');
        });

        // Give every existing vendor a primary branch built from the store details they already
        // have, so the Locations screens are populated on day one and nothing has to be re-entered.
        $vendors = DB::table('users')
            ->where('type', 2)->where('is_deleted', 2)
            ->select('id', 'name', 'country', 'city_name', 'area_name', 'latitude', 'longitude', 'mobile', 'email', 'account_status')
            ->get();

        foreach ($vendors as $v) {
            if (DB::table('vendor_branches')->where('vendor_id', $v->id)->exists()) {
                continue;
            }

            $settings = DB::table('settings')->where('vendor_id', $v->id)->first();
            $hasPin = !empty($v->latitude) && !empty($v->longitude);

            DB::table('vendor_branches')->insert([
                'vendor_id'     => $v->id,
                'name'          => $v->name,
                'is_primary'    => 1,
                'country'       => $v->country,
                'city'          => $v->city_name,
                'area'          => $v->area_name,
                'address'       => $settings->address ?? null,
                'latitude'      => $v->latitude ?: null,
                'longitude'     => $v->longitude ?: null,
                'phone'         => $settings->contact ?? $v->mobile,
                'email'         => $settings->email ?? $v->email,
                'is_available'  => 1,
                'reorder_id'    => 1,
                // A vendor already live with a real pin is treated as verified so nothing
                // disappears from the marketplace during the upgrade.
                'review_status' => $hasPin && $v->account_status === 'verified_active' ? 'verified' : 'pending',
                'geo_source'    => $hasPin ? 'manual' : null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('vendor_branches', function (Blueprint $t) {
            foreach (['coverage_km', 'fulfilment', 'is_remote', 'review_status', 'reviewed_by', 'reviewed_at', 'geo_source', 'geocoded_at'] as $c) {
                if (Schema::hasColumn('vendor_branches', $c)) $t->dropColumn($c);
            }
        });
    }
};
