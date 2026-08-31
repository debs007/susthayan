<?php

namespace App\Services\Payout;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * RazorpayX payouts need a Contact and a Fund Account before a Payout can
 * be made against them - three entities, not one. The Composite Payout API
 * (used below) creates all three in a single call rather than a three-call
 * round trip, since franchise bank details aren't pre-registered as
 * RazorpayX Contacts in this pass.
 *
 * IMPORTANT: verify the field names/endpoint/header below against your own
 * RazorpayX dashboard and current API docs before this ever touches real
 * money - this is a best-effort reconstruction from documentation, not a
 * tested-against-a-live-account integration:
 *   - `account_number` is YOUR OWN RazorpayX business account number
 *     (My Account & Settings -> Banking), NOT the franchise's - Razorpay is
 *     explicit that passing the recipient's account number here is a real,
 *     common mistake, not a hypothetical one.
 *   - `purpose` has to be one of the classifications already configured on
 *     your dashboard - it can't be created via the API. 'payout' is a
 *     placeholder; confirm the right one before using this for real.
 *   - An idempotency key is mandatory on every payout request as of March
 *     2025 - confirm the exact header name RazorpayX expects for your
 *     account (dashboard/current docs), since that's the kind of detail
 *     that can change and isn't worth guessing at for something that
 *     moves money.
 */
class RazorpayXPayoutGateway implements PayoutGatewayInterface
{
    public function __construct(
        private readonly string $keyId,
        private readonly string $keySecret,
        private readonly string $sourceAccountNumber,
    ) {}

    public function payout(
        string $contactName,
        string $accountNumber,
        string $ifsc,
        int $amountInPaise,
        string $referenceId,
        string $narration,
    ): string {
        $response = Http::withBasicAuth($this->keyId, $this->keySecret)
            ->withHeaders(['X-Payout-Idempotency' => (string) Str::uuid()])
            ->asJson()
            ->post('https://api.razorpay.com/v1/payouts', [
                'account_number' => $this->sourceAccountNumber,
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'mode' => 'IMPS',
                'purpose' => 'payout',
                // Queues instead of hard-failing if the platform's RazorpayX
                // balance is temporarily short - auto-processes once funded.
                'queue_if_low_balance' => true,
                'reference_id' => $referenceId,
                'narration' => substr($narration, 0, 30), // 30-char limit, alphanumeric + space only
                'fund_account' => [
                    'account_type' => 'bank_account',
                    'bank_account' => [
                        'name' => $contactName,
                        'ifsc' => $ifsc,
                        'account_number' => $accountNumber,
                    ],
                    'contact' => [
                        'name' => $contactName,
                        'type' => 'vendor',
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException("RazorpayX payout failed: {$response->body()}");
        }

        return (string) $response->json('id');
    }
}
