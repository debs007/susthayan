<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Covers 4 of the 5 record types shown in the Health Records design
     * (lab reports, medical documents, vaccination records, health
     * checkups) - structurally identical (a dated record, optionally with
     * a file), so one table with a `type` column rather than 4 nearly
     * duplicate tables. Prescriptions is deliberately NOT included here -
     * that's already a real, separate feature (see the prescriptions
     * table) and gets merged into the health records LIST at the query
     * level, not duplicated into this table.
     */
    public function up(): void
    {
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['lab_report', 'medical_document', 'vaccination', 'checkup']);
            $table->string('title');
            // Private disk, same convention as prescriptions - never a
            // public URL, streamed only to the authenticated owner.
            $table->string('file_path')->nullable();
            $table->date('record_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
