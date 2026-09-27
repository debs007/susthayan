<?php

namespace App\Services\Appointments;

use App\Enums\PaymentStatus;
use App\Models\AppointmentBooking;
use App\Models\CustomerPayment;
use App\Models\Doctor;
use App\Models\DoctorHospitalAffiliation;
use App\Models\Order;
use App\Models\User;
use App\Services\Payment\PaymentConfirmationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AppointmentBookingService
{
    public function __construct(private readonly PaymentConfirmationService $paymentConfirmation) {}

    /**
     * Same starting point as a product/lab-test order (pending_payment) -
     * "appointment booking should also be considered as order" is
     * literal here, so this needed to produce an Order the existing
     * payment flow already knows how to handle, not a new payment path.
     */
    public function book(User $user, Doctor $doctor, DoctorHospitalAffiliation $affiliation, Carbon $scheduledDate): Order
    {
        $dayOfWeek = strtolower($scheduledDate->format('l'));
        $visitDays = $affiliation->visitDays()->pluck('day_of_week');

        if (! $visitDays->contains($dayOfWeek)) {
            throw new \RuntimeException("Dr. {$doctor->name} doesn't visit this hospital on {$scheduledDate->format('l')}s. Please choose a different date.");
        }

        $alreadyBooked = AppointmentBooking::where('doctor_id', $doctor->id)
            ->where('hospital_id', $affiliation->hospital_id)
            ->where('scheduled_date', $scheduledDate->toDateString())
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($alreadyBooked) {
            throw new \RuntimeException('You already have an appointment with this doctor at this hospital on this date.');
        }

        $charge = (float) $affiliation->consultation_charge;

        return DB::transaction(function () use ($user, $doctor, $affiliation, $scheduledDate, $charge) {
            $order = Order::create([
                'order_type' => 'appointment',
                'user_id' => $user->id,
                'franchise_id' => $affiliation->hospital->franchise_id,
                'status' => 'pending_payment',
                'fulfillment_type' => 'pickup', // the customer visits the hospital in person, same reasoning as a lab center visit
                'address_id' => null,
                'subtotal_amount' => $charge,
                'discount_amount' => 0,
                'tax_amount' => 0,
                'delivery_charge' => 0,
                'total_amount' => $charge,
            ]);

            AppointmentBooking::create([
                'order_id' => $order->id,
                'doctor_id' => $doctor->id,
                'hospital_id' => $affiliation->hospital_id,
                'user_id' => $user->id,
                'franchise_id' => $affiliation->hospital->franchise_id,
                'scheduled_date' => $scheduledDate,
                'status' => 'pending',
            ]);

            // Temporary: payments are off (config('services.payment.enabled')
            // false) - same bypass CheckoutService and LabTestBookingService
            // already apply, run through the exact same confirmation path.
            if (! config('services.payment.enabled')) {
                $payment = CustomerPayment::create([
                    'order_id' => $order->id,
                    'amount' => $order->total_amount,
                    'gateway' => 'bypassed',
                    'status' => PaymentStatus::Initiated,
                ]);

                $this->paymentConfirmation->markSuccessful($payment);
            }

            // Same reasoning as LabTestBookingService - markSuccessful()
            // above updates the order via $payment->order, a separately
            // lazy-loaded instance, so this $order variable's own
            // in-memory status never reflects that update without an
            // explicit refresh.
            return $order->fresh();
        });
    }
}
