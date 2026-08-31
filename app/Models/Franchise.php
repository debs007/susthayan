<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Franchise extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name', 'slug', 'gstin', 'drug_license_number', 'address', 'city',
        'state', 'pincode', 'latitude', 'longitude', 'phone', 'email',
        'commission_percentage', 'bank_account_name', 'bank_account_number',
        'bank_ifsc', 'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        // The platform's cut of this franchise's revenue, not the
        // franchise's own margin - see SettlementService, where
        // net_payable = gross_sales * (1 - commission_percentage / 100).
        'commission_percentage' => 'decimal:2',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function goodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(FranchiseSettlement::class);
    }
}
