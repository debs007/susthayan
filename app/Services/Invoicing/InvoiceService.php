<?php

namespace App\Services\Invoicing;

use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    /**
     * The actual PDF rendering the data-record layer was originally left
     * without. Needs $order->franchise, ->user, ->items.product and
     * ->address loaded - every current caller already loads these for its
     * own detail view, so this doesn't re-fetch them itself.
     */
    public function renderPdf(Order $order, Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        return Pdf::loadView('invoices.pdf', ['order' => $order, 'invoice' => $invoice])
            ->setPaper('a4');
    }

    public function generateFor(Order $order): Invoice
    {
        return DB::transaction(function () use ($order) {
            $existing = Invoice::where('order_id', $order->id)->lockForUpdate()->first();
            if ($existing) {
                return $existing;
            }

            $invoice = Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => null,
                'subtotal_amount' => $order->subtotal_amount,
                'gst_amount' => $order->tax_amount,
                'total_amount' => $order->total_amount,
                'generated_at' => now(),
            ]);

            // The row's own auto-increment id is assigned atomically by the
            // database, so building the number from it (rather than counting
            // existing invoices first) can't race under concurrent fulfillment.
            $invoice->update([
                'invoice_number' => sprintf('INV-%d-%06d', now()->year, $invoice->id),
            ]);

            return $invoice;
        });
    }
}
