<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A settlement can calculate exactly what a franchise is owed, but
     * actually paying it out needs somewhere to send the money - missing
     * from the original schema entirely.
     */
    public function up(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->string('bank_account_name')->nullable()->after('commission_percentage');
            $table->string('bank_account_number')->nullable()->after('bank_account_name');
            $table->string('bank_ifsc', 15)->nullable()->after('bank_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('franchises', function (Blueprint $table) {
            $table->dropColumn(['bank_account_name', 'bank_account_number', 'bank_ifsc']);
        });
    }
};
