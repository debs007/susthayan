<header class="sticky top-0 z-40 border-b border-line bg-surface/95 backdrop-blur" x-data="{ mobileSearchOpen: false }">
    <div class="mx-auto flex max-w-7xl items-center gap-6 px-6 py-4">
        <a href="{{ route('storefront.home') }}" class="shrink-0 font-display text-2xl font-semibold text-forest" wire:navigate>
            Susthayan
        </a>

        <nav class="hidden items-center gap-6 lg:flex">
            <a href="{{ route('storefront.home') }}" class="text-sm font-medium text-ink-soft hover:text-brand" wire:navigate>Home</a>
            <a href="{{ route('storefront.categories') }}" class="text-sm font-medium text-ink-soft hover:text-brand" wire:navigate>Medicine</a>
            <a href="{{ route('storefront.lab-tests') }}" class="text-sm font-medium text-ink-soft hover:text-brand" wire:navigate>Lab Tests</a>
            <a href="{{ route('storefront.appointments') }}" class="text-sm font-medium text-ink-soft hover:text-brand" wire:navigate>Doctors</a>
            <a href="{{ route('storefront.offers') }}" class="text-sm font-medium text-ink-soft hover:text-brand" wire:navigate>Offers</a>
        </nav>

        <form wire:submit="submitSearch" class="ml-auto hidden max-w-md flex-1 md:flex">
            <div class="flex w-full items-center gap-2 rounded-full border border-line bg-canvas px-4 py-2 focus-within:border-brand">
                <svg class="h-4 w-4 shrink-0 text-ink-faint" viewBox="0 0 20 20" fill="none">
                    <circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.6" />
                    <path d="M14 14L17.5 17.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
                </svg>
                <input
                    wire:model="search"
                    type="text"
                    placeholder="Search medicines, health products..."
                    class="w-full bg-transparent text-sm outline-none placeholder:text-ink-faint"
                >
                <button type="submit" class="shrink-0 rounded-full bg-brand px-3 py-1 text-xs font-medium text-white hover:bg-brand-hover">
                    Search
                </button>
            </div>
        </form>

        {{-- Mobile: a search icon that reveals an inline input, rather than the desktop search bar being simply hidden with no alternative at all --}}
        <button @click="mobileSearchOpen = ! mobileSearchOpen" class="ml-auto text-ink-soft hover:text-brand md:hidden" aria-label="Search">
            <svg class="h-6 w-6" viewBox="0 0 20 20" fill="none">
                <circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.6" />
                <path d="M14 14L17.5 17.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
        </button>

        <div class="flex items-center gap-5">
            <a href="{{ route('storefront.cart') }}" class="relative text-ink-soft hover:text-brand" aria-label="Cart" wire:navigate>
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none">
                    <path d="M3 4h2l2.4 12.2a2 2 0 0 0 2 1.6h7.2a2 2 0 0 0 2-1.6L20 8H6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" />
                    <circle cx="9.5" cy="20" r="1.3" fill="currentColor" />
                    <circle cx="17" cy="20" r="1.3" fill="currentColor" />
                </svg>
                @if ($this->cartCount > 0)
                    <span class="absolute -right-2 -top-2 flex h-4.5 min-w-4.5 items-center justify-center rounded-full bg-brand px-1 text-[10px] font-semibold text-white">
                        {{ $this->cartCount }}
                    </span>
                @endif
            </a>

            @auth('web')
                <a href="{{ route('storefront.account') }}" class="text-sm font-medium text-ink-soft hover:text-brand" wire:navigate>
                    {{ explode(' ', auth('web')->user()->name)[0] }}
                </a>
            @else
                <a href="{{ route('storefront.login') }}" class="rounded-full bg-brand px-4 py-2 text-sm font-medium text-white hover:bg-brand-hover" wire:navigate>
                    Log in
                </a>
            @endauth
        </div>
    </div>

    {{-- Mobile search, revealed below the header bar rather than competing for space in the main row --}}
    <div x-show="mobileSearchOpen" x-cloak x-transition class="border-t border-line px-6 py-3 md:hidden">
        <form wire:submit="submitSearch" class="flex items-center gap-2 rounded-full border border-line bg-canvas px-4 py-2 focus-within:border-brand">
            <svg class="h-4 w-4 shrink-0 text-ink-faint" viewBox="0 0 20 20" fill="none">
                <circle cx="9" cy="9" r="6.5" stroke="currentColor" stroke-width="1.6" />
                <path d="M14 14L17.5 17.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" />
            </svg>
            <input
                wire:model="search"
                type="text"
                placeholder="Search medicines, health products..."
                class="w-full bg-transparent text-sm outline-none placeholder:text-ink-faint"
                autofocus
            >
            <button type="submit" class="shrink-0 rounded-full bg-brand px-3 py-1 text-xs font-medium text-white hover:bg-brand-hover">
                Go
            </button>
        </form>
    </div>
</header>
