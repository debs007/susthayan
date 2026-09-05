<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lab_test_bookings', function (Blueprint $table) {
            $table->foreignId('lab_center_id')->after('lab_test_id')->constrained()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lab_test_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lab_center_id');
        });
    }
};
