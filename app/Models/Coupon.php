<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'is_global', 'description', 'discount_type', 'discount_value',
        'max_discount_amount', 'valid_from', 'valid_until', 'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'coupon_product')->withTimestamps();
    }

    public function homeBanners(): HasMany
    {
        return $this->hasMany(HomeBanner::class);
    }

    public function isCurrentlyValid(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $today = now()->toDateString();

        if ($this->valid_from !== null && $today < $this->valid_from->toDateString()) {
            return false;
        }

        if ($this->valid_until !== null && $today > $this->valid_until->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * The single source of truth for what this coupon takes off a given
     * amount - used both to show a discounted price on a product listing
     * and to actually compute the cart-level discount at checkout, so
     * those two numbers can never drift apart from each other.
     */
    public function calculateDiscount(float $amount): float
    {
        $discount = $this->discount_type === 'percentage'
            ? $amount * ((float) $this->discount_value / 100)
            : (float) $this->discount_value;

        if ($this->max_discount_amount !== null) {
            $discount = min($discount, (float) $this->max_discount_amount);
        }

        return min($discount, $amount); // never discount more than the amount itself
    }
}
