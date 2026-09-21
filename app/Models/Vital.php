<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vital extends Model
{
    protected $fillable = [
        'user_id', 'heart_rate_bpm', 'blood_pressure_systolic', 'blood_pressure_diastolic',
        'spo2_percentage', 'temperature_fahrenheit', 'recorded_at',
    ];

    protected $casts = [
        'temperature_fahrenheit' => 'decimal:1',
        'recorded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Was referenced in an earlier admin view but never actually defined - that page has been silently showing no BP reading even when one exists. Fixes that retroactively, no view changes needed. */
    public function getBloodPressureLabelAttribute(): ?string
    {
        if ($this->blood_pressure_systolic === null || $this->blood_pressure_diastolic === null) {
            return null;
        }

        return "{$this->blood_pressure_systolic}/{$this->blood_pressure_diastolic}";
    }
}
