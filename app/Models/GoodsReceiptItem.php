<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GoodsReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receipt_id', 'product_id', 'batch_no', 'expiry_date',
        'received_qty', 'damaged_qty', 'purchase_rate', 'mrp',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'purchase_rate' => 'decimal:2',
        'mrp' => 'decimal:2',
    ];

    public function goodsReceipt(): BelongsTo
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** The stock record this receipt line created - only GRN increases inventory. */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }
}
