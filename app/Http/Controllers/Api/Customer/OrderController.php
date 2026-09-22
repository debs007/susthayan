<?php

namespace App\Http\Controllers\Api\Customer;

use App\Exceptions\PrescriptionRequiredException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\PlaceOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Cart;
use App\Models\Order;
use App\Services\Invoicing\InvoiceService;
use App\Services\Orders\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkout,
        private readonly InvoiceService $invoices,
    ) {}

    /**
     * Cart -> Order (still pending_payment - see PaymentController for what
     * actually confirms it). Stock/prescription checks happen inside
     * CheckoutService; this just translates its exceptions to HTTP codes.
     */
    public function store(PlaceOrderRequest $request): JsonResponse
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);

        try {
            $order = $this->checkout->placeOrder(
                user: $request->user(),
                cart: $cart,
                fulfillmentType: $request->validated('fulfillment_type'),
                addressId: $request->validated('address_id'),
            );
        } catch (PrescriptionRequiredException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return (new OrderResource($order->load(['items.product', 'franchise'])))
            ->response()
            ->setStatusCode(201);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = Order::where('user_id', $request->user()->id)
            ->with(['items.product', 'franchise', 'labTestBooking.labTest', 'labTestBooking.labCenter', 'appointmentBooking.doctor', 'appointmentBooking.hospital'])
            ->latest()
            ->paginate(15);

        return OrderResource::collection($orders);
    }

    public function show(Request $request, Order $order): OrderResource
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        return new OrderResource($order->load(['items.product', 'items.batches', 'franchise', 'labTestBooking.labTest', 'labTestBooking.labCenter', 'appointmentBooking.doctor', 'appointmentBooking.hospital']));
    }

    public function downloadInvoice(Request $request, Order $order)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        abort_unless($order->invoice, 404, 'No invoice has been generated for this order yet.');

        $order->load(['franchise', 'user', 'items.product', 'address']);

        return $this->invoices->renderPdf($order, $order->invoice)->download("invoice-{$order->invoice->invoice_number}.pdf");
    }
}
