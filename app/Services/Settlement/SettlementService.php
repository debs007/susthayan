<?php

namespace App\Services\Settlement;

use App\Enums\FranchiseSettlementStatus;
use App\Models\Franchise;
use App\Models\FranchiseSettlement;
use App\Models\Order;
use App\Services\Payout\PayoutGatewayInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use RuntimeException;

/**
 * "Central Collection Model" (SRS): customers pay a central account, and
 * franchises get settled periodically for what they fulfilled. Only
 * online orders (delivery/pickup) are in scope here - POS money is
 * collected directly by the franchise at the counter and never passes
 * through the central account, so there's nothing to pay out for it.
 */
class SettlementService
{
    public function __construct(private readonly PayoutGatewayInterface $payouts) {}

    /**
     * @throws RuntimeException if the period is invalid or overlaps an existing settlement for this franchise
     */
    public function generate(Franchise $franchise, string $periodStart, string $periodEnd): FranchiseSettlement
    {
        $start = Carbon::parse($periodStart)->startOfDay();
        $end = Carbon::parse($periodEnd)->endOfDay();

        if ($start->gt($end)) {
            throw new RuntimeException('period_start must be on or before period_end.');
        }

        $overlaps = FranchiseSettlement::where('franchise_id', $franchise->id)
            ->where('period_start', '<=', $end->toDateString())
            ->where('period_end', '>=', $start->toDateString())
            ->exists();

        if ($overlaps) {
            throw new RuntimeException(
                "{$franchise->name} already has a settlement covering part of this period - check for an overlap before generating another."
            );
        }

        // Scoped by delivered_at (when revenue was actually recognised),
        // not created_at (when the order was placed) - settlement follows
        // fulfillment, matching "Orders fulfilled by franchise" in the SRS
        // flow. A refunded order's status is 'refunded', not delivered/
        // picked_up, so it's naturally excluded without a separate check.
        $grossSales = (float) Order::where('franchise_id', $franchise->id)
            ->where('fulfillment_type', '!=', 'pos')
            ->whereIn('status', ['delivered', 'picked_up'])
            ->whereBetween('delivered_at', [$start, $end])
            ->sum('total_amount');

        // commission_percentage is what HQ retains - the franchise's share
        // is the remainder. See the comment on franchises.commission_
        // percentage in its migration; this direction is load-bearing for
        // real money and was confirmed against that comment before writing
        // this, not assumed.
        $commissionAmount = round($grossSales * (float) $franchise->commission_percentage / 100, 2);
        $netPayable = round($grossSales - $commissionAmount, 2);

        return FranchiseSettlement::create([
            'franchise_id' => $franchise->id,
            'period_start' => $start->toDateString(),
            'period_end' => $end->toDateString(),
            'gross_sales' => $grossSales,
            'commission_amount' => $commissionAmount,
            'net_payable' => $netPayable,
            'status' => FranchiseSettlementStatus::Generated,
        ]);
    }

    /**
     * @return Collection<int, array{franchise_id: int, franchise_name: string, settlement?: FranchiseSettlement, error?: string}>
     */
    public function generateForAllActiveFranchises(string $periodStart, string $periodEnd): Collection
    {
        return Franchise::where('status', 'active')->get()->map(function (Franchise $franchise) use ($periodStart, $periodEnd) {
            try {
                return [
                    'franchise_id' => $franchise->id,
                    'franchise_name' => $franchise->name,
                    'settlement' => $this->generate($franchise, $periodStart, $periodEnd),
                ];
            } catch (RuntimeException $e) {
                // One franchise's overlap or zero-sales period shouldn't
                // block generating settlements for everyone else in the
                // same batch run.
                return [
                    'franchise_id' => $franchise->id,
                    'franchise_name' => $franchise->name,
                    'error' => $e->getMessage(),
                ];
            }
        });
    }

    /**
     * @throws RuntimeException if the settlement isn't in a payable state, or the franchise has no bank details on file
     */
    public function release(FranchiseSettlement $settlement): FranchiseSettlement
    {
        if ($settlement->status !== FranchiseSettlementStatus::Generated) {
            throw new RuntimeException(
                "This settlement is \"{$settlement->status->value}\" - only a generated settlement can be paid out."
            );
        }

        $franchise = $settlement->franchise;

        if (! $franchise->bank_account_number || ! $franchise->bank_ifsc) {
            throw new RuntimeException("{$franchise->name} has no bank account on file - add one before releasing payment.");
        }

        if ((float) $settlement->net_payable <= 0) {
            throw new RuntimeException('Nothing to pay out - net payable is zero or negative for this period.');
        }

        $amountInPaise = (int) round((float) $settlement->net_payable * 100);

        $payoutId = $this->payouts->payout(
            contactName: $franchise->bank_account_name ?: $franchise->name,
            accountNumber: $franchise->bank_account_number,
            ifsc: $franchise->bank_ifsc,
            amountInPaise: $amountInPaise,
            referenceId: "settlement-{$settlement->id}",
            narration: "Settlement {$franchise->name}",
        );

        $settlement->update([
            'status' => FranchiseSettlementStatus::Paid,
            'paid_at' => now(),
            'payout_reference' => $payoutId,
        ]);

        return $settlement->fresh('franchise');
    }
}
