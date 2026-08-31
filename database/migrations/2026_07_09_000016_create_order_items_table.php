<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            // Set at fulfillment time once FEFO picks the batch - null while the order
            // is only "reserved" and stock hasn't actually been deducted yet.
            // Explicit table name: 'inventory' is deliberately singular (matches the
            // SRS's own naming), so Laravel's auto-pluralized guess ('inventories')
            // would be wrong here.
            $table->foreignId('inventory_id')->nullable()->constrained('inventory')->nullOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
