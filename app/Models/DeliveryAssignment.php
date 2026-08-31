<?php

namespace App\Models;

use App\Enums\DeliveryAssignmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class DeliveryAssignment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'order_id', 'delivery_agent_id', 'status', 'failure_reason', 'assigned_at',
        'picked_up_at', 'delivered_at', 'proof_of_delivery_path',
    ];

    protected $casts = [
        'status' => DeliveryAssignmentStatus::class,
        'assigned_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty();
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_agent_id');
    }
}
