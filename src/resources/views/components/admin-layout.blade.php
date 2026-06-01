@props(['title' => null])

<x-layouts.admin>
    <x-slot name="title">
        {{ $title }}
    </x-slot>

    {{ $slot }}
</x-layouts.admin>