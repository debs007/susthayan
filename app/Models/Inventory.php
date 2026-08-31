<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventory';

    protected $fillable = [
        'franchise_id', 'product_id', 'goods_receipt_item_id', 'batch_no',
        'expiry_date', 'quantity', 'reserved_quantity', 'purchase_rate',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'purchase_rate' => 'decimal:2',
    ];

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function goodsReceiptItem(): BelongsTo
    {
        return $this->belongsTo(GoodsReceiptItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orderItemBatches(): HasMany
    {
        return $this->hasMany(OrderItemBatch::class);
    }

    /** Physically present minus already-reserved - what's actually sellable right now. */
    public function getAvailableQuantityAttribute(): int
    {
        return $this->quantity - $this->reserved_quantity;
    }

    /** FEFO: earliest-expiry batches with sellable (unreserved) stock, for a product at a franchise. */
    public function scopeFefoFor($query, int $franchiseId, int $productId)
    {
        return $query->where('franchise_id', $franchiseId)
            ->where('product_id', $productId)
            ->whereColumn('quantity', '>', 'reserved_quantity')
            ->orderBy('expiry_date');
    }
}
