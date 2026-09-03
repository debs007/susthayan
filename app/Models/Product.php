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
        'category_id', 'brand_id', 'name', 'slug', 'salt_composition', 'manufacturer',
        'hsn_code', 'drug_schedule', 'prescription_required', 'unit',
        'barcode', 'image_path', 'description', 'is_active',
    ];

    protected $casts = [
        'prescription_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
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

    /**
     * Full public URL, computed from the stored R2 object key - never
     * null-unsafe (returns null if no image was ever uploaded, which the
     * customer-facing ProductResource and the Flutter app's fallback
     * image system both already handle). config('filesystems.disks.r2.url')
     * is the public base URL (custom domain or r2.dev subdomain), kept
     * separate from the S3 API endpoint used for uploads - see
     * MANUAL_STEPS_README.md for why these are two different values.
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image_path === null) {
            return null;
        }

        $base = rtrim(config('filesystems.disks.r2.url', ''), '/');

        return $base !== '' ? "{$base}/{$this->image_path}" : null;
    }
}
