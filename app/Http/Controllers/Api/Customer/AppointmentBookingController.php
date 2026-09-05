<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateAppointmentBookingRequest;
use App\Http\Resources\AppointmentBookingResource;
use App\Http\Resources\OrderResource;
use App\Models\AppointmentBooking;
use App\Models\Doctor;
use App\Models\DoctorHospitalAffiliation;
use App\Services\Appointments\AppointmentBookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentBookingController extends Controller
{
    public function __construct(private readonly AppointmentBookingService $bookings) {}

    /**
     * Returns an Order, not a booking-specific shape - same reasoning as
     * LabTestBookingController: the Flutter app reuses its existing
     * OrderProvider.initiatePayment()/verifyPayment() flow unchanged.
     */
    public function store(CreateAppointmentBookingRequest $request): JsonResponse
    {
        $doctor = Doctor::findOrFail($request->validated('doctor_id'));
        $affiliation = DoctorHospitalAffiliation::with('hospital')->findOrFail($request->validated('affiliation_id'));

        abort_unless($affiliation->doctor_id === $doctor->id, 422, 'That hospital is not associated with this doctor.');

        try {
            $order = $this->bookings->book(
                user: $request->user(),
                doctor: $doctor,
                affiliation: $affiliation,
                scheduledDate: Carbon::parse($request->validated('scheduled_date')),
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return (new OrderResource($order->load(['franchise', 'appointmentBooking.doctor', 'appointmentBooking.hospital'])))->response()->setStatusCode(201);
    }

    public function index(Request $request): JsonResponse
    {
        $bookings = AppointmentBooking::where('user_id', $request->user()->id)
            ->with('doctor', 'hospital')
            ->latest('scheduled_date')
            ->get();

        return response()->json(['data' => AppointmentBookingResource::collection($bookings)]);
    }
}
