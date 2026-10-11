<?php

use App\Helpers\Onboarding;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

/**
 * Saving "Edit Profile" used to blank the store link (the form has no link field), so the store
 * stopped opening. Give every store that lost its link one back: vendor 61 gets its old
 * "tot-saif" link, others a link built from their name. Stores that still have a link are untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        $known = [61 => 'tot-saif'];
        // Deleted accounts (is_deleted = 1) have their link cleared on purpose; leave them alone.
        $stores = User::where('type', 2)->where('is_deleted', 2)->where(function ($q) {
            $q->whereNull('slug')->orWhere('slug', '');
        })->get();

        foreach ($stores as $store) {
            $base = $known[$store->id] ?? (Str::slug((string) $store->name) ?: 'store-' . $store->id);
            $store->slug = Onboarding::availableSlug($base, $store->id);
            $store->save();
        }
    }

    public function down(): void
    {
    }
};
