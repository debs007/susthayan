<?php

namespace App\Livewire\Storefront;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AccountPage extends Component
{
    public string $name = '';

    public string $email = '';

    public string $alternateMobile = '';

    public ?string $profileMessage = null;

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

    public function mount(): void
    {
        $user = auth('web')->user();
        $this->name = $user->name;
        $this->email = $user->email ?? '';
        $this->alternateMobile = $user->alternate_mobile ?? '';
    }

    /** Same rules as the API's UpdateProfileRequest - mobile itself is deliberately never editable here, same reasoning as that request. */
    public function updateProfile(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore(auth('web')->id())],
            'alternateMobile' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/'],
        ]);

        auth('web')->user()->update([
            'name' => $this->name,
            'email' => $this->email ?: null,
            'alternate_mobile' => $this->alternateMobile ?: null,
        ]);

        $this->profileMessage = 'Profile updated.';
    }

    public function setDefaultAddress(int $addressId): void
    {
        auth('web')->user()->addresses()->update(['is_default' => false]);
        auth('web')->user()->addresses()->where('id', $addressId)->update(['is_default' => true]);
    }

    public function deleteAddress(int $addressId): void
    {
        auth('web')->user()->addresses()->where('id', $addressId)->delete();
    }

    public function saveNewAddress(): void
    {
        $this->validate();

        auth('web')->user()->addresses()->create([
            'line1' => $this->line1,
            'line2' => $this->line2 ?: null,
            'city' => $this->city,
            'state' => $this->state,
            'pincode' => $this->pincode,
        ]);

        $this->showAddAddress = false;
        $this->reset(['line1', 'line2', 'city', 'state', 'pincode']);
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirect(route('storefront.home'), navigate: true);
    }

    public function render()
    {
        $addresses = auth('web')->user()->addresses()->orderByDesc('is_default')->get();

        return view('livewire.storefront.account-page', compact('addresses'))
            ->layout('components.layouts.storefront', ['title' => 'Your Account - Susthayan']);
    }
}
