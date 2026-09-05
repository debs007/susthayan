<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthArticle extends Model
{
    protected $fillable = ['title', 'youtube_url', 'thumbnail_path', 'description', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /** Same pattern as every other image accessor in this app. */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail_path === null) {
            return null;
        }

        $base = rtrim(config('filesystems.disks.r2.url', ''), '/');

        return $base !== '' ? "{$base}/{$this->thumbnail_path}" : null;
    }

    /**
     * Handles watch?v=, youtu.be/, embed/, and shorts/ URL shapes, with or
     * without extra query params (&t=30s, ?si=..., etc.) - null if the
     * stored URL doesn't match any recognized YouTube format.
     */
    public static function extractVideoId(string $url): ?string
    {
        $pattern = '/(?:youtube\.com\/watch\?v=|youtube\.com\/shorts\/|youtube\.com\/embed\/|youtu\.be\/)([A-Za-z0-9_-]{11})/';

        return preg_match($pattern, $url, $matches) ? $matches[1] : null;
    }
}
