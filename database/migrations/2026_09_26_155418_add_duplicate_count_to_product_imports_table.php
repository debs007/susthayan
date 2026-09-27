<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_imports', function (Blueprint $table) {
            // Separate from skipped_count (malformed rows - missing
            // name/price) - a row skipped because its name already
            // exists is a different, worth-distinguishing situation.
            $table->unsignedInteger('duplicate_count')->default(0)->after('skipped_count');
        });
    }

    public function down(): void
    {
        Schema::table('product_imports', function (Blueprint $table) {
            $table->dropColumn('duplicate_count');
        });
    }
};
