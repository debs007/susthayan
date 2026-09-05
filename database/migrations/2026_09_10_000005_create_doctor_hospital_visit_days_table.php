<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Recurring days of the week (Monday, Wednesday, Friday...), not
     * specific calendar dates - matches how a doctor's schedule at a
     * given hospital actually works in practice: an ongoing weekly
     * pattern, not a list of one-off dates that would need constant
     * re-entry.
     */
    public function up(): void
    {
        Schema::create('doctor_hospital_visit_days', function (Blueprint $table) {
            $table->id();
            // Column defined without ->constrained() here - the
            // auto-generated foreign key name
            // ("doctor_hospital_visit_days_doctor_hospital_affiliation_id_foreign")
            // is 65 characters, one over MySQL's 64-character identifier
            // limit. Explicit ->foreign() below with a short custom name
            // instead.
            $table->foreignId('doctor_hospital_affiliation_id');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->foreign('doctor_hospital_affiliation_id', 'dhvd_affiliation_id_foreign')
                ->references('id')->on('doctor_hospital_affiliations')
                ->cascadeOnDelete();

            $table->unique(['doctor_hospital_affiliation_id', 'day_of_week'], 'doctor_hospital_visit_day_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_hospital_visit_days');
    }
};
