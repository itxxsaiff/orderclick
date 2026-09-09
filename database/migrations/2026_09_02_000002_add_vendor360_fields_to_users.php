<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Vendor 360 identity + the four INDEPENDENT status axes the client asked for.
 *
 * Phase 1's `account_status` stays exactly as it is — it is the payment/activation lifecycle that
 * the middleware and checkplan() gates depend on. The four axes the client listed are separate
 * concerns, so verification / subscription / public page get their own columns, and the
 * "Account Status" axis (Active | Restricted | Suspended | Archived) is derived in
 * Vendor360::accountStatus() from account_status + archived_at + is_available.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            // Stable public Vendor ID — the key the AI layer and every side table hang off.
            if (!Schema::hasColumn('users', 'vendor_code'))       $t->string('vendor_code', 20)->nullable()->unique()->after('id');

            // Legal identity (registration collects the short form; these are filled in setup).
            if (!Schema::hasColumn('users', 'trade_name'))        $t->string('trade_name')->nullable()->after('name');
            if (!Schema::hasColumn('users', 'legal_name'))        $t->string('legal_name')->nullable()->after('trade_name');
            if (!Schema::hasColumn('users', 'owner_name'))        $t->string('owner_name')->nullable()->after('legal_name');
            if (!Schema::hasColumn('users', 'cr_number'))         $t->string('cr_number')->nullable()->index()->after('owner_name');

            // Independent status axes.
            if (!Schema::hasColumn('users', 'verification_status')) $t->string('verification_status', 30)->default('incomplete')->after('account_status');   // incomplete|pending_review|changes_required|approved|expired
            if (!Schema::hasColumn('users', 'subscription_status')) $t->string('subscription_status', 30)->default('expired')->after('verification_status'); // active|past_due|expired|cancelled
            if (!Schema::hasColumn('users', 'public_page_status'))  $t->string('public_page_status', 30)->default('draft')->after('subscription_status');    // draft|pending_approval|published|unpublished

            // Archive instead of hard delete.
            if (!Schema::hasColumn('users', 'archived_at'))       $t->timestamp('archived_at')->nullable()->after('public_page_status');
            if (!Schema::hasColumn('users', 'archived_by'))       $t->unsignedBigInteger('archived_by')->nullable()->after('archived_at');

            // Sandbox / test accounts are excluded from production totals.
            if (!Schema::hasColumn('users', 'is_sandbox'))        $t->tinyInteger('is_sandbox')->default(2)->after('archived_by'); // 1 sandbox, 2 production

            // Activity tracking for the vendor record header.
            if (!Schema::hasColumn('users', 'last_login_at'))     $t->timestamp('last_login_at')->nullable()->after('is_sandbox');
            if (!Schema::hasColumn('users', 'login_count'))       $t->unsignedInteger('login_count')->default(0)->after('last_login_at');

            // Private admin notes — never shown to the vendor, never readable by the AI layer.
            if (!Schema::hasColumn('users', 'admin_notes'))       $t->text('admin_notes')->nullable()->after('login_count');
        });

        // Backfill a stable vendor code for every existing vendor.
        foreach (DB::table('users')->where('type', 2)->whereNull('vendor_code')->pluck('id') as $id) {
            DB::table('users')->where('id', $id)->update(['vendor_code' => 'OC-' . str_pad($id, 5, '0', STR_PAD_LEFT)]);
        }

        // Derive the new axes from the state each vendor is already in, so the list is meaningful
        // on day one rather than showing everything as "incomplete".
        DB::table('users')->where('type', 2)->where('account_status', 'verified_active')->update([
            'verification_status' => 'approved',
            'public_page_status'  => 'published',
        ]);
        DB::table('users')->where('type', 2)->where('account_status', 'provisionally_active')->update([
            'verification_status' => 'pending_review',
            'public_page_status'  => 'published',
        ]);
        DB::table('users')->where('type', 2)->where('account_status', 'correction_required')->update([
            'verification_status' => 'changes_required',
            'public_page_status'  => 'published',
        ]);

        // Subscription status follows the live transaction's expiry.
        DB::statement("
            UPDATE users u
            LEFT JOIN (
                SELECT vendor_id, MAX(expire_date) AS expire_date
                FROM transactions WHERE transaction_type IS NULL GROUP BY vendor_id
            ) t ON t.vendor_id = u.id
            SET u.subscription_status = CASE
                WHEN t.vendor_id IS NULL THEN 'expired'
                WHEN t.expire_date IS NULL OR t.expire_date = '' THEN 'active'
                WHEN t.expire_date >= CURDATE() THEN 'active'
                ELSE 'expired' END
            WHERE u.type = 2
        ");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            foreach ([
                'vendor_code', 'trade_name', 'legal_name', 'owner_name', 'cr_number',
                'verification_status', 'subscription_status', 'public_page_status',
                'archived_at', 'archived_by', 'is_sandbox', 'last_login_at', 'login_count', 'admin_notes',
            ] as $c) {
                if (Schema::hasColumn('users', $c)) $t->dropColumn($c);
            }
        });
    }
};
