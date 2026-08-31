<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UploadPrescriptionRequest;
use App\Models\Prescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function store(UploadPrescriptionRequest $request): JsonResponse
    {
        // 'local' disk - private by default, never served by a public URL.
        // For production, point FILESYSTEM_DISK at S3 with server-side
        // encryption (see .env.example) rather than the local filesystem.
        $path = $request->file('file')->store('prescriptions/'.$request->user()->id, 'local');

        $prescription = Prescription::create([
            'user_id' => $request->user()->id,
            'file_path' => $path,
            'verification_status' => 'pending',
        ]);

        return response()->json([
            'id' => $prescription->id,
            'verification_status' => $prescription->verification_status,
            'message' => 'Uploaded - a pharmacist will review it shortly.',
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $prescriptions = Prescription::where('user_id', $request->user()->id)
            ->latest()
            ->get(['id', 'order_id', 'verification_status', 'rejection_reason', 'verified_at', 'created_at']);

        return response()->json(['prescriptions' => $prescriptions]);
    }
}
