<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\OtpPurpose;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RequestOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Otp\OtpService;
use App\Traits\AssignsRoleAcrossGuards;
use Illuminate\Http\JsonResponse;

class CustomerAuthController extends Controller
{
    use AssignsRoleAcrossGuards;
    public function __construct(private readonly OtpService $otp) {}

    public function requestOtp(RequestOtpRequest $request): JsonResponse
    {
        $this->otp->issue($request->validated('mobile'), OtpPurpose::Login);

        // Same response whether or not this mobile is already registered -
        // this endpoint shouldn't be usable to check which numbers have accounts.
        return response()->json(['message' => 'OTP sent.']);
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $mobile = $request->validated('mobile');

        if (! $this->otp->verify($mobile, OtpPurpose::Login, $request->validated('code'))) {
            return response()->json(['message' => 'Invalid or expired code.'], 422);
        }

        $user = User::firstOrNew(['mobile' => $mobile]);

        if (! $user->exists) {
            $user->name = $request->validated('name') ?: 'Customer';
            $user->is_active = true;
        }

        $user->mobile_verified_at ??= now();
        $user->save();

        // A phone number that already belongs to a staff/admin account can
        // still also be a customer - roles are additive, not exclusive.
        if (! $user->hasRole('Customer')) {
            $this->assignRoleAcrossGuards($user, 'Customer');
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'This account has been disabled.'], 403);
        }

        return response()->json([
            'user' => new UserResource($user),
            'token' => $user->createToken('customer-app')->plainTextToken,
        ]);
    }
}
