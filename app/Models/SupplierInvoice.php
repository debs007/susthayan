<?php

namespace App\Models;

use App\Enums\SupplierInvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class SupplierInvoice extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'supplier_id', 'purchase_order_id', 'goods_receipt_id', 'invoice_number',
        'invoice_date', 'invoice_amount', 'gst_amount', 'status', 'due_date',
    ];

    protected $casts = [
        'status' => SupplierInvoiceStatus::class,
        'invoice_date' => 'date',
        'due_date' => 'date',
        'invoice_amount' => 'decimal:2',
        'gst_amount' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty();
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
