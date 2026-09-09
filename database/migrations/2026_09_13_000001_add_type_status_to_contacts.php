<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Inquiries: type, related system, status and archiving.
 *
 * `linked_user_id` connects an inquiry to the account it came from when the email matches a
 * registered vendor or customer; general inquiries stay unlinked. Archiving replaces deletion so
 * a support thread is never lost.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contacts', function (Blueprint $t) {
            // general | registration | payment | technical
            if (!Schema::hasColumn('contacts', 'inquiry_type'))   $t->string('inquiry_type', 30)->default('general')->after('message');
            // orders | booking | service — may be empty for a general inquiry
            if (!Schema::hasColumn('contacts', 'related_system')) $t->string('related_system', 20)->nullable()->after('inquiry_type');
            // new | in_progress | resolved
            if (!Schema::hasColumn('contacts', 'status'))         $t->string('status', 20)->default('new')->after('related_system');
            if (!Schema::hasColumn('contacts', 'linked_user_id')) $t->unsignedBigInteger('linked_user_id')->nullable()->index()->after('status');
            if (!Schema::hasColumn('contacts', 'archived_at'))    $t->timestamp('archived_at')->nullable()->after('linked_user_id');
            if (!Schema::hasColumn('contacts', 'admin_note'))     $t->text('admin_note')->nullable()->after('archived_at');
        });

        DB::table('contacts')->whereNull('inquiry_type')->orWhere('inquiry_type', '')->update(['inquiry_type' => 'general']);
        DB::table('contacts')->whereNull('status')->orWhere('status', '')->update(['status' => 'new']);

        // Link existing inquiries to the account that shares their email address.
        DB::statement("
            UPDATE contacts c
            JOIN users u ON u.email = c.email AND u.is_deleted = 2
            SET c.linked_user_id = u.id
            WHERE c.linked_user_id IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $t) {
            foreach (['inquiry_type', 'related_system', 'status', 'linked_user_id', 'archived_at', 'admin_note'] as $c) {
                if (Schema::hasColumn('contacts', $c)) $t->dropColumn($c);
            }
        });
    }
};
