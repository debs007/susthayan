// Alpine is no longer imported/started manually here - Livewire now
// provides its own bundled Alpine instance (via @livewireScripts in
// components/layouts/app.blade.php), which is a full drop-in replacement
// including every existing x-data/x-show usage already in this admin
// panel. Per Livewire's own troubleshooting docs: running a manually
// installed Alpine alongside Livewire's bundled one causes "Detected
// multiple instances of Alpine running" and breaks $wire entirely. Don't
// re-add an Alpine import here without removing @livewireScripts first.
