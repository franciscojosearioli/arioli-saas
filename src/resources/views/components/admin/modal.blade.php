@props([
    'id',
    'title',
    'triggerLabel',
    'triggerClass' => 'modal-trigger',
    'triggerStyle' => '',
    'wide' => false,
])

<button type="button" class="{{ $triggerClass }}" style="{{ $triggerStyle }}" onclick="document.getElementById('{{ $id }}').showModal()">{{ $triggerLabel }}</button>

<dialog id="{{ $id }}" class="admin-modal {{ $wide ? 'admin-modal-wide' : '' }}">
    <div class="admin-modal-header">
        <h3>{{ $title }}</h3>
        <button type="button" class="admin-modal-close" onclick="document.getElementById('{{ $id }}').close()">&times;</button>
    </div>
    <div class="admin-modal-body">
        {{ $slot }}
    </div>
</dialog>
