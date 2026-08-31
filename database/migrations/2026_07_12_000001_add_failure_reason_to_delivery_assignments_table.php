<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_assignments', function (Blueprint $table) {
            // Why a delivery was marked "failed" - customer unreachable,
            // wrong address, refused delivery, etc. Without this, a failed
            // attempt has no explanation attached for whoever reviews it.
            $table->string('failure_reason')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_assignments', function (Blueprint $table) {
            $table->dropColumn('failure_reason');
        });
    }
};
