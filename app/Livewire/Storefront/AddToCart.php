<?php

namespace App\Livewire\Storefront;

use App\Models\Cart;
use App\Models\CartItem;
use Livewire\Component;

class AddToCart extends Component
{
    public int $productId;

    public bool $inStock = true;

    public bool $added = false;

    public function add(): void
    {
        if (! auth('web')->check()) {
            $this->redirect(route('storefront.login', ['redirect' => url()->current()]), navigate: true);

            return;
        }

        // Same cart-resolution as CartController::addItem() in the API -
        // one cart per customer, created on first use.
        $cart = Cart::firstOrCreate(['user_id' => auth('web')->id()]);

        $item = CartItem::where('cart_id', $cart->id)->where('product_id', $this->productId)->first();

        if ($item) {
            $item->increment('quantity');
        } else {
            CartItem::create(['cart_id' => $cart->id, 'product_id' => $this->productId, 'quantity' => 1]);
        }

        $this->added = true;
        $this->dispatch('cart-updated'); // tells Header to re-check the cart count
    }

    public function render()
    {
        return view('livewire.storefront.add-to-cart');
    }
}
