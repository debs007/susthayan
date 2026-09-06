<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicine_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('medicine_name');
            $table->string('dosage_note')->nullable(); // e.g. "1 tablet after food"
            $table->json('times'); // ["09:00", "21:00"] - one or more times per day
            $table->date('start_date');
            $table->date('end_date')->nullable(); // null = ongoing/indefinite course
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicine_reminders');
    }
};
