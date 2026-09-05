<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A wallet top-up isn't attributable to any specific store - unlike
     * a product order, a lab test booking, or an appointment, all of
     * which have a genuine franchise/hospital behind them. Rather than
     * force an arbitrary franchise onto a top-up (which would pollute
     * that store's settlement reports with revenue that has nothing to
     * do with it), this makes the column genuinely nullable.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['franchise_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('franchise_id')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('franchise_id')->references('id')->on('franchises')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['franchise_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('franchise_id')->nullable(false)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('franchise_id')->references('id')->on('franchises')->restrictOnDelete();
        });
    }
};
