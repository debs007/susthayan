<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Null for Customer / Super Admin / Finance-Admin; set for
            // Franchise Owner, Franchise Staff, Pharmacist, Delivery Agent.
            $table->foreignId('franchise_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('mobile', 15)->unique();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            // Nullable: pure-OTP customers may never set a password.
            $table->string('password')->nullable();
            $table->boolean('two_factor_enabled')->default(false); // enforced for Super Admin, see SRS 8
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
