<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_test_category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            // Informational, shown to the customer before booking - not
            // used in any booking logic itself.
            $table->string('sample_type')->nullable(); // e.g. "Blood (fasting)", "Urine", "N/A - imaging"
            $table->text('preparation_instructions')->nullable(); // e.g. "8-12 hours fasting required"
            $table->decimal('price', 10, 2);
            // The field driving the whole home-visit vs center-visit
            // distinction from the request: false = a phlebotomist visits
            // the customer (blood/urine-type tests); true = the customer
            // must come to a center with the equipment (MRI/X-ray/imaging).
            $table->boolean('requires_center_visit')->default(false);
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('lab_test_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_tests');
    }
};
