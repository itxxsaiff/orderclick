<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vendor 360 — step 1 of the client's safe implementation sequence: the unified vendor data
 * structure. Nothing here changes existing behaviour; these are new side tables that the list,
 * the vendor record, verification, billing and (later) analytics all read from.
 *
 * Everything hangs off users.id, which is the stable Vendor ID the AI layer will key on.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ---- Branches -------------------------------------------------------------------
        if (!Schema::hasTable('vendor_branches')) {
            Schema::create('vendor_branches', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('vendor_id')->index();
                $t->string('name');
                $t->tinyInteger('is_primary')->default(2);      // 1 primary, 2 secondary
                $t->string('country')->nullable();
                $t->string('city')->nullable();
                $t->string('area')->nullable();
                $t->text('address')->nullable();
                $t->decimal('latitude', 10, 7)->nullable();
                $t->decimal('longitude', 10, 7)->nullable();
                $t->string('phone')->nullable();
                $t->string('email')->nullable();
                $t->json('opening_hours')->nullable();
                $t->tinyInteger('is_available')->default(1);    // 1 active, 2 inactive
                $t->unsignedInteger('reorder_id')->default(0);
                $t->timestamps();
            });
        }

        // ---- WhatsApp routing -----------------------------------------------------------
        if (!Schema::hasTable('vendor_whatsapp_numbers')) {
            Schema::create('vendor_whatsapp_numbers', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('vendor_id')->index();
                $t->unsignedBigInteger('branch_id')->nullable()->index();
                $t->string('number');
                $t->string('label')->nullable();                // purpose: orders, bookings, support…
                $t->string('assigned_to')->nullable();          // branch / doctor / provider / driver
                $t->json('days')->nullable();                   // operating days
                $t->string('start_time')->nullable();
                $t->string('end_time')->nullable();
                $t->unsignedInteger('priority')->default(1);    // routing priority, 1 = first
                $t->string('backup_number')->nullable();
                $t->tinyInteger('is_available')->default(1);
                $t->unsignedInteger('click_count')->default(0);
                $t->tinyInteger('is_primary')->default(2);
                $t->timestamps();
            });
        }

        // ---- Verification documents -----------------------------------------------------
        if (!Schema::hasTable('vendor_documents')) {
            Schema::create('vendor_documents', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('vendor_id')->index();
                $t->string('doc_type');                          // commercial_registration, owner_id, …
                $t->string('file')->nullable();
                $t->string('original_name')->nullable();
                $t->date('expiry_date')->nullable();
                $t->string('status', 30)->default('pending');    // pending|approved|changes_required|expired
                $t->text('review_note')->nullable();
                $t->unsignedBigInteger('reviewed_by')->nullable();
                $t->timestamp('reviewed_at')->nullable();
                $t->unsignedInteger('version')->default(1);      // re-uploads bump this
                $t->timestamps();
            });
        }

        // ---- Add-ons & services ----------------------------------------------------------
        if (!Schema::hasTable('vendor_addons')) {
            Schema::create('vendor_addons', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('vendor_id')->index();
                $t->string('name');
                $t->string('addon_key')->nullable()->index();    // template_design, ai_credits, extra_branch…
                $t->string('source', 20)->default('paid');       // plan | paid
                $t->decimal('quantity', 12, 2)->default(0);
                $t->decimal('used', 12, 2)->default(0);
                $t->decimal('price', 12, 2)->default(0);
                $t->string('currency', 10)->default('USD');
                $t->string('status', 30)->default('pending');    // pending|in_progress|delivered|cancelled
                $t->string('assigned_to')->nullable();
                $t->timestamp('ordered_at')->nullable();
                $t->timestamp('delivered_at')->nullable();
                $t->string('deliverable_link')->nullable();
                $t->string('invoice_number')->nullable();
                $t->timestamps();
            });
        }

        // ---- Audit trail -----------------------------------------------------------------
        if (!Schema::hasTable('vendor_audit_logs')) {
            Schema::create('vendor_audit_logs', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('vendor_id')->index();
                $t->string('field');                             // business_name, gps, plan, verification…
                $t->text('old_value')->nullable();
                $t->text('new_value')->nullable();
                $t->string('actor_type', 20)->default('admin');  // vendor | admin | system | ai
                $t->unsignedBigInteger('actor_id')->nullable();
                $t->string('actor_name')->nullable();
                $t->text('reason')->nullable();
                $t->string('ip', 45)->nullable();
                $t->timestamps();
                $t->index(['vendor_id', 'created_at']);
            });
        }

        // ---- Raw analytics events --------------------------------------------------------
        // Analytics are computed from these rows, never from editable counters. A WhatsApp click
        // and a completed transaction are separate event types on purpose.
        if (!Schema::hasTable('vendor_events')) {
            Schema::create('vendor_events', function (Blueprint $t) {
                $t->id();
                $t->unsignedBigInteger('vendor_id')->index();
                $t->unsignedBigInteger('branch_id')->nullable();
                $t->string('event_type', 40)->index();           // page_view|whatsapp_click|checkout_started|transaction_completed
                $t->string('session_id')->nullable()->index();
                $t->unsignedBigInteger('user_id')->nullable();
                $t->string('source')->nullable();                // referrer / traffic source
                $t->decimal('value', 12, 2)->nullable();
                $t->json('meta')->nullable();
                $t->timestamps();
                $t->index(['vendor_id', 'event_type', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        foreach (['vendor_events', 'vendor_audit_logs', 'vendor_addons', 'vendor_documents', 'vendor_whatsapp_numbers', 'vendor_branches'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
