<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * V2 Booking module — clinics, salons, doctors, gyms, consultants, etc.
 * A customer picks a service + date/time, the booking is saved here and a
 * structured WhatsApp message is generated for the merchant (keys-free flow).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->integer('vendor_id')->index();
                $table->string('booking_number', 60)->nullable();
                $table->integer('service_id')->nullable();          // links to an item (service) if chosen from the list
                $table->string('service_name')->nullable();
                $table->string('staff')->nullable();                // optional staff member
                $table->string('customer_name');
                $table->string('mobile', 40);
                $table->string('email')->nullable();
                $table->date('booking_date')->nullable();
                $table->string('booking_time', 40)->nullable();
                $table->text('notes')->nullable();
                // 1 = pending, 2 = confirmed, 3 = completed, 4 = cancelled
                $table->tinyInteger('status')->default(1);
                $table->tinyInteger('is_notification')->default(1); // 1 = unread, 2 = read
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
