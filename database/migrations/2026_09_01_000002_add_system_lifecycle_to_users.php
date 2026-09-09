<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * V2 account lifecycle.
 *
 * The purchased System is locked onto the account, and every milestone gets its own date column so
 * the subscription period can start at *website activation* instead of at payment — the client's
 * rule 7. Existing vendors are backfilled as already-activated so nothing goes dark on deploy.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            // System -> Activity -> Specialization
            if (!Schema::hasColumn('users', 'system'))              $t->string('system', 20)->nullable()->after('type');
            if (!Schema::hasColumn('users', 'activity_id'))         $t->unsignedBigInteger('activity_id')->nullable()->after('system');
            if (!Schema::hasColumn('users', 'specialization_id'))   $t->unsignedBigInteger('specialization_id')->nullable()->after('activity_id');
            if (!Schema::hasColumn('users', 'legal_status'))        $t->string('legal_status', 20)->nullable()->after('specialization_id'); // company | freelancer

            // Lifecycle status. See App\Helpers\Systems::statuses() for the allowed values.
            if (!Schema::hasColumn('users', 'account_status'))      $t->string('account_status', 30)->default('pending_payment')->after('is_available');
            if (!Schema::hasColumn('users', 'setup_completed'))     $t->tinyInteger('setup_completed')->default(2)->after('account_status'); // 1 yes, 2 no

            // Milestone dates — kept separate so none of them can be inferred from another.
            if (!Schema::hasColumn('users', 'account_created_date'))    $t->timestamp('account_created_date')->nullable()->after('setup_completed');
            if (!Schema::hasColumn('users', 'payment_date'))            $t->timestamp('payment_date')->nullable()->after('account_created_date');
            if (!Schema::hasColumn('users', 'document_submitted_date')) $t->timestamp('document_submitted_date')->nullable()->after('payment_date');
            if (!Schema::hasColumn('users', 'website_activated_date'))  $t->timestamp('website_activated_date')->nullable()->after('document_submitted_date');
            if (!Schema::hasColumn('users', 'subscription_start_date')) $t->date('subscription_start_date')->nullable()->after('website_activated_date');
            if (!Schema::hasColumn('users', 'subscription_end_date'))   $t->date('subscription_end_date')->nullable()->after('subscription_start_date');
            if (!Schema::hasColumn('users', 'verified_date'))           $t->timestamp('verified_date')->nullable()->after('subscription_end_date');
        });

        // Backfill: any vendor that already exists keeps working exactly as before.
        DB::table('users')->where('type', 2)->update(['system' => 'orders']);

        // A vendor with a live subscription is treated as already activated and verified, so the
        // new gates never lock out a paying customer during the upgrade.
        $live = DB::table('transactions')
            ->whereNull('transaction_type')
            ->whereNotNull('vendor_id')
            ->select('vendor_id', DB::raw('MAX(expire_date) as expire_date'), DB::raw('MIN(purchase_date) as purchase_date'))
            ->groupBy('vendor_id')
            ->get();

        foreach ($live as $row) {
            DB::table('users')->where('id', $row->vendor_id)->where('type', 2)->update([
                'account_status'         => 'verified_active',
                'setup_completed'        => 1,
                'account_created_date'   => $row->purchase_date,
                'payment_date'           => $row->purchase_date,
                'website_activated_date' => $row->purchase_date,
                'subscription_start_date' => $row->purchase_date ? date('Y-m-d', strtotime($row->purchase_date)) : null,
                'subscription_end_date'  => $row->expire_date ?: null,
                'verified_date'          => $row->purchase_date,
            ]);
        }

        // Admin (vendor 1) is never subject to the vendor lifecycle.
        DB::table('users')->where('type', 1)->update(['account_status' => 'verified_active', 'setup_completed' => 1]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            foreach ([
                'system', 'activity_id', 'specialization_id', 'legal_status', 'account_status', 'setup_completed',
                'account_created_date', 'payment_date', 'document_submitted_date', 'website_activated_date',
                'subscription_start_date', 'subscription_end_date', 'verified_date',
            ] as $c) {
                if (Schema::hasColumn('users', $c)) $t->dropColumn($c);
            }
        });
    }
};
