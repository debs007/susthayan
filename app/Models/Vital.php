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
}
