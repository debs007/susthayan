<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_medicines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained()->cascadeOnDelete();
            // Free text, not a product_id - the pharmacist is transcribing
            // from the prescription image at approval time, before any
            // matching to a catalogue product happens. That matching is a
            // separate step, done later when an order is actually created
            // from this prescription.
            $table->string('medicine_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_medicines');
    }
};
