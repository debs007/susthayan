<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoctorHospitalVisitDay extends Model
{
    protected $fillable = ['doctor_hospital_affiliation_id', 'day_of_week', 'start_time', 'end_time'];

    public function affiliation(): BelongsTo
    {
        return $this->belongsTo(DoctorHospitalAffiliation::class, 'doctor_hospital_affiliation_id');
    }
}
