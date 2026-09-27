<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Combined from the import's multiple use0/use1/... columns,
            // joined into one field - the existing description column is
            // generic free text, not structured for this specifically.
            $table->text('uses')->nullable()->after('description');
            // Directly from the import's own consolidated side-effects
            // column - already a single field there, no combining needed.
            $table->text('side_effects')->nullable()->after('uses');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['uses', 'side_effects']);
        });
    }
};
