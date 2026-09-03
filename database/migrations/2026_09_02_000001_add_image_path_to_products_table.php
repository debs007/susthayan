<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Stores the R2 object key (e.g. "products/abc123.jpg"), not a
            // full URL - the full public URL is computed on read via
            // Product::getImageUrlAttribute(), so switching buckets/domains
            // later never requires touching stored data.
            $table->string('image_path')->nullable()->after('barcode');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
