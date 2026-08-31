<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthProfile extends Model
{
    protected $fillable = [
        'user_id', 'blood_group', 'height_cm', 'weight_kg', 'date_of_birth', 'gender',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'weight_kg' => 'decimal:1',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Null if date_of_birth was never set - the profile card needs to handle "age unknown" gracefully, not assume this is always available. */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }
}
