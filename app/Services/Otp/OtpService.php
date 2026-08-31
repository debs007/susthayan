<?php

namespace App\Services\Otp;

use App\Enums\OtpPurpose;
use App\Services\Sms\SmsProviderInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

/**
 * Used for both customer login OTPs and staff 2FA codes - the mechanism is
 * identical, only the purpose (and therefore the cache key) differs.
 *
 * Request-per-minute throttling is handled at the route level (see the
 * 'otp' rate limiter in AppServiceProvider + routes/api.php), not in here -
 * this class only owns the OTP's own lifecycle: generate, store, verify.
 */
class OtpService
{
    private const int TTL_SECONDS = 300; // 5 minutes

    private const int MAX_ATTEMPTS = 5;

    public function __construct(private readonly SmsProviderInterface $sms) {}

    public function issue(string $mobile, OtpPurpose $purpose): void
    {
        $code = (string) random_int(100000, 999999);

        Cache::put($this->codeKey($mobile, $purpose), Hash::make($code), self::TTL_SECONDS);
        Cache::forget($this->attemptsKey($mobile, $purpose));

        $this->sms->send(
            $mobile,
            "Your Susthayan verification code is {$code}. It expires in 5 minutes. Don't share it with anyone."
        );
    }

    public function verify(string $mobile, OtpPurpose $purpose, string $code): bool
    {
        $attemptsKey = $this->attemptsKey($mobile, $purpose);
        $attempts = (int) Cache::get($attemptsKey, 0);

        if ($attempts >= self::MAX_ATTEMPTS) {
            return false; // caller should prompt the user to request a fresh code
        }

        $hashedCode = Cache::get($this->codeKey($mobile, $purpose));

        if (! $hashedCode || ! Hash::check($code, $hashedCode)) {
            Cache::put($attemptsKey, $attempts + 1, self::TTL_SECONDS);

            return false;
        }

        // One-time use - clear it the moment it's successfully verified.
        Cache::forget($this->codeKey($mobile, $purpose));
        Cache::forget($attemptsKey);

        return true;
    }

    private function codeKey(string $mobile, OtpPurpose $purpose): string
    {
        return "otp:{$purpose->value}:{$mobile}";
    }

    private function attemptsKey(string $mobile, OtpPurpose $purpose): string
    {
        return "otp:{$purpose->value}:{$mobile}:attempts";
    }
}
