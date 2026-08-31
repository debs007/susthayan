<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The actual FEFO allocation for a fulfilled order line. One order_item
     * can draw from more than one batch (nearest-expiry batch runs out
     * mid-order), so this is a proper one-to-many, not a single FK on
     * order_items. order_items.inventory_id is kept as a convenience
     * pointer to the first/primary batch, but this table is the source of
     * truth once an order is actually fulfilled.
     */
    public function up(): void
    {
        Schema::create('order_item_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            // Explicit table name: 'inventory' is deliberately singular (matches
            // the SRS's own naming), so constrained()'s auto-pluralized guess
            // ('inventories') is wrong here - the exact same fix already applied
            // to order_items.inventory_id, just missed on this sibling table.
            $table->foreignId('inventory_id')->constrained('inventory')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_batches');
    }
};
