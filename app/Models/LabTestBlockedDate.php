<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabTestBlockedDate extends Model
{
    protected $fillable = ['franchise_id', 'date', 'reason', 'created_by'];

    protected $casts = [
        'date' => 'date',
    ];

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Null date is never valid to check against - always pass a real date. */
    public static function isBlocked(\DateTimeInterface $date): bool
    {
        return static::whereNull('franchise_id')
            ->whereDate('date', $date)
            ->exists();
    }
}
