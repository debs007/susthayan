<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('original_filename');
            // Where the uploaded file itself sits until the job finishes
            // with it - not the CSV's own data, just its storage location.
            $table->string('stored_path');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            // Counted upfront from the file (a fast line-count pass) so the
            // progress bar has a real denominator from the very first
            // poll, not just once some rows have already been processed.
            $table->unsignedInteger('total_rows')->nullable();
            $table->unsignedInteger('processed_rows')->default(0);
            $table->unsignedInteger('imported_count')->default(0);
            // A malformed row (missing name/price, etc.) is skipped rather
            // than failing the whole import - this is where that count is
            // surfaced, so a large "skipped" number is visible rather than
            // silently swallowed.
            $table->unsignedInteger('skipped_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_imports');
    }
};
