<?php

namespace App\Services\LabTests;

use App\Models\LabTest;
use App\Models\LabTestBlockedDate;
use App\Models\LabTestBooking;
use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LabTestBookingService
{
    /**
     * Same starting point as a product order (pending_payment) so the
     * existing PaymentController::initiate()/verify() flow works
     * completely unchanged - this only needed to produce an Order shaped
     * the way that flow already expects, not a parallel payment path.
     */
    public function book(User $user, LabTest $test, int $franchiseId, Carbon $scheduledDate, ?int $addressId): Order
    {
        if (LabTestBlockedDate::isBlocked($scheduledDate)) {
            throw new \RuntimeException('That date is not available for lab test bookings. Please choose another date.');
        }

        return DB::transaction(function () use ($user, $test, $franchiseId, $scheduledDate, $addressId) {
            $order = Order::create([
                'order_type' => 'lab_test',
                'user_id' => $user->id,
                'franchise_id' => $franchiseId,
                'status' => 'pending_payment',
                'fulfillment_type' => $test->requires_center_visit ? 'pickup' : 'delivery',
                'address_id' => $addressId,
                'subtotal_amount' => $test->price,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'delivery_charge' => 0,
                'total_amount' => $test->price,
            ]);

            LabTestBooking::create([
                'order_id' => $order->id,
                'lab_test_id' => $test->id,
                'user_id' => $user->id,
                'franchise_id' => $franchiseId,
                'address_id' => $addressId,
                'booking_type' => $test->requires_center_visit ? 'center_visit' : 'home_visit',
                'scheduled_date' => $scheduledDate,
                'status' => 'pending',
            ]);

            return $order;
        });
    }
}
