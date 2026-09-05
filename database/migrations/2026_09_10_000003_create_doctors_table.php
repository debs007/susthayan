<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('degree'); // e.g. "MBBS, MD (Cardiology)"
            $table->unsignedTinyInteger('years_of_experience')->nullable();
            $table->text('bio')->nullable();
            // R2 object key, same convention as every other image in this
            // app - a real profile photo for what's meant to be an
            // elegant, polished booking UI.
            $table->string('photo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
