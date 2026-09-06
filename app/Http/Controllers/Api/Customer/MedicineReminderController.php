<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\CreateMedicineReminderRequest;
use App\Http\Resources\MedicineReminderResource;
use App\Models\MedicineReminder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedicineReminderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reminders = MedicineReminder::where('user_id', $request->user()->id)
            ->where('is_active', true)
            ->orderBy('medicine_name')
            ->get();

        return response()->json(['data' => MedicineReminderResource::collection($reminders)]);
    }

    public function store(CreateMedicineReminderRequest $request): JsonResponse
    {
        $reminder = MedicineReminder::create([
            'user_id' => $request->user()->id,
            ...$request->validated(),
            'is_active' => true,
        ]);

        return (new MedicineReminderResource($reminder))->response()->setStatusCode(201);
    }

    public function update(CreateMedicineReminderRequest $request, MedicineReminder $medicineReminder): JsonResponse
    {
        abort_if($medicineReminder->user_id !== $request->user()->id, 403);

        $medicineReminder->update($request->validated());

        return response()->json(['data' => new MedicineReminderResource($medicineReminder)]);
    }

    /**
     * Soft "stop" rather than delete - is_active=false, and index()
     * already filters to active-only, so a stopped reminder simply
     * disappears from the app (functionally identical to delete from
     * the customer's point of view) while the row itself is preserved
     * rather than permanently lost.
     */
    public function destroy(Request $request, MedicineReminder $medicineReminder): JsonResponse
    {
        abort_if($medicineReminder->user_id !== $request->user()->id, 403);

        $medicineReminder->update(['is_active' => false]);

        return response()->json(['message' => 'Reminder stopped.']);
    }
}
