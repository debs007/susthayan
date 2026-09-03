<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Default 'product' so every existing order (all of which are
            // product orders) backfills correctly without a data migration.
            $table->enum('order_type', ['product', 'lab_test'])->default('product')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_type');
        });
    }
};
