<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unsignedBigInteger('franchise_id')->nullable();

            $table->decimal('mrp', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->date('effective_from')->nullable();
            $table->timestamps();
        });

        DB::statement(
            'ALTER TABLE product_prices
             ADD COLUMN franchise_id_key BIGINT UNSIGNED
             GENERATED ALWAYS AS (COALESCE(franchise_id, 0)) STORED'
        );

        Schema::table('product_prices', function (Blueprint $table) {
            $table->unique(['product_id', 'franchise_id_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};