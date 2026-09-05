<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    protected $fillable = ['department_id', 'name', 'degree', 'years_of_experience', 'bio', 'photo_path', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function hospitals(): BelongsToMany
    {
        return $this->belongsToMany(Hospital::class, 'doctor_hospital_affiliations')->withPivot('id', 'consultation_charge')->withTimestamps();
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(DoctorHospitalAffiliation::class);
    }

    /** Same pattern as every other image accessor in this app. */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo_path === null) {
            return null;
        }

        $base = rtrim(config('filesystems.disks.r2.url', ''), '/');

        return $base !== '' ? "{$base}/{$this->photo_path}" : null;
    }
}
