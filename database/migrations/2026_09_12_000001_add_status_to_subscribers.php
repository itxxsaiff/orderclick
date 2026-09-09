<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Email subscribers: status instead of deletion.
 *
 * An unsubscribe must never remove the row — the address has to stay on record precisely so no
 * future marketing email is sent to it. `token` backs the one-click unsubscribe link that every
 * marketing email carries.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscribers', function (Blueprint $t) {
            if (!Schema::hasColumn('subscribers', 'status'))          $t->string('status', 20)->default('subscribed')->after('email'); // subscribed | unsubscribed
            if (!Schema::hasColumn('subscribers', 'token'))           $t->string('token', 64)->nullable()->unique()->after('status');
            if (!Schema::hasColumn('subscribers', 'subscribed_at'))   $t->timestamp('subscribed_at')->nullable()->after('token');
            if (!Schema::hasColumn('subscribers', 'unsubscribed_at')) $t->timestamp('unsubscribed_at')->nullable()->after('subscribed_at');
            // Where the address came from, so it is always clear consent was given.
            if (!Schema::hasColumn('subscribers', 'source'))          $t->string('source', 30)->nullable()->after('unsubscribed_at');
        });

        foreach (DB::table('subscribers')->whereNull('token')->pluck('id') as $id) {
            DB::table('subscribers')->where('id', $id)->update([
                'token'         => Str::random(48),
                'status'        => 'subscribed',
                'subscribed_at' => DB::raw('COALESCE(created_at, NOW())'),
                'source'        => 'signup_form',
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('subscribers', function (Blueprint $t) {
            foreach (['status', 'token', 'subscribed_at', 'unsubscribed_at', 'source'] as $c) {
                if (Schema::hasColumn('subscribers', $c)) $t->dropColumn($c);
            }
        });
    }
};
