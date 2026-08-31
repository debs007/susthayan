<?php

namespace App\Services\Purchasing;

use App\Models\Supplier;
use App\Models\SupplierPayment;
use Illuminate\Support\Collection;

class VendorLedgerService
{
    /** One row per supplier with an unpaid balance. Pass $franchiseId to scope to invoices tied to that franchise's POs. */
    public function outstanding(?int $franchiseId = null): Collection
    {
        return Supplier::query()
            ->get()
            ->map(function (Supplier $supplier) use ($franchiseId) {
                $invoices = $supplier->invoices()
                    ->whereIn('status', ['approved', 'paid'])
                    ->when($franchiseId, fn ($query) => $query->whereHas(
                        'purchaseOrder',
                        fn ($q) => $q->where('franchise_id', $franchiseId)
                    ))
                    ->get();

                $totalInvoiced = (float) $invoices->sum(fn ($i) => (float) $i->invoice_amount + (float) $i->gst_amount);

                $totalPaid = (float) SupplierPayment::whereIn('supplier_invoice_id', $invoices->pluck('id'))
                    ->where('status', 'completed')
                    ->sum('amount_paid');

                return [
                    'supplier_id' => $supplier->id,
                    'supplier_name' => $supplier->name,
                    'total_invoiced' => $totalInvoiced,
                    'total_paid' => $totalPaid,
                    'outstanding' => round($totalInvoiced - $totalPaid, 2),
                ];
            })
            ->filter(fn ($row) => $row['outstanding'] > 0)
            ->sortByDesc('outstanding')
            ->values();
    }

    /**
     * Chronological statement: every approved/paid invoice (debit) and
     * completed payment (credit), running balance. Pass $franchiseId so a
     * franchise user only sees their own store's transactions with this
     * supplier - suppliers are shared across the network, but their
     * purchase history with other stores isn't this franchise's to see.
     * Admin calls this with $franchiseId = null for the full picture.
     */
    public function ledger(Supplier $supplier, ?int $franchiseId = null): array
    {
        $invoiceEntries = $supplier->invoices()
            ->whereIn('status', ['approved', 'paid'])
            ->when($franchiseId, fn ($query) => $query->whereHas(
                'purchaseOrder',
                fn ($q) => $q->where('franchise_id', $franchiseId)
            ))
            ->get()
            ->map(fn ($invoice) => [
                'type' => 'invoice',
                'date' => $invoice->invoice_date->toDateString(),
                'reference' => $invoice->invoice_number,
                'debit' => (float) $invoice->invoice_amount + (float) $invoice->gst_amount,
                'credit' => 0.0,
            ]);

        $paymentEntries = SupplierPayment::where('supplier_id', $supplier->id)
            ->where('status', 'completed')
            ->when($franchiseId, fn ($query) => $query->whereHas(
                'invoice.purchaseOrder',
                fn ($q) => $q->where('franchise_id', $franchiseId)
            ))
            ->get()
            ->map(fn ($payment) => [
                'type' => 'payment',
                'date' => $payment->payment_date->toDateString(),
                'reference' => $payment->reference_number,
                'debit' => 0.0,
                'credit' => (float) $payment->amount_paid,
            ]);

        $running = 0.0;
        $entries = $invoiceEntries->concat($paymentEntries)
            ->sortBy('date')
            ->values()
            ->map(function (array $entry) use (&$running) {
                $running += $entry['debit'] - $entry['credit'];
                $entry['running_balance'] = round($running, 2);

                return $entry;
            });

        return [
            'entries' => $entries,
            'closing_balance' => round($running, 2),
        ];
    }
}
