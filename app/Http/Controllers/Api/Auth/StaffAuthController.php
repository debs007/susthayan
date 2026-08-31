<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\StaffLoginRequest;
use App\Http\Requests\Auth\VerifyTwoFactorRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Otp\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StaffAuthController extends Controller
{
    private const int CHALLENGE_TTL_SECONDS = 300;

    public function __construct(private readonly OtpService $otp) {}

    /**
     * Franchise Owner / Franchise Staff / Pharmacist / Delivery Agent /
     * Super Admin / Accountant all log in here - password first, and a
     * second OTP step for Super Admin (or anyone flagged two_factor_enabled).
     */
    public function login(StaffLoginRequest $request): JsonResponse
    {
        $login = $request->validated('login');

        $user = User::where(fn ($query) => $query->where('mobile', $login)->orWhere('email', $login))->first();

        if (! $user || ! $user->password || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'This account has been disabled.'], 403);
        }

        if ($user->hasRole('Super Admin') || $user->two_factor_enabled) {
            $this->otp->issue($user->mobile, OtpPurpose::TwoFactor);

            $challenge = (string) Str::uuid();
            Cache::put("2fa_pending:{$challenge}", $user->id, self::CHALLENGE_TTL_SECONDS);

            return response()->json([
                'two_factor_required' => true,
                'challenge' => $challenge,
            ]);
        }

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('staff-portal')->plainTextToken,
        ]);
    }

    public function verifyTwoFactor(VerifyTwoFactorRequest $request): JsonResponse
    {
        $challenge = $request->validated('challenge');
        $userId = Cache::get("2fa_pending:{$challenge}");

        if (! $userId) {
            return response()->json(['message' => 'This login attempt has expired. Please log in again.'], 422);
        }

        $user = User::findOrFail($userId);

        if (! $this->otp->verify($user->mobile, OtpPurpose::TwoFactor, $request->validated('code'))) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        Cache::forget("2fa_pending:{$challenge}");

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('staff-portal')->plainTextToken,
        ]);
    }
}
