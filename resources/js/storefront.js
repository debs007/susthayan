// Deliberately does NOT import or start Alpine here, unlike
// resources/js/app.js (the admin portal's entry). Livewire 3 bundles
// and starts its own Alpine instance automatically via the
// @livewireScripts directive - importing a second copy here would
// double-initialize Alpine on every storefront page.
