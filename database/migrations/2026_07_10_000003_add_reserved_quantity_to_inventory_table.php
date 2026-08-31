<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Splits "physically in the store" (quantity) from "spoken for by a
     * paid, not-yet-fulfilled order" (reserved_quantity). Available-to-sell
     * is always quantity - reserved_quantity. Physical quantity only moves
     * on GRN (up) or actual fulfillment/damage (down) - reservation never
     * touches it, matching the SRS's "Stock RESERVED (not deducted)" step.
     */
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->unsignedInteger('reserved_quantity')->default(0)->after('quantity');
        });
    }

    public function down(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropColumn('reserved_quantity');
        });
    }
};
