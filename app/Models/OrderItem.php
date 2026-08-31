<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id', 'inventory_id', 'quantity',
        'unit_price', 'tax_percentage', 'total_price',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Null until fulfillment, when FEFO picks the actual batch. */
    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    /** The actual FEFO allocation(s) once this line is fulfilled - see OrderItemBatch. */
    public function batches(): HasMany
    {
        return $this->hasMany(OrderItemBatch::class);
    }
}
