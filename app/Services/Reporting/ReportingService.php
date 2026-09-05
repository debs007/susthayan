<?php

namespace App\Services\Reporting;

use App\Models\Order;
use App\Models\SupplierInvoice;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * "Daily transactions recorded -> Sales register -> Purchase register ->
 * GST reports -> Vendor outstanding report -> Franchise settlement report
 * -> Export to Tally/ERP" (SRS flow #10). The last two steps already exist
 * (Api\Franchise\VendorLedgerController / Api\Admin\SettlementController) -
 * this service covers the rest.
 */
class ReportingService
{
    /**
     * Every revenue-recognised sale in the period - online AND POS both,
     * unlike Franchise Settlement's gross_sales which deliberately excludes
     * POS (nothing to pay a franchise back for money it already collected
     * itself). A sales register is an accounting record of what was sold,
     * not a statement of what's owed to whom - both belong in it.
     */
    public function salesRegister(string $from, string $to, ?int $franchiseId = null): Collection
    {
        [$start, $end] = $this->dayBounds($from, $to);

        return Order::whereIn('status', ['delivered', 'picked_up'])
            ->whereBetween('delivered_at', [$start, $end])
            ->when($franchiseId, fn ($query) => $query->where('franchise_id', $franchiseId))
            ->with(['franchise:id,name', 'invoice'])
            ->orderBy('delivered_at')
            ->get()
            ->map(fn (Order $order) => [
                'date' => $order->delivered_at->toDateString(),
                'order_id' => (string) $order->id,
                'invoice_number' => $order->invoice?->invoice_number ?? '',
                'franchise' => $order->franchise?->name ?? 'N/A',
                'channel' => $order->fulfillment_type->value,
                'subtotal' => (string) $order->subtotal_amount,
                'tax' => (string) $order->tax_amount,
                'total' => (string) $order->total_amount,
            ]);
    }

    /**
     * Based on supplier invoices, not GRNs - GRNs don't carry a GST amount,
     * and a purchase register without tax figures isn't useful for the GST
     * report that follows it in the same flow.
     */
    public function purchaseRegister(string $from, string $to, ?int $franchiseId = null): Collection
    {
        return SupplierInvoice::whereIn('status', ['approved', 'paid'])
            ->whereBetween('invoice_date', [$from, $to])
            ->when($franchiseId, fn ($query) => $query->whereHas(
                'purchaseOrder',
                fn ($q) => $q->where('franchise_id', $franchiseId)
            ))
            ->with(['supplier:id,name'])
            ->orderBy('invoice_date')
            ->get()
            ->map(fn (SupplierInvoice $invoice) => [
                'date' => $invoice->invoice_date->toDateString(),
                'invoice_number' => $invoice->invoice_number,
                'supplier' => $invoice->supplier->name,
                'amount' => (string) $invoice->invoice_amount,
                'gst' => (string) $invoice->gst_amount,
                'total' => (string) ((float) $invoice->invoice_amount + (float) $invoice->gst_amount),
            ]);
    }

    /**
     * Output GST (collected on sales) minus input GST (paid on purchases,
     * claimable as a credit) = net payable to the government. Summary
     * level only - a real GSTR-1 filing needs HSN-code-wise and rate-wise
     * breakdowns, which both registers above have the underlying data for
     * but this doesn't group by yet.
     */
    public function gstSummary(string $from, string $to, ?int $franchiseId = null): array
    {
        $outputGst = (float) $this->salesRegister($from, $to, $franchiseId)->sum(fn ($row) => (float) $row['tax']);
        $inputGst = (float) $this->purchaseRegister($from, $to, $franchiseId)->sum(fn ($row) => (float) $row['gst']);

        return [
            'period' => ['from' => $from, 'to' => $to],
            'output_gst' => round($outputGst, 2),
            'input_gst' => round($inputGst, 2),
            'net_gst_payable' => round($outputGst - $inputGst, 2),
        ];
    }

    /**
     * "Order vs payment mismatch report" (SRS 3.5). Two directions of
     * mismatch: an order that moved past pending_payment with no
     * successful payment behind it (shouldn't be possible given how
     * PaymentConfirmationService gates that transition, but the whole
     * point of a reconciliation report is not trusting "shouldn't happen"),
     * and the reverse - a successful payment sitting on an order that
     * never got marked confirmed, which usually means a webhook silently
     * failed and the client-side verify call never landed either.
     */
    public function paymentMismatches(): array
    {
        $confirmedWithoutPayment = Order::whereNotIn('status', ['pending_payment', 'cancelled'])
            ->whereDoesntHave('payments', fn ($query) => $query->where('status', 'success'))
            ->with('franchise:id,name')
            ->get(['id', 'franchise_id', 'status', 'total_amount', 'created_at'])
            ->map(fn (Order $order) => [
                'order_id' => $order->id,
                'franchise' => $order->franchise?->name ?? 'N/A',
                'status' => $order->status->value,
                'total_amount' => (string) $order->total_amount,
                'placed_at' => $order->created_at->toIso8601String(),
            ]);

        $paidButStillPending = Order::where('status', 'pending_payment')
            ->whereHas('payments', fn ($query) => $query->where('status', 'success'))
            ->with('franchise:id,name')
            ->get(['id', 'franchise_id', 'status', 'total_amount', 'created_at'])
            ->map(fn (Order $order) => [
                'order_id' => $order->id,
                'franchise' => $order->franchise?->name ?? 'N/A',
                'total_amount' => (string) $order->total_amount,
                'placed_at' => $order->created_at->toIso8601String(),
            ]);

        return [
            'confirmed_without_successful_payment' => $confirmedWithoutPayment,
            'paid_but_order_still_pending' => $paidButStillPending,
        ];
    }

    /** @return array{0: Carbon, 1: Carbon} */
    private function dayBounds(string $from, string $to): array
    {
        return [Carbon::parse($from)->startOfDay(), Carbon::parse($to)->endOfDay()];
    }
}
