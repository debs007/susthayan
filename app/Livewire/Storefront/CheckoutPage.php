<?php

namespace App\Livewire\Storefront;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\PrescriptionRequiredException;
use App\Livewire\Storefront\Concerns\HandlesRazorpayPayment;
use App\Models\Cart;
use App\Models\Franchise;
use App\Models\Prescription;
use App\Services\Orders\CheckoutService;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CheckoutPage extends Component
{
    use HandlesRazorpayPayment;

    public ?int $selectedAddressId = null;

    public bool $showAddAddress = false;

    #[Validate('required|string|max:255')]
    public string $line1 = '';

    #[Validate('nullable|string|max:255')]
    public string $line2 = '';

    #[Validate('required|string|max:100')]
    public string $city = '';

    #[Validate('required|string|max:100')]
    public string $state = '';

    #[Validate('required|string|max:10')]
    public string $pincode = '';

    public ?string $errorMessage = null;

    public bool $needsApprovedPrescription = false;

    public function mount(): void
    {
        $default = auth('web')->user()->addresses()->where('is_default', true)->first()
            ?? auth('web')->user()->addresses()->first();
        $this->selectedAddressId = $default?->id;
    }

    public function saveNewAddress(): void
    {
        $this->validate();

        $address = auth('web')->user()->addresses()->create([
            'line1' => $this->line1,
            'line2' => $this->line2 ?: null,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
        ]);

        $this->selectedAddressId = $address->id;
        $this->showAddAddress = false;
        $this->reset(['line1', 'line2', 'city', 'state', 'pincode']);
    }

    public function placeOrder(CheckoutService $checkout): void
    {
        $this->errorMessage = null;
        $this->needsApprovedPrescription = false;

        if (! $this->selectedAddressId) {
            $this->errorMessage = 'Please select or add a delivery address.';

            return;
        }

        $user = auth('web')->user();
        $cart = Cart::with('items.product')->where('user_id', $user->id)->first();

        if (! $cart || $cart->items->isEmpty()) {
            $this->errorMessage = 'Your cart is empty.';

            return;
        }

        $franchiseId = $cart->franchise_id ?? Franchise::where('status', 'active')->value('id');

        if (! $franchiseId) {
            $this->errorMessage = 'No store is available to fulfil this order right now.';

            return;
        }

        try {
            $order = $checkout->placeOrder($user, $cart, $franchiseId, 'delivery', $this->selectedAddressId);
        } catch (InsufficientStockException $e) {
            $this->errorMessage = 'One of your items just went out of stock. Please review your cart.';

            return;
        } catch (PrescriptionRequiredException $e) {
            $this->needsApprovedPrescription = true;
            $this->errorMessage = 'This order needs an approved prescription before it can be placed.';

            return;
        }

        $this->initiatePayment($order);
    }

    public function render()
    {
        $cart = Cart::with('items.product')->where('user_id', auth('web')->id())->first();
        $addresses = auth('web')->user()->addresses()->orderByDesc('is_default')->get();

        $hasApprovedPrescription = Prescription::where('user_id', auth('web')->id())
            ->where('verification_status', 'approved')
            ->whereNull('order_id')
            ->exists();

        return view('livewire.storefront.checkout-page', compact('cart', 'addresses', 'hasApprovedPrescription'))
            ->layout('components.layouts.storefront', ['title' => 'Checkout - Susthayan']);
    }
}
