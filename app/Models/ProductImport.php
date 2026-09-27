<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImport extends Model
{
    protected $fillable = [
        'uploaded_by', 'original_filename', 'stored_path', 'status',
        'total_rows', 'processed_rows', 'imported_count', 'skipped_count',
        'duplicate_count', 'error_message', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /** 0-100, safe against a zero or not-yet-known total_rows - the progress bar has something sane to show even before the upfront line-count finishes. */
    public function progressPercentage(): int
    {
        if (! $this->total_rows) {
            return 0;
        }

        return (int) min(100, round(($this->processed_rows / $this->total_rows) * 100));
    }
}
