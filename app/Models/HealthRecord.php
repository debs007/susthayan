<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthRecord extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'file_path', 'record_date', 'notes',
    ];

    protected $casts = [
        'record_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
