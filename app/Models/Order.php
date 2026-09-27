<?php

namespace App\Models;

use App\Enums\FulfillmentType;
use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Order extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'order_type', 'user_id', 'franchise_id', 'address_id', 'fulfillment_type', 'status',
        'walk_in_customer_name', 'walk_in_customer_phone', 'prescription_note',
        'subtotal_amount', 'discount_amount', 'tax_amount', 'delivery_charge',
        'total_amount', 'requires_prescription', 'confirmed_at', 'prepared_at',
        'out_for_delivery_at', 'delivered_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'fulfillment_type' => FulfillmentType::class,
        'subtotal_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'requires_prescription' => 'boolean',
        'confirmed_at' => 'datetime',
        'prepared_at' => 'datetime',
        'out_for_delivery_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logOnlyDirty();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function franchise(): BelongsTo
    {
        return $this->belongsTo(Franchise::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /** The lab-test equivalent of items() - populated only when order_type is 'lab_test', null for a product order. */
    public function labTestBooking(): HasOne
    {
        return $this->hasOne(LabTestBooking::class);
    }

    /**
     * Additive alongside labTestBooking() above, not a replacement - that
     * HasOne is already relied on by the web storefront's order history,
     * the web admin order view, and the existing API response shape, so
     * it stays exactly as-is. This is the same lab_test_bookings table,
     * just the full list rather than one row - what a multi-test booking
     * actually needs, since several rows can now share one order_id.
     */
    public function labTestBookings(): HasMany
    {
        return $this->hasMany(LabTestBooking::class);
    }

    /** Same pattern as labTestBooking() - the appointment equivalent, null for a product or lab-test order. */
    public function appointmentBooking(): HasOne
    {
        return $this->hasOne(AppointmentBooking::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class);
    }

    public function deliveryAssignments(): HasMany
    {
        return $this->hasMany(DeliveryAssignment::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    /**
     * Payment != revenue until fulfilled (SRS key rule) - except POS, which
     * the SRS explicitly calls out as an immediate final sale. POS sales
     * are created directly in the PickedUp status (see PosSaleService) so
     * this needs no special-casing: it's already true the instant they're
     * created, matching "Revenue recognized immediately."
     */
    public function isRevenueRecognised(): bool
    {
        return $this->status->isRevenueRecognised();
    }
}
