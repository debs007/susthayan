<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Shared across all 4 portals - both sit behind auth:sanctum. */
class AuthController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        return response()->json(['user' => new UserResource($request->user())]);
    }

    /**
     * `mobile` is deliberately never accepted here - UpdateProfileRequest
     * doesn't even validate it, so there's no path through this method
     * that could change the OTP-verified login identity. Only
     * alternate_mobile (a free-form secondary contact number) is editable.
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->safe()->except('profile_image');

        if ($request->hasFile('profile_image')) {
            $oldPath = $user->profile_image_path;
            $data['profile_image_path'] = $request->file('profile_image')->store('profile-images', 'r2');

            if ($oldPath !== null) {
                Storage::disk('r2')->delete($oldPath);
            }
        }

        $user->update($data);

        return response()->json(['user' => new UserResource($user->fresh())]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }
}
