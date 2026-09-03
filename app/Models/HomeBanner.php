<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeBanner extends Model
{
    protected $fillable = [
        'image_path', 'badge_text', 'headline', 'subtitle', 'button_text', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /** Same pattern as Product/Brand/User's image accessors. */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path === null) {
            return null;
        }

        $base = rtrim(config('filesystems.disks.r2.url', ''), '/');

        return $base !== '' ? "{$base}/{$this->image_path}" : null;
    }
}
