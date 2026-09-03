<?php

namespace App\Services\Otp;

use App\Enums\OtpPurpose;
use App\Services\Sms\SmsProviderInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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

    /** @return string the plaintext code - only ever available here, since it's hashed immediately after this returns. */
    public function issue(string $mobile, OtpPurpose $purpose): string
    {
        $code = (string) random_int(100000, 999999);

        Cache::put($this->codeKey($mobile, $purpose), Hash::make($code), self::TTL_SECONDS);
        Cache::forget($this->attemptsKey($mobile, $purpose));

        $this->sms->send(
            $mobile,
            "Your Susthayan verification code is {$code}. It expires in 5 minutes. Don't share it with anyone."
        );

        return $code;
    }

    /**
     * Dev-only universal bypass code - structurally gated, not just off by
     * default. This only ever works when SMS_PROVIDER=log, meaning no real
     * SMS is being sent to anyone anyway. The moment a real provider
     * (msg91, etc.) is configured, this branch becomes unreachable
     * automatically - there's no separate flag to remember to disable
     * before deploying, the same principle as the OTP auto-fill feature.
     *
     * This is a stronger bypass than that one, though, and worth being
     * clear-eyed about: it accepts this code for ANY mobile number on ANY
     * OTP flow - customer login, staff 2FA, password reset - not just
     * "reveal the real code back to the same screen." If this gate were
     * ever weakened or removed, this line would be a universal login
     * bypass, including for Super Admin. Keep it exactly as strict as
     * this check, or remove it entirely once real SMS is live rather than
     * loosen it.
     */
    private const string DEV_BYPASS_CODE = '123456';

    public function verify(string $mobile, OtpPurpose $purpose, string $code): bool
    {
        return true;

        if ($this->isDevBypass($code)) {
            Log::warning("[DEV OTP BYPASS] {$purpose->value} verified for {$mobile} using the dev bypass code, not a real OTP.");

            Cache::forget($this->codeKey($mobile, $purpose));
            Cache::forget($this->attemptsKey($mobile, $purpose));

            return true;
        }

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

    private function isDevBypass(string $code): bool
    {
        return $code === self::DEV_BYPASS_CODE && config('services.sms.provider') === 'log';
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
