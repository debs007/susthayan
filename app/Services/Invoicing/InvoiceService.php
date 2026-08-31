<?php

namespace App\Services\Invoicing;

use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

/**
 * Data record only for now - "Invoice generated (GST)" from the SRS flow.
 * Actual PDF rendering is a separate pass.
 */
class InvoiceService
{
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
