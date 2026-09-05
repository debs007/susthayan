<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    protected $fillable = ['franchise_id', 'name', 'address', 'city', 'state', 'pincode', 'phone', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_hospital_affiliations')->withPivot('id', 'consultation_charge')->withTimestamps();
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(DoctorHospitalAffiliation::class);
    }

    public function fullAddress(): string
    {
        return collect([$this->address, $this->city, $this->state, $this->pincode])->filter()->implode(', ');
    }
}
