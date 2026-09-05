<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorHospitalAffiliation extends Model
{
    protected $fillable = ['doctor_id', 'hospital_id', 'consultation_charge', 'is_active'];

    protected $casts = [
        'consultation_charge' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
    }

    public function visitDays(): HasMany
    {
        return $this->hasMany(DoctorHospitalVisitDay::class);
    }
}
