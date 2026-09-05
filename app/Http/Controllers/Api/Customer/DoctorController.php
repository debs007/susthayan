<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $doctors = Doctor::with('department')
            ->where('is_active', true)
            ->when($request->filled('department_id'), fn ($q) => $q->where('department_id', $request->integer('department_id')))
            ->orderBy('name')
            ->get();

        return response()->json(['data' => DoctorResource::collection($doctors)]);
    }

    public function show(Doctor $doctor): JsonResponse
    {
        abort_unless($doctor->is_active, 404);

        $doctor->load('department', 'affiliations.hospital', 'affiliations.visitDays');

        return response()->json(['data' => new DoctorResource($doctor)]);
    }
}
