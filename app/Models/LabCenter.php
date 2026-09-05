<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabCenter extends Model
{
    protected $fillable = [
        'franchise_id', 'name', 'address', 'city', 'state', 'pincode', 'phone',
        'latitude', 'longitude', 'offers_home_collection', 'is_active',
    ];

    protected $casts = [
        'offers_home_collection' => 'boolean',
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    /** withPivot('price') is what makes $center->tests->first()->pivot->price available - the center-specific price for that test. */
    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(LabTest::class, 'lab_center_test')->withPivot('price')->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(LabTestBooking::class);
    }

    public function fullAddress(): string
    {
        return collect([$this->address, $this->city, $this->state, $this->pincode])
            ->filter()
            ->implode(', ');
    }
}
