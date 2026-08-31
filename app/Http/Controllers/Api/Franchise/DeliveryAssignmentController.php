<?php

namespace App\Http\Controllers\Api\Franchise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\AssignDeliveryRequest;
use App\Http\Resources\DeliveryAssignmentResource;
use App\Models\DeliveryAssignment;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DeliveryAssignmentController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $assignments = DeliveryAssignment::whereHas(
            'order',
            fn ($query) => $query->where('franchise_id', $request->user()->franchise_id)
        )->with(['order.items.product', 'deliveryAgent:id,name,mobile'])
            ->latest()
            ->paginate(20);

        return DeliveryAssignmentResource::collection($assignments);
    }

    // franchise.scope already confirmed $order belongs to the caller's franchise.
    public function store(AssignDeliveryRequest $request, Order $order): JsonResponse
    {
        if ($order->status->value !== 'ready_for_dispatch') {
            return response()->json([
                'message' => 'Only a ready-for-dispatch order can be assigned to a delivery agent.',
            ], 422);
        }

        if ($order->fulfillment_type->value !== 'delivery') {
            return response()->json([
                'message' => 'This is a pickup order - it doesn\'t need a delivery assignment.',
            ], 422);
        }

        if ($order->deliveryAssignments()->exists()) {
            return response()->json(['message' => 'This order already has a delivery assignment.'], 422);
        }

        $assignment = DeliveryAssignment::create([
            'order_id' => $order->id,
            'delivery_agent_id' => $request->validated('delivery_agent_id'),
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        return (new DeliveryAssignmentResource($assignment->load('order', 'deliveryAgent')))
            ->response()
            ->setStatusCode(201);
    }
}
