<?php

namespace App\Models;

use App\Enums\SupplierPaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class SupplierPayment extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'supplier_id', 'supplier_invoice_id', 'amount_paid',
        'payment_date', 'payment_mode', 'reference_number', 'status',
    ];

    protected $casts = [
        'status' => SupplierPaymentStatus::class,
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }
}
