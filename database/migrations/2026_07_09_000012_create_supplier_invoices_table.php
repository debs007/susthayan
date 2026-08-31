<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supplier_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('goods_receipt_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number');
            $table->date('invoice_date');
            $table->decimal('invoice_amount', 12, 2);
            $table->decimal('gst_amount', 12, 2)->default(0);
            $table->enum('status', ['pending_match', 'matched', 'approved', 'disputed', 'paid'])
                ->default('pending_match');
            $table->date('due_date')->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'invoice_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_invoices');
    }
};
