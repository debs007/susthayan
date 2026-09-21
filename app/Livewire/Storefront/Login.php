<?php

namespace App\Livewire\Storefront;

use App\Enums\OtpPurpose;
use App\Models\User;
use App\Services\Otp\OtpService;
use App\Traits\AssignsRoleAcrossGuards;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    use AssignsRoleAcrossGuards;

    public string $step = 'mobile';

    #[Validate('required|digits:10')]
    public string $mobile = '';

    #[Validate('required|min:4')]
    public string $code = '';

    public string $name = '';

    public ?string $errorMessage = null;

    public ?string $redirectTo = null;

    public function mount(): void
    {
        // Read explicitly from the request rather than rely on Livewire's
        // implicit query-string-to-property binding, which needs the
        // query key to match this property's name in a way I can't
        // fully verify without running the actual app.
        $this->redirectTo = request()->query('redirect');
    }

    public function requestOtp(OtpService $otp): void
    {
        $this->validateOnly('mobile');
        $this->errorMessage = null;

        $otp->issue($this->mobile, OtpPurpose::Login);
        $this->step = 'otp';
    }

    public function changeNumber(): void
    {
        $this->step = 'mobile';
        $this->code = '';
        $this->errorMessage = null;
    }

    /**
     * Same user-resolution and role-assignment logic as
     * CustomerAuthController::verifyOtp() in the API - the only
     * difference is Auth::login() (session/web guard) at the end
     * instead of createToken() (Sanctum), since this runs as a normal
     * web request, not an API call from the mobile app.
     */
    public function verifyOtp(OtpService $otp): void
    {
        $this->validateOnly('code');
        $this->errorMessage = null;

        if (! $otp->verify($this->mobile, OtpPurpose::Login, $this->code)) {
            $this->errorMessage = 'Invalid or expired code.';

            return;
        }

        $user = User::firstOrNew(['mobile' => $this->mobile]);

        if (! $user->exists) {
            $user->name = trim($this->name) ?: 'Customer';
            $user->is_active = true;
        }

        $user->mobile_verified_at ??= now();
        $user->save();

        if (! $user->hasRole('Customer')) {
            $this->assignRoleAcrossGuards($user, 'Customer');
        }

        if (! $user->is_active) {
            $this->errorMessage = 'This account has been disabled.';

            return;
        }

        Auth::guard('web')->login($user, remember: true);
        session()->regenerate();

        $this->redirect($this->redirectTo ?? route('storefront.home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.storefront.login')->layout('components.layouts.storefront', ['title' => 'Log in - Susthayan']);
    }
}
