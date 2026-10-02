<x-layouts.app title="Purchase Order #{{ $purchaseOrder->id }}">
    <a href="{{ route('admin.vendors.outstanding') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to outstanding &amp; purchase orders
    </a>

    <div class="mb-6 rounded-xl border border-border bg-canvas-raised p-5">
        <div class="flex items-center gap-3">
            <h1 class="font-display text-lg font-semibold">Purchase Order #{{ $purchaseOrder->id }}</h1>
            <span class="rounded-full bg-canvas px-2.5 py-1 text-xs font-medium capitalize text-ink-muted">{{ str_replace('_', ' ', $purchaseOrder->status->value) }}</span>
        </div>
        <div class="mt-3 grid gap-3 text-sm sm:grid-cols-2">
            <div>
                <p class="text-xs uppercase tracking-wide text-ink-muted">Supplier</p>
                <a href="{{ route('admin.vendors.ledger', $purchaseOrder->supplier) }}" class="font-medium hover:underline">{{ $purchaseOrder->supplier->name }}</a>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink-muted">Franchise</p>
                <p class="font-medium">{{ $purchaseOrder->franchise->name }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink-muted">Created by</p>
                <p class="font-medium">{{ $purchaseOrder->createdBy?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-wide text-ink-muted">Approved by</p>
                <p class="font-medium">{{ $purchaseOrder->approvedBy?->name ?? 'Not yet approved' }}</p>
            </div>
        </div>
    </div>

    <div class="mb-6 rounded-xl border border-border bg-canvas-raised">
        <div class="border-b border-border px-5 py-4">
            <h2 class="font-display font-semibold">Items</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Product</th>
                    <th class="px-5 py-3 text-right font-medium">Qty</th>
                    <th class="px-5 py-3 text-right font-medium">Expected rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ($purchaseOrder->items as $item)
                    <tr>
                        <td class="px-5 py-3 font-medium">{{ $item->product?->name ?? "Product #{$item->product_id}" }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs">{{ $item->ordered_qty }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($item->expected_rate, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($purchaseOrder->goodsReceipts->isNotEmpty())
        <div class="mb-6 rounded-xl border border-border bg-canvas-raised">
            <div class="border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Goods received</h2>
            </div>
            <div class="divide-y divide-border">
                @foreach ($purchaseOrder->goodsReceipts as $receipt)
                    <div class="px-5 py-4 text-sm">
                        <p class="font-medium">Received {{ $receipt->received_date->format('d M Y') }} by {{ $receipt->receivedBy?->name ?? '—' }}</p>
                        <div class="mt-2 space-y-1">
                            @foreach ($receipt->items as $receiptItem)
                                <p class="text-ink-muted">{{ $receiptItem->batch_no ?? 'No batch no.' }} &bull; Qty {{ $receiptItem->received_qty }} &bull; Exp {{ $receiptItem->expiry_date->format('M Y') }}</p>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @if ($purchaseOrder->invoices->isNotEmpty())
        <div class="rounded-xl border border-border bg-canvas-raised">
            <div class="border-b border-border px-5 py-4">
                <h2 class="font-display font-semibold">Supplier invoices &amp; payments</h2>
            </div>
            <div class="divide-y divide-border">
                @foreach ($purchaseOrder->invoices as $invoice)
                    <div class="px-5 py-4 text-sm">
                        <div class="flex items-center justify-between">
                            <p class="font-medium">Invoice {{ $invoice->invoice_number }}</p>
                            <span class="rounded-full bg-canvas px-2.5 py-1 text-xs font-medium capitalize text-ink-muted">{{ str_replace('_', ' ', $invoice->status->value) }}</span>
                        </div>
                        <p class="mt-1 text-xs text-ink-muted">
                            Dated {{ $invoice->invoice_date->format('d M Y') }} &bull;
                            ₹{{ number_format($invoice->invoice_amount, 2) }} + ₹{{ number_format($invoice->gst_amount, 2) }} GST
                        </p>
                        @if ($invoice->payments->isNotEmpty())
                            <div class="mt-2 space-y-1">
                                @foreach ($invoice->payments as $payment)
                                    <p class="text-xs text-ink-muted">
                                        ₹{{ number_format($payment->amount_paid, 2) }} paid {{ $payment->payment_date->format('d M Y') }} via {{ str_replace('_', ' ', $payment->payment_mode) }}
                                        @if ($payment->reference_number)
                                            ({{ $payment->reference_number }})
                                        @endif
                                    </p>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.app>
