<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'name', 'slug', 'salt_composition', 'manufacturer',
        'hsn_code', 'drug_schedule', 'prescription_required', 'unit',
        'barcode', 'description', 'is_active',
    ];

    protected $casts = [
        'prescription_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function purchaseOrderItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function goodsReceiptItems(): HasMany
    {
        return $this->hasMany(GoodsReceiptItem::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** Resolve the effective selling price for a given franchise (falls back to the global price). */
    public function priceFor(?int $franchiseId): ?ProductPrice
    {
        if ($this->relationLoaded('prices')) {
            return $this->prices->firstWhere('franchise_id', $franchiseId)
                ?? $this->prices->firstWhere('franchise_id', null);
        }

        return $this->prices()->where('franchise_id', $franchiseId)->first()
            ?? $this->prices()->whereNull('franchise_id')->first();
    }
}
