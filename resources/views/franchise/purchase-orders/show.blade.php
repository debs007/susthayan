<x-layouts.app title="PO #{{ $purchaseOrder->id }}">
    <a href="{{ route('franchise.purchase-orders.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink">
        <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        Back to purchase orders
    </a>

    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @foreach (['approval', 'grn', 'payment'] as $bag)
        @error($bag)
            <div class="mb-6 rounded-lg border border-danger-500/30 bg-danger-50 px-4 py-2.5 text-sm text-danger-600">{{ $message }}</div>
        @enderror
    @endforeach

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="font-display text-lg font-semibold">{{ $purchaseOrder->supplier->name }}</h2>
            <p class="text-sm text-ink-muted">Ordered {{ $purchaseOrder->po_date?->format('d M Y') }}</p>
        </div>
        <span class="rounded-full bg-canvas px-3 py-1 text-sm font-medium capitalize text-ink-muted">
            {{ str_replace('_', ' ', $purchaseOrder->status->value) }}
        </span>
    </div>

    {{-- Line items --}}
    <div class="mb-6 rounded-xl border border-border bg-canvas-raised">
        <div class="border-b border-border px-5 py-4"><h3 class="font-display font-semibold">Ordered items</h3></div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wide text-ink-muted">
                    <th class="px-5 py-3 font-medium">Product</th>
                    <th class="px-5 py-3 text-right font-medium">Qty</th>
                    <th class="px-5 py-3 text-right font-medium">Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @foreach ($purchaseOrder->items as $item)
                    <tr>
                        <td class="px-5 py-3">{{ $item->product->name }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs">{{ $item->ordered_qty }}</td>
                        <td class="px-5 py-3 text-right font-code text-xs">₹{{ number_format($item->expected_rate, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Approve / reject --}}
    @if ($purchaseOrder->status->value === 'pending_approval')
        <div class="mb-6 rounded-xl border border-honey-500/30 bg-honey-50 p-5">
            <p class="mb-3 text-sm font-medium text-honey-600">Awaiting your approval</p>
            <div class="flex gap-3">
                <form method="POST" action="{{ route('franchise.purchase-orders.approve', $purchaseOrder) }}">
                    @csrf
                    <button type="submit" class="rounded-lg bg-success-500 px-4 py-2 text-sm font-medium text-white hover:bg-success-600">Approve</button>
                </form>
                <form method="POST" action="{{ route('franchise.purchase-orders.reject', $purchaseOrder) }}">
                    @csrf
                    <button type="submit" class="rounded-lg border border-danger-500/30 px-4 py-2 text-sm font-medium text-danger-600 hover:bg-danger-50">Reject</button>
                </form>
            </div>
        </div>
    @endif

    {{-- GRN --}}
    @if ($purchaseOrder->status->value === 'approved')
        <form method="POST" action="{{ route('franchise.purchase-orders.receive-goods', $purchaseOrder) }}" class="mb-6 space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
            @csrf
            <h3 class="font-display font-semibold">Receive goods</h3>
            <x-form.field name="received_date" label="Received date" type="date" required :value="now()->toDateString()" />

            @foreach ($purchaseOrder->items as $index => $item)
                <div class="rounded-lg border border-border p-4">
                    <p class="mb-3 text-sm font-medium">{{ $item->product->name }} <span class="text-xs text-ink-muted">(ordered {{ $item->ordered_qty }})</span></p>
                    <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item->product_id }}">
                    <div class="grid grid-cols-3 gap-3">
                        <x-form.field name="items[{{ $index }}][batch_no]" label="Batch no" required />
                        <x-form.field name="items[{{ $index }}][expiry_date]" label="Expiry date" type="date" required />
                        <x-form.field name="items[{{ $index }}][received_qty]" label="Received qty" type="number" :value="$item->ordered_qty" required />
                        <x-form.field name="items[{{ $index }}][damaged_qty]" label="Damaged qty" type="number" :value="0" />
                        <x-form.field name="items[{{ $index }}][purchase_rate]" label="Purchase rate" type="number" step="0.01" :value="$item->expected_rate" required />
                        <x-form.field name="items[{{ $index }}][mrp]" label="MRP" type="number" step="0.01" />
                    </div>
                </div>
            @endforeach

            <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                Confirm receipt - updates stock
            </button>
        </form>
    @endif

    {{-- Goods receipts already on file --}}
    @if ($purchaseOrder->goodsReceipts->isNotEmpty())
        <div class="mb-6 rounded-xl border border-border bg-canvas-raised p-5">
            <h3 class="mb-3 font-display font-semibold">Goods received</h3>
            @foreach ($purchaseOrder->goodsReceipts as $grn)
                <p class="text-sm text-ink-muted">GRN #{{ $grn->id }} · {{ $grn->items->count() }} line(s) · received {{ $grn->received_date?->format('d M Y') }}</p>
            @endforeach
        </div>

        {{-- Invoice capture, once there's something to match against --}}
        <form method="POST" action="{{ route('franchise.purchase-orders.capture-invoice', $purchaseOrder) }}" class="mb-6 space-y-4 rounded-xl border border-border bg-canvas-raised p-5">
            @csrf
            <h3 class="font-display font-semibold">Capture supplier invoice</h3>
            <input type="hidden" name="supplier_id" value="{{ $purchaseOrder->supplier_id }}">
            <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">
            <input type="hidden" name="goods_receipt_id" value="{{ $purchaseOrder->goodsReceipts->last()->id }}">
            <div class="grid grid-cols-2 gap-4">
                <x-form.field name="invoice_number" label="Invoice number" required />
                <x-form.field name="invoice_date" label="Invoice date" type="date" required />
                <x-form.field name="invoice_amount" label="Invoice amount" type="number" step="0.01" required />
                <x-form.field name="gst_amount" label="GST amount" type="number" step="0.01" required />
                <x-form.field name="due_date" label="Due date" type="date" />
            </div>
            <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">
                Capture invoice
            </button>
        </form>
    @endif

    {{-- Invoices + payments --}}
    @if ($purchaseOrder->invoices->isNotEmpty())
        <div class="space-y-4">
            @foreach ($purchaseOrder->invoices as $invoice)
                <div class="rounded-xl border border-border bg-canvas-raised p-5">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <p class="font-medium">Invoice {{ $invoice->invoice_number }}</p>
                            <p class="font-code text-xs text-ink-muted">₹{{ number_format($invoice->invoice_amount + $invoice->gst_amount, 2) }} total</p>
                        </div>
                        <span class="rounded-full bg-canvas px-2 py-0.5 text-xs font-medium capitalize text-ink-muted">{{ str_replace('_', ' ', $invoice->status->value) }}</span>
                    </div>

                    @if ($invoice->status->value === 'approved')
                        <form method="POST" action="{{ route('franchise.purchase-orders.record-payment', [$purchaseOrder, $invoice]) }}" class="grid grid-cols-4 gap-3">
                            @csrf
                            <input type="number" step="0.01" name="amount" placeholder="Amount" required class="rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <input type="date" name="payment_date" required class="rounded-lg border border-border px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                            <select name="payment_mode" class="rounded-lg border border-border bg-canvas-raised px-3 py-2 text-sm focus:border-primary-500 focus:outline-none">
                                <option value="bank_transfer">Bank transfer</option>
                                <option value="upi">UPI</option>
                                <option value="cheque">Cheque</option>
                                <option value="cash">Cash</option>
                            </select>
                            <button type="submit" class="rounded-lg bg-honey-500 px-3 py-2 text-sm font-medium text-white hover:bg-honey-600">Record payment</button>
                        </form>
                    @endif

                    @if ($invoice->payments->isNotEmpty())
                        <div class="mt-3 space-y-1 border-t border-border pt-3">
                            @foreach ($invoice->payments as $payment)
                                <p class="font-code text-xs text-ink-muted">₹{{ number_format($payment->amount_paid, 2) }} via {{ str_replace('_', ' ', $payment->payment_mode) }} on {{ $payment->payment_date->format('d M Y') }}</p>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</x-layouts.app>
