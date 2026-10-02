<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Order */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_type' => $this->order_type,
            'status' => $this->status->value,
            'fulfillment_type' => $this->fulfillment_type->value,
            'franchise' => $this->whenLoaded('franchise', fn () => $this->franchise ? [
                'id' => $this->franchise->id,
                'name' => $this->franchise->name,
            ] : null),
            'customer' => $this->fulfillment_type->value === 'pos' ? [
                'walk_in_name' => $this->walk_in_customer_name,
                'walk_in_phone' => $this->walk_in_customer_phone,
            ] : $this->whenLoaded('user', fn () => $this->user ? [
                'name' => $this->user->name,
                'mobile' => $this->user->mobile,
            ] : null),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            // Populated only for order_type = 'lab_test' - the equivalent
            // of 'items' above for a product order. A lab test order has
            // no order_items at all, so without this the app had nothing
            // to show for what was actually booked.
            'lab_test_booking' => $this->whenLoaded('labTestBooking', fn () => $this->labTestBooking ? [
                'test_name' => $this->labTestBooking->labTest?->name,
                'center_name' => $this->labTestBooking->labCenter?->name,
                'center_address' => $this->labTestBooking->labCenter?->fullAddress(),
                'booking_type' => $this->labTestBooking->booking_type,
                'scheduled_date' => $this->labTestBooking->scheduled_date?->toDateString(),
                'status' => $this->labTestBooking->status,
            ] : null),
            // Additive alongside lab_test_booking above, not a replacement -
            // populated for every lab-test order (including a single-test
            // one, as a one-item list) once the app requests this relation
            // loaded. Lets a multi-test booking show every test, not just
            // whichever one labTestBooking's HasOne happens to resolve to.
            'lab_test_bookings' => $this->whenLoaded('labTestBookings', fn () => $this->labTestBookings->map(fn ($booking) => [
                'test_name' => $booking->labTest?->name,
                'center_name' => $booking->labCenter?->name,
                'center_address' => $booking->labCenter?->fullAddress(),
                'booking_type' => $booking->booking_type,
                'scheduled_date' => $booking->scheduled_date?->toDateString(),
                'status' => $booking->status,
            ])),
            // Same pattern as lab_test_booking above - the equivalent for
            // order_type = 'appointment'. Without this, an appointment
            // order would hit the exact same "no item details" bug the
            // lab test bookings had before that was fixed.
            'appointment_booking' => $this->whenLoaded('appointmentBooking', fn () => $this->appointmentBooking ? [
                'doctor_name' => $this->appointmentBooking->doctor?->name,
                'doctor_degree' => $this->appointmentBooking->doctor?->degree,
                'hospital_name' => $this->appointmentBooking->hospital?->name,
                'hospital_address' => $this->appointmentBooking->hospital?->fullAddress(),
                'scheduled_date' => $this->appointmentBooking->scheduled_date?->toDateString(),
                'status' => $this->appointmentBooking->status,
            ] : null),
            'subtotal_amount' => (string) $this->subtotal_amount,
            'discount_amount' => (string) $this->discount_amount,
            'tax_amount' => (string) $this->tax_amount,
            'delivery_charge' => (string) $this->delivery_charge,
            'total_amount' => (string) $this->total_amount,
            'requires_prescription' => $this->requires_prescription,
            'prescription_note' => $this->prescription_note,
            'placed_at' => $this->created_at?->toIso8601String(),
            'confirmed_at' => $this->confirmed_at?->toIso8601String(),
            'prepared_at' => $this->prepared_at?->toIso8601String(),
            'out_for_delivery_at' => $this->out_for_delivery_at?->toIso8601String(),
            'delivered_at' => $this->delivered_at?->toIso8601String(),
        ];
    }
}
