@props(['title' => null])

<x-cliente-layouts.app :title="$title">
    {{ $slot }}
</x-cliente-layouts.app>