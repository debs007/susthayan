import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                // Separate entry points for the customer-facing storefront -
                // its own brand-green token namespace (not the admin
                // portal's teal/honey one), and no manually-started Alpine
                // import here since Livewire bundles and starts its own;
                // loading both would double-initialize Alpine on the page.
                'resources/css/storefront.css',
                'resources/js/storefront.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
