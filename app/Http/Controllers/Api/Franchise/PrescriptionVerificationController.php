<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\VerifyPrescriptionRequest;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PrescriptionVerificationController extends Controller
{
    /** Pharmacist's review queue - unclaimed (order_id null, so from cart-time upload) and still pending. */
    public function index(): JsonResponse
    {
        $pending = Prescription::where('verification_status', 'pending')
            ->with('user:id,name,mobile')
            ->latest()
            ->get(['id', 'user_id', 'file_path', 'created_at']);

        return response()->json(['prescriptions' => $pending]);
    }

    public function verify(VerifyPrescriptionRequest $request, Prescription $prescription): JsonResponse
    {
        $prescription->update([
            'verification_status' => $request->validated('status'),
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'rejection_reason' => $request->validated('status') === 'rejected'
                ? $request->validated('rejection_reason')
                : null,
        ]);

        // If this prescription was already linked to an order (uploaded after
        // an order existed rather than pre-checkout), push the update live so
        // the customer isn't stuck refreshing to see the verdict.
        if ($prescription->order_id) {
            OrderStatusUpdated::dispatch($prescription->order->fresh());
        }

        return response()->json(['prescription' => $prescription]);
    }

    /** Same private-disk pattern as the web portal's equivalent - never a public URL, streamed only to an authenticated, role-checked pharmacist. */
    public function show(Prescription $prescription): Response
    {
        abort_unless(Storage::disk('local')->exists($prescription->file_path), 404);

        return response()->file(Storage::disk('local')->path($prescription->file_path));
    }
}
