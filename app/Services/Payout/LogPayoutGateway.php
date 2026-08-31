<?php

namespace App\Services\Payout;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Local/dev driver - fabricates a payout id and just logs it, so the
 * generate -> release -> paid flow can be exercised without a funded
 * RazorpayX account. Set PAYOUT_GATEWAY=log. Never use outside local/testing.
 */
class LogPayoutGateway implements PayoutGatewayInterface
{
    public function payout(
        string $contactName,
        string $accountNumber,
        string $ifsc,
        int $amountInPaise,
        string $referenceId,
        string $narration,
    ): string {
        $payoutId = 'pout_fake_'.Str::random(14);

        Log::info(
            "[Payout] fake payout {$payoutId}: ₹".number_format($amountInPaise / 100, 2).
            " to {$contactName} ({$accountNumber}, {$ifsc}) - ref {$referenceId}"
        );

        return $payoutId;
    }
}
