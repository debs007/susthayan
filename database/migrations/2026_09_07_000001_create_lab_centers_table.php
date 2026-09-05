<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Deliberately separate from franchises, not reusing that table - a
     * lab center is a diagnostic collection/testing point, not a pharmacy
     * storefront, even though it's still tied to a franchise_id for
     * settlement/payout attribution (every order still needs a franchise
     * to credit financially, same as a product order).
     */
    public function up(): void
    {
        Schema::create('lab_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('address');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('phone', 15)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            // The correction from the original design - a center can be
            // selected for a home-collection test too (the customer's
            // preference for which lab processes their sample), not just
            // for tests that require a physical visit. This flag is what
            // lets the app filter which centers show up for a
            // home-collection test specifically.
            $table->boolean('offers_home_collection')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_centers');
    }
};
