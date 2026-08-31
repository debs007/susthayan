<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('goods_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goods_receipt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->string('batch_no');
            $table->date('expiry_date');
            $table->unsignedInteger('received_qty');
            $table->unsignedInteger('damaged_qty')->default(0); // optional Purchase Return module, SRS flow 3
            $table->decimal('purchase_rate', 10, 2);
            $table->decimal('mrp', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['product_id', 'batch_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('goods_receipt_items');
    }
};
