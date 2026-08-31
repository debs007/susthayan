<x-layouts.app title="GST Summary">
    @include('admin.reports._tabs')

    <x-reports.date-filter
        :action="route('admin.reports.gst-summary')"
        :from="$summary['period']['from']"
        :to="$summary['period']['to']"
    />

    <div class="grid grid-cols-3 gap-4">
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Output GST (collected on sales)</p>
            <p class="mt-1.5 font-display text-2xl font-semibold">₹{{ number_format($summary['output_gst'], 2) }}</p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Input GST (paid on purchases)</p>
            <p class="mt-1.5 font-display text-2xl font-semibold">₹{{ number_format($summary['input_gst'], 2) }}</p>
        </div>
        <div class="rounded-xl border border-border bg-canvas-raised p-5">
            <p class="text-sm text-ink-muted">Net GST payable</p>
            <p class="mt-1.5 font-display text-2xl font-semibold text-honey-600">₹{{ number_format($summary['net_gst_payable'], 2) }}</p>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-honey-500/30 bg-honey-50 px-4 py-2.5 text-sm text-honey-600">
        Summary level only - a GSTR-1 filing needs HSN-code-wise and rate-wise breakdowns, which the Sales/Purchase registers have the underlying data for but this view doesn't group by yet.
    </div>
</x-layouts.app>
