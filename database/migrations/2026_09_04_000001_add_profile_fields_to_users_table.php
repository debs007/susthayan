<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // R2 object key, same convention as products.image_path - the
            // full public URL is computed on read, never stored.
            $table->string('profile_image_path')->nullable()->after('email');
            // Deliberately separate from `mobile` (the OTP-verified login
            // identity, never editable here) - this is a secondary contact
            // number the customer can set/change freely, with no
            // verification requirement.
            $table->string('alternate_mobile', 15)->nullable()->after('mobile');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profile_image_path', 'alternate_mobile']);
        });
    }
};
