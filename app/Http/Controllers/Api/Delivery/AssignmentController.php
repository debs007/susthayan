<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Delivery\MarkDeliveredRequest;
use App\Http\Requests\Delivery\MarkFailedRequest;
use App\Http\Resources\DeliveryAssignmentResource;
use App\Models\DeliveryAssignment;
use App\Services\Invoicing\InvoiceService;
use App\Services\Orders\OrderFulfillmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use RuntimeException;

/**
 * franchise.scope (applied to this whole route group) can't help here -
 * DeliveryAssignment has no franchise_id column, since the real boundary
 * for a delivery agent isn't "my franchise" but "my own assignments". Every
 * action below checks delivery_agent_id === the calling user explicitly;
 * that check is the actual security boundary on this controller.
 */
class AssignmentController extends Controller
{
    public function __construct(
        private readonly OrderFulfillmentService $fulfillment,
        private readonly InvoiceService $invoices,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $assignments = DeliveryAssignment::where('delivery_agent_id', $request->user()->id)
            ->whereIn('status', ['assigned', 'picked_up', 'out_for_delivery'])
            ->with(['order.items.product', 'order.address'])
            ->orderBy('assigned_at')
            ->get();

        return DeliveryAssignmentResource::collection($assignments);
    }

    public function history(Request $request): AnonymousResourceCollection
    {
        $assignments = DeliveryAssignment::where('delivery_agent_id', $request->user()->id)
            ->where('status', 'delivered')
            ->when($request->filled('date_from'), fn ($q) => $q->whereDate('delivered_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($q) => $q->whereDate('delivered_at', '<=', $request->date('date_to')))
            ->with(['order.items.product', 'order.address'])
            ->orderByDesc('delivered_at')
            ->get();

        return DeliveryAssignmentResource::collection($assignments);
    }

    public function downloadInvoice(Request $request, DeliveryAssignment $assignment)
    {
        abort_unless($assignment->delivery_agent_id === $request->user()->id, 403);

        $order = $assignment->order;
        abort_unless($order->invoice, 404, 'No invoice has been generated for this order yet.');

        $order->load(['franchise', 'user', 'items.product', 'address']);

        return $this->invoices->renderPdf($order, $order->invoice)->download("invoice-{$order->invoice->invoice_number}.pdf");
    }

    public function show(Request $request, DeliveryAssignment $assignment): JsonResponse
    {
        abort_unless($assignment->delivery_agent_id === $request->user()->id, 403);

        return response()->json([
            'assignment' => new DeliveryAssignmentResource(
                $assignment->load(['order.items.product', 'order.address'])
            ),
        ]);
    }

    public function pickedUp(Request $request, DeliveryAssignment $assignment): JsonResponse
    {
        abort_unless($assignment->delivery_agent_id === $request->user()->id, 403);

        if ($assignment->status->value !== 'assigned') {
            return response()->json([
                'message' => "This assignment is \"{$assignment->status->value}\", not \"assigned\".",
            ], 422);
        }

        $assignment->update(['status' => 'picked_up', 'picked_up_at' => now()]);

        try {
            $this->fulfillment->transition($assignment->order, 'out_for_delivery');
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json(['assignment' => new DeliveryAssignmentResource($assignment->fresh('order'))]);
    }

    public function delivered(MarkDeliveredRequest $request, DeliveryAssignment $assignment): JsonResponse
    {
        abort_unless($assignment->delivery_agent_id === $request->user()->id, 403);

        if (! in_array($assignment->status->value, ['picked_up', 'out_for_delivery'], true)) {
            return response()->json([
                'message' => "This assignment is \"{$assignment->status->value}\" - it needs to be picked up first.",
            ], 422);
        }

        // Same private-disk pattern as prescriptions - proof of delivery
        // isn't public, served through the app rather than a bare URL.
        $path = $request->file('proof_of_delivery')->store('proof-of-delivery/'.$assignment->order_id, 'local');

        $assignment->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'proof_of_delivery_path' => $path,
        ]);

        try {
            $this->fulfillment->transition($assignment->order, 'delivered');
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (InsufficientStockException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        }

        return response()->json(['assignment' => new DeliveryAssignmentResource($assignment->fresh('order'))]);
    }

    /**
     * Customer unreachable, wrong address, refused delivery, etc. The order
     * itself is deliberately left as-is here (still out_for_delivery) -
     * re-attempting or re-assigning is a franchise-side follow-up action,
     * not something this endpoint decides on the agent's behalf.
     */
    public function failed(MarkFailedRequest $request, DeliveryAssignment $assignment): JsonResponse
    {
        abort_unless($assignment->delivery_agent_id === $request->user()->id, 403);

        if (! in_array($assignment->status->value, ['assigned', 'picked_up', 'out_for_delivery'], true)) {
            return response()->json([
                'message' => "This assignment is already \"{$assignment->status->value}\".",
            ], 422);
        }

        $assignment->update([
            'status' => 'failed',
            'failure_reason' => $request->validated('failure_reason'),
        ]);

        return response()->json(['assignment' => new DeliveryAssignmentResource($assignment->fresh('order'))]);
    }
}
