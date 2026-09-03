<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deliberately linked to a real orders row (order_id), not a separate
     * parallel booking/payment system - "this should be counted as an
     * order" was explicit in the request, so this reuses the existing
     * order/payment/status infrastructure (Razorpay flow, order history,
     * franchise settlement) rather than duplicating it.
     */
    public function up(): void
    {
        Schema::create('lab_test_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_test_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('franchise_id')->constrained()->restrictOnDelete();
            // Only relevant for home_visit bookings - null for center_visit.
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            // Captured at booking time, not derived live from
            // lab_tests.requires_center_visit - if that flag changes later
            // (a test gets reclassified), past bookings shouldn't silently
            // change type.
            $table->enum('booking_type', ['home_visit', 'center_visit']);
            $table->date('scheduled_date');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['franchise_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_test_bookings');
    }
};
