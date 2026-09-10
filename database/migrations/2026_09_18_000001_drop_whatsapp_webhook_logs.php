<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Removes the temporary WhatsApp diagnostics log used while setting up the Meta integration.
 * Safe on installs that never had it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('whatsapp_webhook_logs');
    }

    public function down(): void
    {
        //
    }
};
