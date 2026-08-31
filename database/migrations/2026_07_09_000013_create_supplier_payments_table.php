<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('supplier_invoice_id')->constrained()->restrictOnDelete();
            $table->decimal('amount_paid', 12, 2);
            $table->date('payment_date');
            $table->enum('payment_mode', ['bank_transfer', 'upi', 'cheque', 'cash']);
            $table->string('reference_number')->nullable();
            $table->enum('status', ['initiated', 'completed', 'failed'])->default('initiated');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payments');
    }
};
