<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Self-reported only - there's no wearable/device integration
     * anywhere in this system, so every row here is something the
     * customer typed in themselves, not a synced reading. All metric
     * columns are individually nullable since a customer might log just
     * one reading (e.g. only blood pressure) at a time, not all four.
     */
    public function up(): void
    {
        Schema::create('vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('heart_rate_bpm')->nullable();
            $table->unsignedSmallInteger('blood_pressure_systolic')->nullable();
            $table->unsignedSmallInteger('blood_pressure_diastolic')->nullable();
            $table->unsignedTinyInteger('spo2_percentage')->nullable();
            $table->decimal('temperature_fahrenheit', 4, 1)->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['user_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vitals');
    }
};
