<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Best-effort reconstruction, not a guaranteed byte-for-byte copy of
     * what `php artisan vendor:publish` would generate - vendor:publish
     * itself wasn't producing a file, so this was hand-built instead.
     * Confidence varies by column:
     *
     * CONFIRMED (appeared directly in the actual failed INSERT statement,
     * not assumed): log_name, attribute_changes, properties, event,
     * subject_id, subject_type, description, created_at, updated_at.
     *
     * RECONSTRUCTED (not in that insert - nullable columns that were
     * simply unset for that particular seeded event, since there's no
     * authenticated "causer" during seeding): causer_type, causer_id,
     * batch_uuid. These follow this package's stable, long-standing shape
     * across versions, but aren't independently confirmed against your
     * exact installed copy the way the columns above are.
     *
     * Since this is a fresh migrate with no existing data to preserve,
     * the practical risk of a minor mismatch is low - worst case is a
     * quick follow-up migration to adjust a column, not any data loss.
     */
    public function up(): void
    {
        Schema::create('activity_log', function (Blueprint $table) {
            $table->id();
            $table->string('log_name')->nullable();
            $table->text('description');
            $table->nullableMorphs('subject', 'subject');
            $table->nullableMorphs('causer', 'causer');
            $table->string('event')->nullable();
            $table->json('properties')->nullable();
            $table->json('attribute_changes')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->timestamps();

            $table->index('log_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_log');
    }
};
