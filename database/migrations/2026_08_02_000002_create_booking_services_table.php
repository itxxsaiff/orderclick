<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('booking_services')) {
            return;
        }
        Schema::create('booking_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id')->index();
            $table->string('name');
            $table->string('category')->nullable();   // e.g. Cardiology / Deluxe Room / Haircut
            $table->decimal('price', 12, 2)->default(0);
            $table->string('duration')->nullable();    // e.g. "30 min", "per night"
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->tinyInteger('is_available')->default(1);
            $table->integer('reorder_id')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_services');
    }
};
