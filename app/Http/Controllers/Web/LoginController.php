<?php

namespace App\Http\Controllers\Web;

use App\Enums\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RequestPasswordResetRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\StaffLoginRequest;
use App\Models\User;
use App\Services\Otp\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * Session-based login for the Franchise & Admin web portals - the Flutter
 * apps (Customer/POS/Delivery) use the token-based /api/auth/* endpoints
 * instead (see CustomerAuthController / StaffAuthController). Same users
 * table and the same password + 2FA rules, different transport.
 */
class LoginController extends Controller
{
    public function __construct(private readonly OtpService $otp) {}

    public function show(): View
    {
        return view('auth.login');
    }

    public function login(StaffLoginRequest $request): RedirectResponse
    {
        $login = $request->validated('login');

        $user = User::where(fn ($query) => $query->where('mobile', $login)->orWhere('email', $login))->first();

        if (! $user || ! $user->password || ! Hash::check($request->validated('password'), $user->password)) {
            return back()->withErrors(['login' => 'Those credentials don\'t match our records.'])->onlyInput('login');
        }

        if (! $user->is_active) {
            return back()->withErrors(['login' => 'This account has been disabled.']);
        }

        if ($user->hasRole('Super Admin') || $user->two_factor_enabled) {
            $this->otp->issue($user->mobile, OtpPurpose::TwoFactor);
            $request->session()->put('2fa_user_id', $user->id);

            return redirect()->route('two-factor.show');
        }

        Auth::guard('web')->login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showTwoFactor(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor');
    }

    public function verifyTwoFactor(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $userId = $request->session()->get('2fa_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::findOrFail($userId);

        if (! $this->otp->verify($user->mobile, OtpPurpose::TwoFactor, $request->input('code'))) {
            return back()->withErrors(['code' => 'That code is wrong or has expired.']);
        }

        $request->session()->forget('2fa_user_id');
        Auth::guard('web')->login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Enumeration-resistant by design: the response is identical whether
     * or not the mobile matches a real staff account - an OTP is only
     * actually issued when it does, but the page always moves forward the
     * same way. A guessed mobile that doesn't exist just always fails at
     * the code-verification step (nothing was ever issued to check
     * against), the same generic "wrong or expired code" a real user
     * would see for a mistyped code - never a different message that
     * would confirm whether an account exists.
     */
    public function sendResetOtp(RequestPasswordResetRequest $request): RedirectResponse
    {
        $mobile = $request->validated('mobile');

        $user = User::where('mobile', $mobile)
            ->whereHas('roles', fn ($query) => $query->where('name', '!=', 'Customer'))
            ->first();

        if ($user) {
            $this->otp->issue($mobile, OtpPurpose::PasswordReset);
        }

        $request->session()->put('password_reset_mobile', $mobile);

        return redirect()->route('password-reset.show')
            ->with('info', 'If that mobile number belongs to a staff account, a reset code has been sent to it.');
    }

    public function showResetPassword(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('password_reset_mobile')) {
            return redirect()->route('forgot-password.show');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $mobile = $request->session()->get('password_reset_mobile');

        if (! $mobile) {
            return redirect()->route('forgot-password.show');
        }

        if (! $this->otp->verify($mobile, OtpPurpose::PasswordReset, $request->validated('code'))) {
            return back()->withErrors(['code' => 'That code is wrong or has expired.']);
        }

        // Deliberately re-looks up the user rather than trusting a stale
        // reference - the OTP only proves the mobile received the code
        // just now, not that the account is still the one to update.
        $user = User::where('mobile', $mobile)->first();

        if (! $user) {
            return redirect()->route('forgot-password.show');
        }

        $user->update(['password' => Hash::make($request->validated('password'))]);
        $request->session()->forget('password_reset_mobile');

        return redirect()->route('login')->with('success', 'Password reset - you can log in with your new password now.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
