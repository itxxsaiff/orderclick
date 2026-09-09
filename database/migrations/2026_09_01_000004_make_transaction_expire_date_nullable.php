<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * A first purchase is now stored with no expiry — the subscription clock only starts when the
 * merchant activates their public website — so expire_date has to accept NULL.
 * Raw SQL because the column change needs no doctrine/dbal dependency.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `transactions` MODIFY `expire_date` VARCHAR(255) NULL');
    }

    public function down(): void
    {
        DB::statement("UPDATE `transactions` SET `expire_date` = '' WHERE `expire_date` IS NULL");
        DB::statement('ALTER TABLE `transactions` MODIFY `expire_date` VARCHAR(255) NOT NULL');
    }
};
