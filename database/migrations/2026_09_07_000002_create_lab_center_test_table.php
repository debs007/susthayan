<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Does two jobs at once: a row's mere existence is the admin's
     * checkbox ("this center offers this test" - a center with no MRI
     * machine simply has no row for the MRI test, so it never appears as
     * an option for that test in the app), and the price column is that
     * center's specific price for that test - different centers can
     * genuinely charge different amounts for the same test.
     */
    public function up(): void
    {
        Schema::create('lab_center_test', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_center_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lab_test_id')->constrained()->cascadeOnDelete();
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['lab_center_id', 'lab_test_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_center_test');
    }
};
