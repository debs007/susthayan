<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\AddCartItemRequest;
use App\Http\Requests\Customer\UpdateCartItemRequest;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $cart = $this->cartFor($request);

        return $this->cartResponse($cart);
    }

    /** Picking a store early lets the cart show real price/stock, not just at final checkout. */
    public function selectFranchise(Request $request): JsonResponse
    {
        $request->validate(['franchise_id' => ['required', 'integer', 'exists:franchises,id']]);

        $cart = $this->cartFor($request);
        $cart->update(['franchise_id' => $request->integer('franchise_id')]);

        return $this->cartResponse($cart);
    }

    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        $cart = $this->cartFor($request);

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->validated('product_id'))
            ->first();

        if ($item) {
            $item->increment('quantity', $request->validated('quantity'));
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->validated('product_id'),
                'quantity' => $request->validated('quantity'),
            ]);
        }

        return $this->cartResponse($cart);
    }

    public function updateItem(UpdateCartItemRequest $request, CartItem $cartItem): JsonResponse
    {
        $this->authorizeOwnership($request, $cartItem);

        $cartItem->update(['quantity' => $request->validated('quantity')]);

        return $this->cartResponse($cartItem->cart);
    }

    public function removeItem(Request $request, CartItem $cartItem): JsonResponse
    {
        $this->authorizeOwnership($request, $cartItem);

        $cart = $cartItem->cart;
        $cartItem->delete();

        return $this->cartResponse($cart);
    }

    /**
     * Validates the code is real, currently valid, and actually applies
     * to at least one product in the cart before attaching it - a coupon
     * that discounts nothing shouldn't silently "apply" and confuse the
     * customer about why their total didn't change.
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        $cart = $this->cartFor($request);
        $coupon = Coupon::where('code', strtoupper($request->string('code')))->first();

        if (! $coupon || ! $coupon->isCurrentlyValid()) {
            return response()->json(['message' => 'That coupon code is invalid or has expired.'], 422);
        }

        $cart->update(['coupon_id' => $coupon->id]);
        $cart->refresh();

        if ($cart->couponDiscountAmount() <= 0) {
            $cart->update(['coupon_id' => null]);

            return response()->json(['message' => 'This coupon doesn\'t apply to anything currently in your cart.'], 422);
        }

        return $this->cartResponse($cart);
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        $cart = $this->cartFor($request);
        $cart->update(['coupon_id' => null]);

        return $this->cartResponse($cart);
    }

    private function cartFor(Request $request): Cart
    {
        return Cart::firstOrCreate(['user_id' => $request->user()->id]);
    }

    private function authorizeOwnership(Request $request, CartItem $cartItem): void
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 403);
    }

    private function cartResponse(Cart $cart): JsonResponse
    {
        $cart->load(['items.product.prices', 'coupon']);

        $items = $cart->items->map(function (CartItem $item) use ($cart) {
            $price = $item->product->priceFor($cart->franchise_id);

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'prescription_required' => $item->product->prescription_required,
                'unit_price' => $price ? (string) $price->selling_price : null,
                'line_total' => $price ? (string) round($price->selling_price * $item->quantity, 2) : null,
            ];
        });

        $subtotal = $items->sum(fn ($i) => (float) ($i['line_total'] ?? 0));
        $discount = $cart->couponDiscountAmount();

        return response()->json([
            'cart_id' => $cart->id,
            'franchise_id' => $cart->franchise_id,
            'requires_prescription' => $cart->requiresPrescription(),
            'items' => $items,
            'subtotal' => (string) $subtotal,
            'coupon' => $cart->coupon ? [
                'id' => $cart->coupon->id,
                'code' => $cart->coupon->code,
                'discount_amount' => (string) $discount,
            ] : null,
            'total' => (string) max(0, $subtotal - $discount),
        ]);
    }
}
