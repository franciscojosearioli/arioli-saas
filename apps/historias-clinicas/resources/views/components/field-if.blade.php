@props(['entidad', 'campo'])
@if (app(\App\Platform\FieldVisibilityResolver::class)->isVisible($entidad, $campo))
{{ $slot }}
@endif
