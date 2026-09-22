<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Photo reuses the existing profile_image_path column and its
            // getProfileImageUrlAttribute() accessor - not duplicated here.
            $table->date('dob')->nullable()->after('name');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('dob');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dob', 'gender']);
        });
    }
};
