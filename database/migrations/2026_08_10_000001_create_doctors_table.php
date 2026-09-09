<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('doctors')) {
            Schema::create('doctors', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_id')->index();
                $table->string('name');
                $table->string('specialty')->nullable();     // department, e.g. Cardiology / Dental
                $table->string('image')->nullable();
                $table->string('qualification')->nullable();  // e.g. MBBS, MD (Cardiology)
                $table->string('experience')->nullable();     // e.g. 12 years
                $table->decimal('fee', 12, 2)->default(0);     // consultation fee
                $table->string('languages')->nullable();       // e.g. English, Arabic
                $table->text('about')->nullable();
                $table->tinyInteger('is_available')->default(1);
                $table->integer('reorder_id')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
