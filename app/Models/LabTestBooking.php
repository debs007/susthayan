<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabTestBooking extends Model
{
    protected $fillable = [
        'order_id', 'lab_test_id', 'lab_center_id', 'user_id', 'franchise_id', 'address_id',
        'booking_type', 'scheduled_date', 'status',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function labTest(): BelongsTo
    {
        return $this->belongsTo(LabTest::class);
    }

    public function labCenter(): BelongsTo
    {
        return $this->belongsTo(LabCenter::class);
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
}
