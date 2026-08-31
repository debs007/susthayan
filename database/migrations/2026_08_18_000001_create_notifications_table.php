<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Custom table, not Laravel's built-in notifications system - that
     * one is polymorphic-relation-based and designed for multi-channel
     * delivery (mail/SMS/database all at once), which is more machinery
     * than this needs. This is just an in-app notification list.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['order', 'health', 'offer', 'alert']);
            $table->string('title');
            $table->string('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
