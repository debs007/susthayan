<div class="mb-6 flex flex-wrap gap-2 text-sm">
    <a href="{{ route('admin.reports.sales-register') }}" class="rounded-lg px-3 py-1.5 font-medium {{ request()->routeIs('admin.reports.sales-register') ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas' }}">Sales register</a>
    <a href="{{ route('admin.reports.purchase-register') }}" class="rounded-lg px-3 py-1.5 font-medium {{ request()->routeIs('admin.reports.purchase-register') ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas' }}">Purchase register</a>
    <a href="{{ route('admin.reports.gst-summary') }}" class="rounded-lg px-3 py-1.5 font-medium {{ request()->routeIs('admin.reports.gst-summary') ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas' }}">GST summary</a>
    <a href="{{ route('admin.reports.payment-mismatches') }}" class="rounded-lg px-3 py-1.5 font-medium {{ request()->routeIs('admin.reports.payment-mismatches') ? 'bg-primary-50 text-primary-600' : 'text-ink-muted hover:bg-canvas' }}">Payment mismatches</a>
</div>
