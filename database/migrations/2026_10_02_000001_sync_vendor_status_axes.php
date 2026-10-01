<?php

use App\Helpers\Vendor360;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;

/**
 * Re-sync the vendor card's Subscription and Public Page badges.
 *
 * Those two columns were only filled once, by the Vendor 360 migration, and nothing updated them
 * afterwards — so every merchant who paid and went live later still showed "Subscription expired"
 * and "Draft". Vendor360::syncStatus() now runs on every approval/activation; this applies it to
 * the accounts that already exist.
 */
return new class extends Migration
{
    public function up(): void
    {
        User::where('type', 2)->pluck('id')->each(fn($id) => Vendor360::syncStatus($id));
    }

    public function down(): void
    {
        // Data correction only.
    }
};
