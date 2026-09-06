<x-layouts.app title="Send Notification">
    <div class="mb-6">
        <h1 class="font-display text-lg font-semibold">Send Notification</h1>
        <p class="text-sm text-ink-muted">Sends an in-app notification to one customer - it shows up in their Notifications tab, no push/SMS involved.</p>
    </div>

    <x-form.errors />

    @if (session('success'))
        <div class="mb-6 flex items-center gap-2 rounded-lg border border-success-500/30 bg-success-50 px-4 py-2.5 text-sm text-success-600">
            <svg viewBox="0 0 24 24" fill="none" class="h-4 w-4 flex-shrink-0"><path d="m5 13 4 4L19 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-2xl space-y-6">
        @if ($preselected)
            <div class="rounded-xl border border-border bg-canvas-raised p-6">
                <p class="mb-4 text-sm text-ink-muted">Sending to:</p>
                <div class="mb-5 flex items-center justify-between rounded-lg bg-canvas p-3">
                    <div>
                        <p class="font-medium">{{ $preselected->name }}</p>
                        <p class="text-xs text-ink-muted">+91 {{ $preselected->mobile }}</p>
                    </div>
                    <a href="{{ route('admin.notifications.create') }}" class="text-xs text-primary-500 hover:underline">Change</a>
                </div>

                <form method="POST" action="{{ route('admin.notifications.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $preselected->id }}">
                    <x-form.field name="title" label="Title" required placeholder="e.g. Your order is ready" />
                    <x-form.textarea name="message" label="Message" required :rows="4" placeholder="Write the notification message here..." />
                    <button type="submit" class="w-full rounded-lg bg-primary-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-primary-600">
                        Send Notification
                    </button>
                </form>
            </div>
        @else
            <div class="rounded-xl border border-border bg-canvas-raised p-6">
                <form method="GET" class="flex gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer by name or mobile number" class="w-full rounded-lg border border-border px-3 py-2 text-sm" autofocus>
                    <button type="submit" class="rounded-lg bg-primary-500 px-4 py-2 text-sm font-medium text-white hover:bg-primary-600">Search</button>
                </form>
            </div>

            @if (request('search'))
                <div class="rounded-xl border border-border bg-canvas-raised">
                    @forelse ($customers as $customer)
                        <a href="{{ route('admin.notifications.create', ['user_id' => $customer->id]) }}" class="flex items-center justify-between border-b border-border p-4 last:border-0 hover:bg-canvas">
                            <div>
                                <p class="font-medium">{{ $customer->name }}</p>
                                <p class="text-xs text-ink-muted">+91 {{ $customer->mobile }}</p>
                            </div>
                            <span class="text-xs font-medium text-primary-500">Compose →</span>
                        </a>
                    @empty
                        <p class="p-6 text-center text-sm text-ink-muted">No customers match that search.</p>
                    @endforelse
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
