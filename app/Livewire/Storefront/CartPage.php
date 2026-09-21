<?php

namespace App\Livewire\Storefront;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Franchise;
use Livewire\Component;

class CartPage extends Component
{
    public string $couponCode = '';

    public ?string $couponError = null;

    /**
     * Same auto-select behavior as the mobile app's cart screen (see
     * single-store-simplification in the app's build history) - if
     * there's exactly one active franchise, there's nothing for the
     * customer to actually choose, so picking it automatically avoids
     * an empty-seeming "select a store" step for what's currently a
     * single-location business.
     */
    protected function cart(): ?Cart
    {
        if (! auth('web')->check()) {
            return null;
        }

        $cart = Cart::with(['items.product', 'coupon'])->firstOrCreate(['user_id' => auth('web')->id()]);

        if ($cart->franchise_id === null) {
            $activeFranchises = Franchise::where('status', 'active')->limit(2)->pluck('id');
            if ($activeFranchises->count() === 1) {
                $cart->update(['franchise_id' => $activeFranchises->first()]);
            }
        }

        return $cart;
    }

    public function updateQuantity(int $cartItemId, int $quantity): void
    {
        $item = CartItem::findOrFail($cartItemId);
        abort_unless($item->cart->user_id === auth('web')->id(), 403);

        if ($quantity < 1) {
            $item->delete();
        } else {
            $item->update(['quantity' => $quantity]);
        }

        $this->dispatch('cart-updated');
    }

    public function removeItem(int $cartItemId): void
    {
        $item = CartItem::findOrFail($cartItemId);
        abort_unless($item->cart->user_id === auth('web')->id(), 403);

        $item->delete();
        $this->dispatch('cart-updated');
    }

    /** Same lookup/validity logic as CartController::applyCoupon() in the API. */
    public function applyCoupon(): void
    {
        $this->couponError = null;
        $code = trim($this->couponCode);

        if ($code === '') {
            return;
        }

        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (! $coupon || ! $coupon->isCurrentlyValid()) {
            $this->couponError = 'That coupon code is invalid or has expired.';

            return;
        }

        $this->cart()->update(['coupon_id' => $coupon->id]);
        $this->couponCode = '';
        $this->dispatch('cart-updated');
    }

    public function removeCoupon(): void
    {
        $this->cart()->update(['coupon_id' => null]);
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        $cart = $this->cart();

        return view('livewire.storefront.cart-page', compact('cart'))
            ->layout('components.layouts.storefront', ['title' => 'Your Cart - Susthayan']);
    }
}
