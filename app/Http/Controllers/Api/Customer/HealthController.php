<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\RecordVitalsRequest;
use App\Http\Requests\Customer\UpdateHealthProfileRequest;
use App\Http\Requests\Customer\UploadHealthRecordRequest;
use App\Http\Resources\HealthProfileResource;
use App\Http\Resources\HealthRecordResource;
use App\Http\Resources\VitalResource;
use App\Models\HealthProfile;
use App\Models\HealthRecord;
use App\Models\Prescription;
use App\Models\Vital;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class HealthController extends Controller
{
    public function showProfile(Request $request): JsonResponse
    {
        $profile = HealthProfile::firstOrNew(['user_id' => $request->user()->id]);

        return response()->json(['profile' => new HealthProfileResource($profile)]);
    }

    public function updateProfile(UpdateHealthProfileRequest $request): JsonResponse
    {
        $profile = HealthProfile::updateOrCreate(
            ['user_id' => $request->user()->id],
            $request->validated()
        );

        return response()->json(['profile' => new HealthProfileResource($profile)]);
    }

    public function indexVitals(Request $request): JsonResponse
    {
        $vitals = Vital::where('user_id', $request->user()->id)
            ->latest('recorded_at')
            ->limit(50)
            ->get();

        return response()->json(['data' => VitalResource::collection($vitals)]);
    }

    public function storeVitals(RecordVitalsRequest $request): JsonResponse
    {
        $vital = Vital::create([
            'user_id' => $request->user()->id,
            ...$request->validated(),
            'recorded_at' => $request->validated('recorded_at') ?? now(),
        ]);

        return response()->json(['vital' => new VitalResource($vital)], 201);
    }

    public function destroyVitals(Request $request, Vital $vital): JsonResponse
    {
        abort_if($vital->user_id !== $request->user()->id, 403);

        $vital->delete();

        return response()->json(['message' => 'Deleted.']);
    }

    /**
     * Merges the new health_records table with real Prescription data
     * into one chronological list - prescriptions were never duplicated
     * into health_records, they're normalized to the same shape here
     * instead, so there's exactly one source of truth for prescription
     * data.
     */
    public function indexRecords(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $records = HealthRecord::where('user_id', $userId)
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->get()
            ->map(fn (HealthRecord $record) => (new HealthRecordResource($record))->resolve());

        $includePrescriptions = ! $request->filled('type') || $request->string('type') === 'prescription';

        $prescriptions = $includePrescriptions
            ? Prescription::where('user_id', $userId)->get()->map(fn (Prescription $p) => [
                'id' => 'prescription-'.$p->id,
                'type' => 'prescription',
                'title' => 'Prescription',
                'has_file' => true,
                'record_date' => $p->created_at->toDateString(),
                'notes' => $p->verification_status === 'rejected' ? $p->rejection_reason : null,
            ])
            : collect();

        $combined = $records->concat($prescriptions)
            ->sortByDesc('record_date')
            ->values();

        return response()->json(['data' => $combined]);
    }

    public function storeRecord(UploadHealthRecordRequest $request): JsonResponse
    {
        $filePath = null;
        if ($request->hasFile('file')) {
            // Private disk, same convention as prescriptions - never a
            // public URL.
            $filePath = $request->file('file')->store('health-records', 'local');
        }

        $record = HealthRecord::create([
            'user_id' => $request->user()->id,
            'type' => $request->validated('type'),
            'title' => $request->validated('title'),
            'file_path' => $filePath,
            'record_date' => $request->validated('record_date'),
            'notes' => $request->validated('notes'),
        ]);

        return response()->json(['record' => new HealthRecordResource($record)], 201);
    }

    /** Same pattern as PrescriptionController::show() - private disk, streamed only to the authenticated owner, never a public URL. */
    public function showRecordFile(Request $request, HealthRecord $healthRecord): Response
    {
        abort_if($healthRecord->user_id !== $request->user()->id, 403);
        abort_if($healthRecord->file_path === null, 404);
        abort_unless(Storage::disk('local')->exists($healthRecord->file_path), 404);

        return response()->file(Storage::disk('local')->path($healthRecord->file_path));
    }

    /**
     * Scoped to real HealthRecord rows only - indexRecords() also merges
     * in Prescription entries under the same "record" shape (with a
     * composite id like "prescription-5"), but those are a completely
     * separate model with their own lifecycle and aren't deletable
     * through this endpoint at all - the route parameter binding itself
     * already enforces this, since a "prescription-5" id could never
     * resolve to a real HealthRecord row.
     */
    public function destroyRecord(Request $request, HealthRecord $healthRecord): JsonResponse
    {
        abort_if($healthRecord->user_id !== $request->user()->id, 403);

        if ($healthRecord->file_path !== null) {
            Storage::disk('local')->delete($healthRecord->file_path);
        }

        $healthRecord->delete();

        return response()->json(['message' => 'Deleted.']);
    }
}
