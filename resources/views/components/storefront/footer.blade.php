<footer class="mt-24 border-t border-line bg-surface">
    <div class="mx-auto max-w-7xl px-6 py-14">
        <div class="grid grid-cols-2 gap-10 md:grid-cols-4">
            <div class="col-span-2 md:col-span-1">
                <p class="font-display text-xl font-semibold text-forest">Susthayan</p>
                <p class="mt-3 max-w-xs text-sm text-ink-soft">Your health, our priority. Medicines, lab tests, and doctor consultations, all in one place.</p>
            </div>

            <div>
                <p class="text-sm font-semibold text-ink">Shop</p>
                <ul class="mt-3 space-y-2 text-sm text-ink-soft">
                    <li><a href="{{ route('storefront.categories') }}" class="hover:text-brand">All categories</a></li>
                    <li><a href="{{ route('storefront.offers') }}" class="hover:text-brand">Offers &amp; coupons</a></li>
                    <li><a href="{{ route('storefront.lab-tests') }}" class="hover:text-brand">Lab tests</a></li>
                </ul>
            </div>

            <div>
                <p class="text-sm font-semibold text-ink">Health</p>
                <ul class="mt-3 space-y-2 text-sm text-ink-soft">
                    <li><a href="{{ route('storefront.appointments') }}" class="hover:text-brand">Book a doctor</a></li>
                    <li><a href="{{ route('storefront.health-articles') }}" class="hover:text-brand">Health articles</a></li>
                </ul>
            </div>

            <div>
                <p class="text-sm font-semibold text-ink">Account</p>
                <ul class="mt-3 space-y-2 text-sm text-ink-soft">
                    <li><a href="{{ route('storefront.orders') }}" class="hover:text-brand">Your orders</a></li>
                    <li><a href="{{ route('storefront.account') }}" class="hover:text-brand">Your account</a></li>
                </ul>
            </div>
        </div>

        <p class="mt-12 text-xs text-ink-faint">&copy; {{ now()->year }} Susthayan. All rights reserved.</p>
    </div>
</footer>
