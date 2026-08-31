<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('salt_composition')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('hsn_code', 20)->nullable(); // needed for GST invoicing
            // Indian drug schedule classification - drives prescription/legal handling.
            // Confirm exact values with legal/compliance before relying on this in logic.
            $table->enum('drug_schedule', ['otc', 'h', 'h1', 'x'])->default('otc');
            $table->boolean('prescription_required')->default(false);
            $table->string('unit')->nullable(); // e.g. "strip of 10 tablets", "100ml bottle"
            $table->string('barcode')->nullable()->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
