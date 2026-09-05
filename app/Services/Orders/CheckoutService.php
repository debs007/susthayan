<?php

namespace App\Services\Orders;

use App\Enums\OrderStatus;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\PrescriptionRequiredException;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Prescription;
use App\Models\User;
use App\Services\Inventory\StockService;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CheckoutService
{
    public function __construct(private readonly StockService $stock) {}

    /**
     * Cart -> Order. Stock is only soft-checked here (available(), not
     * reserve()) - the actual hold happens on payment success, matching the
     * SRS's "Payment SUCCESS -> Stock RESERVED" ordering. That means stock
     * can still run out between checkout and payment; PaymentConfirmation
     * Service has to handle that.
     */
    public function placeOrder(User $user, Cart $cart, int $franchiseId, string $fulfillmentType, ?int $addressId): Order
    {
        $cart->loadMissing('items.product.prices');

        if ($cart->items->isEmpty()) {
            throw new RuntimeException('Cart is empty.');
        }

        return DB::transaction(function () use ($user, $cart, $franchiseId, $fulfillmentType, $addressId) {
            foreach ($cart->items as $cartItem) {
                $available = $this->stock->available($franchiseId, $cartItem->product_id);
                if ($available < $cartItem->quantity) {
                    throw new InsufficientStockException($cartItem->product_id, $cartItem->quantity, $available);
                }
            }

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
                'franchise_id' => $franchiseId,
                'address_id' => $addressId,
                'fulfillment_type' => $fulfillmentType,
                'status' => OrderStatus::PendingPayment,
                'requires_prescription' => $requiresRx,
            ]);

            $subtotal = 0;
            $tax = 0;

            foreach ($cart->items as $cartItem) {
                $price = $cartItem->product->priceFor($franchiseId);

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

            return $order->fresh(['items.product']);
        });
    }
}
