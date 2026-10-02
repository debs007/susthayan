<?php

namespace App\Services\Orders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\PrescriptionRequiredException;
use App\Models\Cart;
use App\Models\CustomerPayment;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Prescription;
use App\Models\Product;
use App\Models\User;
use App\Services\Invoicing\InvoiceService;
use App\Services\Payment\PaymentConfirmationService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        private readonly PaymentConfirmationService $paymentConfirmation,
        private readonly InvoiceService $invoices,
    ) {}

    /**
     * Cart -> Order, with no franchise attached yet. Every customer order
     * now lands unassigned - an admin picks the fulfilling franchise
     * afterward (see Admin\OrderAssignmentController), rather than the
     * customer's cart or a "just pick the only active one" fallback
     * silently deciding it at checkout.
     *
     * That means pricing and stock can't be resolved per-franchise here,
     * since no franchise is known yet:
     * - Price uses priceFor(null) - the existing global/default price row
     *   (franchise_id IS NULL), the same fallback the storefront already
     *   uses when browsing before a franchise is chosen. This price is
     *   what the customer actually pays and is NOT re-priced later when
     *   a franchise is assigned - the assigned franchise fulfills at the
     *   price the customer already agreed to and paid, not its own local
     *   pricing. A product with no global price row configured will fail
     *   checkout here ("No price configured") - every sellable product
     *   needs one now, not just franchise-specific overrides.
     * - Stock is NOT checked at all at this stage, since there's no
     *   franchise to check it against. Best-effort reservation happens
     *   when an admin assigns a franchise (see Admin\OrderController::
     *   assign()) - not a hard check, since assignment itself is never
     *   blocked by stock; see StockService::reserveQuantity().
     */
    public function placeOrder(User $user, Cart $cart, string $fulfillmentType, ?int $addressId): Order
    {
        $cart->loadMissing('items.product.prices');

        if ($cart->items->isEmpty()) {
            throw new RuntimeException('Cart is empty.');
        }

        return DB::transaction(function () use ($user, $cart, $fulfillmentType, $addressId) {
            $requiresRx = $cart->items->contains(fn ($item) => $item->product->prescription_required);
            $prescription = null;

            if ($requiresRx) {
                // Unclaimed (order_id still null) + approved. The pharmacist who
                // approved it is trusted to have checked it covers what's in the
                // cart - matching it item-by-item is a clinical judgement call,
                // not something to encode as string-matching logic here.
                $prescription = Prescription::where('user_id', $user->id)
                    ->where('verification_status', 'approved')
                    ->whereNull('order_id')
                    ->latest()
                    ->first();

                if (! $prescription) {
                    throw new PrescriptionRequiredException;
                }
            }

            $order = Order::create([
                'user_id' => $user->id,
                'franchise_id' => null,
                'address_id' => $addressId,
                'fulfillment_type' => $fulfillmentType,
                'status' => OrderStatus::PendingPayment,
                'requires_prescription' => $requiresRx,
            ]);

            $subtotal = 0;
            $tax = 0;

            foreach ($cart->items as $cartItem) {
                $price = $cartItem->product->priceFor(null);

                if (! $price) {
                    throw new RuntimeException("No price configured for product #{$cartItem->product_id}.");
                }

                $lineSubtotal = $price->selling_price * $cartItem->quantity;
                $lineTax = round((float) ($lineSubtotal * $price->tax_percentage / 100), 2);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $price->selling_price,
                    'tax_percentage' => $price->tax_percentage,
                    'total_price' => $lineSubtotal + $lineTax,
                ]);

                $subtotal += $lineSubtotal;
                $tax += $lineTax;
            }

            // Delivery pricing (distance/minimum-order based fees) isn't modelled
            // yet - delivery_charge stays 0 until that's designed.
            // Discount computed the same way Cart::couponDiscountAmount()
            // already does (same method, same source of truth) - only
            // ever non-zero if a coupon was actually applied and still
            // valid at the moment of checkout.
            $discount = $cart->couponDiscountAmount();

            $order->update([
                'subtotal_amount' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => $tax,
                'total_amount' => max(0, $subtotal + $tax - $discount),
            ]);

            if ($prescription) {
                $prescription->update(['order_id' => $order->id]);
            }

            $cart->items()->delete();
            // Coupon lives on the cart, not the (now-empty) cart's items -
            // clearing it explicitly so it doesn't silently reapply to
            // whatever the customer shops for next.
            $cart->update(['coupon_id' => null]);

            // Temporary: payments are off (config('services.payment.enabled')
            // false). Rather than duplicate markSuccessful()'s confirm +
            // event-dispatch logic here, run a real (non-Razorpay)
            // CustomerPayment through the exact same path every genuine
            // payment already goes through - same guarantees, one code path.
            if (! config('services.payment.enabled')) {
                $payment = CustomerPayment::create([
                    'order_id' => $order->id,
                    'amount' => $order->total_amount,
                    'gateway' => 'bypassed',
                    'status' => PaymentStatus::Initiated,
                ]);

                $this->paymentConfirmation->markSuccessful($payment);

                // Admin-side invoice access right after placement, as asked -
                // the customer-facing download still only appears once the
                // order is actually delivered/picked up (unchanged, see
                // OrderFulfillmentService). Franchise details on the PDF
                // itself fill in automatically once one is assigned, since
                // rendering reads the order live rather than a snapshot.
                $this->invoices->generateFor($order->fresh());
            }

            return $order->fresh(['items.product']);
        });
    }

    /**
     * $items is a list of ['product_id' => int, 'quantity' => int] -
     * admin's own matching of the pharmacist's transcribed medicine
     * names to actual catalogue products, with quantities. This is never
     * routed through a payment gateway at all - it's an admin directly
     * creating a confirmed order on the customer's behalf from an
     * already-approved prescription, not a customer checking out - so
     * unlike placeOrder() above, there's no PAYMENT_ENABLED check to
     * make here; this flow was never going to touch a gateway either way.
     */
    public function placeOrderFromPrescription(
        User $user,
        Prescription $prescription,
        array $items,
        string $fulfillmentType,
        ?int $addressId,
    ): Order {
        if ($items === []) {
            throw new RuntimeException('At least one item is required.');
        }

        return DB::transaction(function () use ($user, $prescription, $items, $fulfillmentType, $addressId) {
            $order = Order::create([
                'user_id' => $user->id,
                'franchise_id' => null,
                'address_id' => $addressId,
                'fulfillment_type' => $fulfillmentType,
                'status' => OrderStatus::PendingPayment,
                'requires_prescription' => true,
            ]);

            $subtotal = 0;
            $tax = 0;

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $price = $product->priceFor(null);

                if (! $price) {
                    throw new RuntimeException("No price configured for product #{$product->id}.");
                }

                $lineSubtotal = $price->selling_price * $item['quantity'];
                $lineTax = round((float) ($lineSubtotal * $price->tax_percentage / 100), 2);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $price->selling_price,
                    'tax_percentage' => $price->tax_percentage,
                    'total_price' => $lineSubtotal + $lineTax,
                ]);

                $subtotal += $lineSubtotal;
                $tax += $lineTax;
            }

            $order->update([
                'subtotal_amount' => $subtotal,
                'discount_amount' => 0,
                'tax_amount' => $tax,
                'total_amount' => $subtotal + $tax,
            ]);

            $prescription->update(['order_id' => $order->id]);

            // Always confirms immediately - see the method doc above for
            // why PAYMENT_ENABLED doesn't apply to this flow at all.
            $payment = CustomerPayment::create([
                'order_id' => $order->id,
                'amount' => $order->total_amount,
                'gateway' => 'admin_prescription_order',
                'status' => PaymentStatus::Initiated,
            ]);

            $this->paymentConfirmation->markSuccessful($payment);
            $this->invoices->generateFor($order->fresh());

            return $order->fresh(['items.product']);
        });
    }
}
