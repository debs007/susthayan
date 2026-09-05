<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('health_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('youtube_url');
            // R2 object key, same convention as every other image in this
            // app - admin-uploaded thumbnail, not auto-fetched from
            // YouTube, since the admin may want a custom/branded one.
            $table->string('thumbnail_path');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('health_articles');
    }
};
