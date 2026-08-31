<?php

namespace App\Http\Controllers\Web\Franchise;

use App\Http\Controllers\Controller;
use App\Http\Requests\Franchise\AssignDeliveryRequest;
use App\Http\Requests\Franchise\UpdateOrderStatusRequest;
use App\Models\DeliveryAssignment;
use App\Models\Order;
use App\Models\User;
use App\Services\Orders\OrderFulfillmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class OrderController extends Controller
{
    public function __construct(private readonly OrderFulfillmentService $fulfillment) {}

    public function index(Request $request): View
    {
        $orders = Order::where('franchise_id', $request->user()->franchise_id)
            ->whereNotIn('status', ['pending_payment'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->with(['items.product', 'deliveryAssignments'])
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Delivery Agents at this same store, for the assign-delivery
        // dropdown - franchise.scope already keeps this whole controller
        // scoped to one franchise, so this is naturally just their own staff.
        $deliveryAgents = User::where('franchise_id', $request->user()->franchise_id)
            ->whereHas('roles', fn ($q) => $q->where('name', 'Delivery Agent'))
            ->get(['id', 'name']);

        $nextStatuses = $orders->mapWithKeys(fn (Order $order) => [
            $order->id => $this->fulfillment->validNextStatuses($order),
        ]);

        return view('franchise.orders.index', compact('orders', 'deliveryAgents', 'nextStatuses'));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        try {
            $this->fulfillment->transition($order, $request->validated('status'));
        } catch (RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('franchise.orders.index')->with('success', "Order #{$order->id} updated.");
    }

    public function assignDelivery(AssignDeliveryRequest $request, Order $order): RedirectResponse
    {
        if ($order->status->value !== 'ready_for_dispatch') {
            return back()->withErrors(['delivery' => 'Only a ready-for-dispatch order can be assigned to a delivery agent.']);
        }

        if ($order->fulfillment_type->value !== 'delivery') {
            return back()->withErrors(['delivery' => "This is a {$order->fulfillment_type->value} order - it doesn't need a delivery assignment."]);
        }

        DeliveryAssignment::create([
            'order_id' => $order->id,
            'delivery_agent_id' => $request->validated('delivery_agent_id'),
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        return redirect()->route('franchise.orders.index')->with('success', "Order #{$order->id} assigned for delivery.");
    }
}
