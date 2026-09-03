<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * franchise_id is nullable and left in for future flexibility, but
     * the admin UI being built manages this as a network-wide calendar -
     * "admin can turn off the test day" was worded generically (Admin,
     * not Franchise), matching how this app's Admin role already manages
     * things centrally elsewhere (the product catalog, for example).
     */
    public function up(): void
    {
        Schema::create('lab_test_blocked_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchise_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['franchise_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_test_blocked_dates');
    }
};
