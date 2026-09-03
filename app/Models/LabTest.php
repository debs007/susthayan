<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LabTest extends Model
{
    protected $fillable = [
        'lab_test_category_id', 'name', 'slug', 'description', 'sample_type',
        'preparation_instructions', 'price', 'requires_center_visit', 'duration_minutes', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'requires_center_visit' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(LabTestCategory::class, 'lab_test_category_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(LabTestBooking::class);
    }
}
