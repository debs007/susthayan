<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, LogsActivity, Notifiable, SoftDeletes;

    protected $fillable = [
        'franchise_id', 'name', 'mobile', 'alternate_mobile', 'email', 'password',
        'profile_image_path', 'two_factor_enabled', 'is_active', 'dob', 'gender',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'mobile_verified_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_enabled' => 'boolean',
        'is_active' => 'boolean',
        'dob' => 'date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty()->logExcept(['password', 'remember_token']);
    }

    /** Same pattern as Product::getImageUrlAttribute() - computed from the stored R2 key, never null-unsafe. */
    public function getProfileImageUrlAttribute(): ?string
    {
        if ($this->profile_image_path === null) {
            return null;
        }

        $base = rtrim(config('filesystems.disks.r2.url', ''), '/');

        return $base !== '' ? "{$base}/{$this->profile_image_path}" : null;
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function verifiedPrescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'verified_by');
    }

    public function deliveryAssignments(): HasMany
    {
        return $this->hasMany(DeliveryAssignment::class, 'delivery_agent_id');
    }

    public function healthProfile(): HasOne
    {
        return $this->hasOne(HealthProfile::class);
    }

    public function vitals(): HasMany
    {
        return $this->hasMany(Vital::class);
    }

    public function healthRecords(): HasMany
    {
        return $this->hasMany(HealthRecord::class);
    }

    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    public function appointmentBookings(): HasMany
    {
        return $this->hasMany(AppointmentBooking::class);
    }

    public function labTestBookings(): HasMany
    {
        return $this->hasMany(LabTestBooking::class);
    }

    /** Named distinctly from Notifiable's own notifications() (Laravel's built-in DatabaseNotification) - this app's in-app notifications are a completely separate, custom model. */
    public function appNotifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }
}
