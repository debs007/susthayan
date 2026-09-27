<?php

namespace App\Services\LabTests;

use App\Enums\PaymentStatus;
use App\Models\CustomerPayment;
use App\Models\LabCenter;
use App\Models\LabTest;
use App\Models\LabTestBlockedDate;
use App\Models\LabTestBooking;
use App\Models\Order;
use App\Models\User;
use App\Services\Payment\PaymentConfirmationService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LabTestBookingService
{
    public function __construct(private readonly PaymentConfirmationService $paymentConfirmation) {}

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
     *
     * $tests is always a collection now, even for a single test - one
     * booking under one order, same as before, just the one-element
     * case of the same code path rather than a separate one.
     */
    public function book(User $user, Collection $tests, LabCenter $center, Carbon $scheduledDate, ?int $addressId): Order
    {
        if (LabTestBlockedDate::isBlocked($scheduledDate)) {
            throw new \RuntimeException('That date is not available for lab test bookings. Please choose another date.');
        }

        // The center's own price for each specific test, not the test's
        // base price - different centers can genuinely charge differently
        // for the same test, and this is the number actually charged.
        // Resolved for every test up front so a booking never partially
        // succeeds only to fail on a later item in the loop.
        $prices = $tests->mapWithKeys(function (LabTest $test) use ($center) {
            $price = $center->tests()->where('lab_tests.id', $test->id)->first()?->pivot->price;

            if ($price === null) {
                throw new \RuntimeException("That center does not currently offer {$test->name}.");
            }

            return [$test->id => $price];
        });

        $totalPrice = $prices->sum();

        // If any selected test needs an in-person visit, the whole
        // booking is treated as a center visit - the customer is going
        // there anyway for that one, so there's no meaningfully
        // separate "delivery" experience for the rest of the batch.
        $anyCenterVisit = $tests->contains(fn (LabTest $test) => $test->requires_center_visit);

        return DB::transaction(function () use ($user, $tests, $center, $scheduledDate, $addressId, $prices, $totalPrice, $anyCenterVisit) {
            $order = Order::create([
                'order_type' => 'lab_test',
                'user_id' => $user->id,
                'franchise_id' => $center->franchise_id,
                'status' => 'pending_payment',
                'fulfillment_type' => $anyCenterVisit ? 'pickup' : 'delivery',
                'address_id' => $addressId,
                'subtotal_amount' => $totalPrice,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'delivery_charge' => 0,
                'total_amount' => $totalPrice,
            ]);

            foreach ($tests as $test) {
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
            }

            // Temporary: payments are off (config('services.payment.enabled')
            // false) - same bypass CheckoutService already applies for a
            // product order, run through the exact same confirmation path
            // rather than a parallel one.
            if (! config('services.payment.enabled')) {
                $payment = CustomerPayment::create([
                    'order_id' => $order->id,
                    'amount' => $order->total_amount,
                    'gateway' => 'bypassed',
                    'status' => PaymentStatus::Initiated,
                ]);

                $this->paymentConfirmation->markSuccessful($payment);
            }

            // markSuccessful() above updates the order via $payment->order,
            // a separately lazy-loaded instance - it persists correctly to
            // the database, but this $order variable's own in-memory
            // status never reflects that update on its own. Without this,
            // the caller (and the app) would see a stale 'pending_payment'
            // status on an order that's actually already confirmed.
            return $order->fresh();
        });
    }
}
