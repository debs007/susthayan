<?php

namespace App\Models;

use App\Enums\FranchiseSettlementStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class FranchiseSettlement extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'franchise_id', 'period_start', 'period_end', 'gross_sales',
        'commission_amount', 'net_payable', 'status', 'paid_at', 'payout_reference',
    ];

    protected $casts = [
        'status' => FranchiseSettlementStatus::class,
        'period_start' => 'date',
        'period_end' => 'date',
        'gross_sales' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_payable' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty();
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }
}
