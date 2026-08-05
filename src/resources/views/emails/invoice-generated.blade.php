@component('mail::message')
# Factura {{ $invoice->number ?? '(borrador)' }}

Hola,

Te adjuntamos la factura por **{{ $invoice->currency }} {{ number_format($invoice->amount, 2) }}**@if($invoice->notes) en concepto de "{{ $invoice->notes }}"@endif.

@if($invoice->isDraft())
Nota: esta factura todavía es un comprobante de numeración interna (borrador) — no es válida como comprobante fiscal.
@endif

Cualquier consulta, escribinos desde [arioli.dev/contacto](https://{{ config('app.landing_domain') }}/contacto) o abrí un ticket desde tu panel de cliente.

Gracias.
@endcomponent
