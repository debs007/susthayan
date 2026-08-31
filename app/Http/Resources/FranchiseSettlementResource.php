<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\FranchiseSettlement */
class FranchiseSettlementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'franchise' => $this->whenLoaded('franchise', fn () => [
                'id' => $this->franchise->id,
                'name' => $this->franchise->name,
            ]),
            'period_start' => $this->period_start?->toDateString(),
            'period_end' => $this->period_end?->toDateString(),
            'gross_sales' => (string) $this->gross_sales,
            'commission_amount' => (string) $this->commission_amount,
            'net_payable' => (string) $this->net_payable,
            'paid_at' => $this->paid_at?->toIso8601String(),
            'payout_reference' => $this->payout_reference,
        ];
    }
}
