<?php

namespace App\Http\Controllers\Web\Franchise;

use App\Events\OrderStatusUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\VerifyPrescriptionRequest;
use App\Models\Prescription;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PrescriptionController extends Controller
{
    public function index(): View
    {
        $pending = Prescription::where('verification_status', 'pending')
            ->with('user:id,name,mobile')
            ->latest()
            ->get();

        return view('franchise.prescriptions.index', compact('pending'));
    }

    public function verify(VerifyPrescriptionRequest $request, Prescription $prescription): RedirectResponse
    {
        $prescription->update([
            'verification_status' => $request->validated('status'),
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'rejection_reason' => $request->validated('status') === 'rejected'
                ? $request->validated('rejection_reason')
                : null,
        ]);

        if ($prescription->order_id) {
            OrderStatusUpdated::dispatch($prescription->order->fresh());
        }

        return redirect()->route('franchise.prescriptions.index')->with('success', 'Prescription '.$request->validated('status').'.');
    }

    /** Prescriptions live on a private disk on purpose - never a public URL. This streams the file only to an authenticated, role-checked pharmacist. */
    public function show(Prescription $prescription): Response
    {
        abort_unless(Storage::disk('local')->exists($prescription->file_path), 404);

        return response()->file(Storage::disk('local')->path($prescription->file_path));
    }
}
