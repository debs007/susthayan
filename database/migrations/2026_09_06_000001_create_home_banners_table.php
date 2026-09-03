<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fields deliberately match the existing PromoBanner widget's
     * constructor exactly (badgeText, headline, subtitle, buttonText) -
     * this table is admin-managed content for a UI component that
     * already exists in the app, not a new display format.
     */
    public function up(): void
    {
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();
            // R2 object key, same convention as products/brands/profile -
            // full public URL computed on read, never stored.
            $table->string('image_path');
            $table->string('badge_text')->nullable();
            $table->string('headline')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('button_text')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_banners');
    }
};
