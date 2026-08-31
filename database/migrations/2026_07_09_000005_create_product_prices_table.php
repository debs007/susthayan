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
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            // Null franchise_id = global default price; set = franchise-specific override.
            $table->foreignId('franchise_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('mrp', 10, 2);
            $table->decimal('selling_price', 10, 2);
            $table->decimal('tax_percentage', 5, 2)->default(0);
            $table->date('effective_from')->nullable();
            $table->timestamps();
        });

        // MySQL (like Postgres) treats every NULL as distinct for uniqueness
        // purposes, so a plain unique(product_id, franchise_id) would still
        // let two "global" (franchise_id IS NULL) rows exist for the same
        // product. MySQL also has no partial/filtered indexes, so instead of
        // a WHERE-clause index we add a generated column that substitutes 0
        // (never a real franchise id) for NULL, and make that column part of
        // a normal unique index - which enforces both cases in one shot.
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
