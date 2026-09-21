<!DOCTYPE html>
<html lang="en" class="storefront">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Susthayan - Your Health, Our Priority' }}</title>
    <meta name="description" content="{{ $description ?? 'Genuine medicines, at-home lab tests, and verified doctor appointments, delivered.' }}">
    @vite(['resources/css/storefront.css', 'resources/js/storefront.js'])
    @livewireStyles
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body class="storefront flex min-h-screen flex-col bg-canvas text-ink">
    @livewire('storefront.header')

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-storefront.footer />

    @livewireScripts
</body>
</html>
