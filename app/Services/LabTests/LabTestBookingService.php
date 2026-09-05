<?php

namespace App\Services\LabTests;

use App\Models\LabCenter;
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
     *
     * franchise_id on the order/booking is derived from the selected
     * center, not passed in separately - the customer never picks a
     * franchise directly, only a center, and that center's operating
     * franchise is what gets credited for settlement.
     */
    public function book(User $user, LabTest $test, LabCenter $center, Carbon $scheduledDate, ?int $addressId): Order
    {
        if (LabTestBlockedDate::isBlocked($scheduledDate)) {
            throw new \RuntimeException('That date is not available for lab test bookings. Please choose another date.');
        }

        // The center's own price for this specific test, not the test's
        // base price - different centers can genuinely charge differently
        // for the same test, and this is the number actually charged.
        $price = $center->tests()->where('lab_tests.id', $test->id)->first()?->pivot->price;

        if ($price === null) {
            throw new \RuntimeException('That center does not currently offer this test.');
        }

        return DB::transaction(function () use ($user, $test, $center, $scheduledDate, $addressId, $price) {
            $order = Order::create([
                'order_type' => 'lab_test',
                'user_id' => $user->id,
                'franchise_id' => $center->franchise_id,
                'status' => 'pending_payment',
                'fulfillment_type' => $test->requires_center_visit ? 'pickup' : 'delivery',
                'address_id' => $addressId,
                'subtotal_amount' => $price,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'delivery_charge' => 0,
                'total_amount' => $price,
            ]);

            LabTestBooking::create([
                'order_id' => $order->id,
                'lab_test_id' => $test->id,
                'lab_center_id' => $center->id,
                'user_id' => $user->id,
                'franchise_id' => $center->franchise_id,
                'address_id' => $addressId,
                'booking_type' => $test->requires_center_visit ? 'center_visit' : 'home_visit',
                'scheduled_date' => $scheduledDate,
                'status' => 'pending',
            ]);

            return $order;
        });
    }
}
