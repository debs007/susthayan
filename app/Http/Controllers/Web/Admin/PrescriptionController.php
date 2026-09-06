<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PrescriptionController extends Controller
{
    /**
     * franchise.prescriptions.index only ever shows 'pending' ones (it's
     * a work queue for pharmacists to act on) - this shows every
     * prescription regardless of status, for oversight of what's already
     * been decided, not just what's still waiting.
     */
    public function index(Request $request): View
    {
        $prescriptions = Prescription::with(['user:id,name,mobile', 'verifiedBy:id,name'])
            ->when($request->filled('status'), fn ($q) => $q->where('verification_status', $request->string('status')))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.prescriptions.index', compact('prescriptions'));
    }

    /** Same private-disk pattern as franchise.prescriptions.show - never a public URL, streamed only to an authenticated admin. */
    public function show(Prescription $prescription): Response
    {
        abort_unless(Storage::disk('local')->exists($prescription->file_path), 404);

        return response()->file(Storage::disk('local')->path($prescription->file_path));
    }
}
