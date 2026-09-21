<x-layouts.storefront :title="$title.' - Susthayan'">
    <div class="mx-auto flex min-h-[50vh] max-w-xl flex-col items-center justify-center px-6 text-center">
        <p class="font-display text-2xl font-medium text-forest">{{ $title }}</p>
        <p class="mt-2 text-sm text-ink-soft">This page is being built - check back soon.</p>
        <a href="{{ route('storefront.home') }}" wire:navigate class="mt-6 rounded-full bg-brand px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-hover">
            Back to home
        </a>
    </div>
</x-layouts.storefront>
