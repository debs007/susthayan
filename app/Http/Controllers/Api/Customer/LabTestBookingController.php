<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateLabTestBookingRequest;
use App\Http\Resources\LabTestBookingResource;
use App\Http\Resources\OrderResource;
use App\Models\LabCenter;
use App\Models\LabTest;
use App\Models\LabTestBooking;
use App\Services\LabTests\LabTestBookingService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LabTestBookingController extends Controller
{
    public function __construct(private readonly LabTestBookingService $bookings) {}

    /**
     * Returns an Order, not a booking-specific shape - the Flutter app
     * reuses its existing OrderProvider.initiatePayment()/verifyPayment()
     * flow completely unchanged from here, the same way it already does
     * for a product order.
     */
    public function store(CreateLabTestBookingRequest $request): JsonResponse
    {
        $test = LabTest::findOrFail($request->validated('lab_test_id'));
        $center = LabCenter::findOrFail($request->validated('lab_center_id'));

        try {
            $order = $this->bookings->book(
                user: $request->user(),
                test: $test,
                center: $center,
                scheduledDate: Carbon::parse($request->validated('scheduled_date')),
                addressId: $request->validated('address_id'),
            );
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return (new OrderResource($order->load(['franchise', 'labTestBooking.labTest', 'labTestBooking.labCenter'])))->response()->setStatusCode(201);
    }

    public function index(Request $request): JsonResponse
    {
        $bookings = LabTestBooking::where('user_id', $request->user()->id)
            ->with('labTest')
            ->latest('scheduled_date')
            ->get();

        return response()->json(['data' => LabTestBookingResource::collection($bookings)]);
    }
}
