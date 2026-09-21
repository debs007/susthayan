<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_banners', function (Blueprint $table) {
            // Default 'mobile' means every existing row (all uploaded for
            // the app's aspect ratio) stays exactly as it was - no
            // backfill needed, and the mobile app's own banner query
            // keeps returning exactly what it always has.
            $table->enum('platform', ['mobile', 'web'])->default('mobile')->after('image_path');
        });
    }

    public function down(): void
    {
        Schema::table('home_banners', function (Blueprint $table) {
            $table->dropColumn('platform');
        });
    }
};
