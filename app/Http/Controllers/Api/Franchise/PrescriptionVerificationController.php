<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\VerifyPrescriptionRequest;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    /** Past approved prescriptions this pharmacist reviewed - not rejected, matching "approved prescriptions" as asked. */
    public function history(Request $request): JsonResponse
    {
        $approved = Prescription::where('verification_status', 'approved')
            ->where('verified_by', $request->user()->id)
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('verified_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('verified_at', '<=', $request->date('date_to')))
            ->with('user:id,name,mobile')
            ->orderByDesc('verified_at')
            ->get(['id', 'user_id', 'file_path', 'created_at', 'verified_at']);

        return response()->json(['prescriptions' => $approved]);
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

        // Only on approval - replaces rather than appends, so correcting
        // a transcription by re-submitting doesn't leave stale lines
        // behind from an earlier attempt.
        if ($request->validated('status') === 'approved' && $request->filled('medicines')) {
            $prescription->medicines()->delete();
            $prescription->medicines()->createMany(
                collect($request->validated('medicines'))->map(fn ($name) => ['medicine_name' => $name])->all()
            );
        }

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
