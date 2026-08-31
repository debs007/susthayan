<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            // Traceability back to the GRN line that created this stock.
            // Only GRN increases stock (SRS flow 3) - this FK is how we enforce that in code.
            $table->foreignId('goods_receipt_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('batch_no');
            $table->date('expiry_date');
            $table->integer('quantity')->default(0);
            $table->decimal('purchase_rate', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['franchise_id', 'product_id', 'batch_no']);
            $table->index('expiry_date'); // powers FEFO selection + near-expiry alerts
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
