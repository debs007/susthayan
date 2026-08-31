<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'franchise_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function requiresPrescription(): bool
    {
        return $this->items->loadMissing('product')->contains(
            fn (CartItem $item) => $item->product->prescription_required
        );
    }
}
