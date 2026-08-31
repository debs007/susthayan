<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            // Nullable: for online payments, Razorpay's Standard Checkout is a
            // single widget covering UPI/Card/Netbanking - which one the
            // customer actually used is only known once the gateway reports
            // it back (webhook/verify), not at the moment payment is initiated.
            $table->enum('payment_mode', ['upi', 'card', 'netbanking', 'cod', 'pos_cash'])->nullable();
            $table->string('gateway')->nullable(); // e.g. "razorpay"
            $table->string('gateway_order_id')->nullable();
            $table->string('gateway_txn_id')->nullable();
            $table->enum('status', ['initiated', 'success', 'failed', 'pending'])->default('initiated');
            $table->date('settlement_date')->nullable();
            $table->timestamps();

            $table->index('gateway_txn_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
    }
};
