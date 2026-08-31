<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('franchise_id')->constrained()->restrictOnDelete();
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete(); // null for pickup
            $table->enum('fulfillment_type', ['delivery', 'pickup']);
            $table->enum('status', [
                'pending_payment', 'confirmed', 'preparing', 'ready_for_dispatch',
                'out_for_delivery', 'delivered', 'picked_up', 'cancelled', 'refunded',
            ])->default('pending_payment');
            $table->decimal('subtotal_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('delivery_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->boolean('requires_prescription')->default(false);
            // Mirrors the stage timestamps in the Customer Order & Payment flow.
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->timestamp('out_for_delivery_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['franchise_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
