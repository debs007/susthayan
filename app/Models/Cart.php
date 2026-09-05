<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'franchise_id', 'coupon_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
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

    /**
     * Only discounts the line items the coupon actually applies to, not
     * the whole cart - a coupon scoped to specific products shouldn't
     * silently discount everything else someone happens to add alongside
     * them. Needs prices already resolved (priceFor) to know the real
     * line total per item, so this only works meaningfully after a
     * franchise has been selected.
     */
    public function couponDiscountAmount(): float
    {
        if ($this->coupon === null || ! $this->coupon->isCurrentlyValid()) {
            return 0;
        }

        $couponProductIds = $this->coupon->products()->pluck('products.id')->all();

        $qualifyingSubtotal = $this->items->loadMissing('product.prices')->sum(function (CartItem $item) use ($couponProductIds) {
            if (! in_array($item->product_id, $couponProductIds, true)) {
                return 0;
            }
            $price = $item->product->priceFor($this->franchise_id);

            return $price ? (float) $price->selling_price * $item->quantity : 0;
        });

        return $this->coupon->calculateDiscount($qualifyingSubtotal);
    }
}
