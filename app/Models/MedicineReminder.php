<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicineReminder extends Model
{
    protected $fillable = ['user_id', 'medicine_name', 'dosage_note', 'times', 'start_date', 'end_date', 'is_active'];

    protected $casts = [
        'times' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
