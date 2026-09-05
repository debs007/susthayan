<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The charge lives here - once per doctor+hospital pairing - rather
     * than on individual visit-day rows, so the same doctor at the same
     * hospital can never end up with two different charges on two
     * different days by accident. Visit days are a separate one-to-many
     * table underneath this (see doctor_hospital_visit_days).
     */
    public function up(): void
    {
        Schema::create('doctor_hospital_affiliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->decimal('consultation_charge', 10, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['doctor_id', 'hospital_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_hospital_affiliations');
    }
};
