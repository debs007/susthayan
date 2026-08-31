<?php

namespace App\Services\Payout;

interface PayoutGatewayInterface
{
    /**
     * @throws \RuntimeException if the payout is rejected
     * @return string the gateway's payout id
     */
    public function payout(
        string $contactName,
        string $accountNumber,
        string $ifsc,
        int $amountInPaise,
        string $referenceId,
        string $narration,
    ): string;
}
