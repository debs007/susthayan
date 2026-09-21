<?php

namespace App\Livewire\Storefront;

use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Header extends Component
{
    public string $search = '';

    /**
     * Re-evaluated on every render, not cached across requests - this
     * component holds no cart state itself, so there's nothing to keep
     * in sync except calling this again. The #[On] listener below just
     * forces a re-render, and Livewire's #[Computed] cache is
     * per-request anyway, so a fresh render means a fresh query.
     */
    #[Computed]
    public function cartCount(): int
    {
        $user = auth('web')->user();
        if (! $user) {
            return 0;
        }

        return (int) ($user->cart?->items()->sum('quantity') ?? 0);
    }

    /** Fired by AddToCart (and the cart page itself) after any change - keeps the header badge correct without a full page reload. */
    #[On('cart-updated')]
    public function refreshCartCount(): void
    {
        unset($this->cartCount); // clears this request's #[Computed] cache so the next access re-queries
    }

    public function submitSearch(): void
    {
        $query = trim($this->search);
        if ($query === '') {
            return;
        }

        $this->redirect(route('storefront.products.index', ['q' => $query]));
    }

    public function render()
    {
        return view('livewire.storefront.header');
    }
}
