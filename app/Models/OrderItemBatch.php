<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** The actual FEFO allocation for a fulfilled order line - see migration comment. */
class OrderItemBatch extends Model
{
    use HasFactory;

    protected $fillable = ['order_item_id', 'inventory_id', 'quantity'];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
