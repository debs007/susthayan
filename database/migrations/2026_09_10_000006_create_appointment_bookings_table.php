<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Same reasoning as lab_test_bookings - linked to a real Order so
     * "appointment booking should also be considered as order" is
     * literally true, not just a UI label. franchise_id is denormalized
     * from the hospital at booking time (not looked up live later), same
     * historical-accuracy reasoning as lab_test_bookings.franchise_id.
     */
    public function up(): void
    {
        Schema::create('appointment_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('hospital_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('franchise_id')->constrained()->restrictOnDelete();
            $table->date('scheduled_date');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['doctor_id', 'hospital_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_bookings');
    }
};
