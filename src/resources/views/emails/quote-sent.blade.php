@component('mail::message')
# Tenés una propuesta de Arioli.dev

Hola,

Te enviamos la propuesta **{{ $quote->title }}**
@if(count($quote->totalsByCurrency()))
por un total de **{{ collect($quote->totalsByCurrency())->map(fn ($amount, $currency) => "{$currency} " . number_format($amount, 2))->implode(' + ') }}**.
@else
.
@endif

Podés revisarla y aceptarla o rechazarla desde el siguiente link:

@component('mail::button', ['url' => $publicUrl, 'color' => 'primary'])
Ver propuesta
@endcomponent

@if($quote->valid_until)
Esta propuesta es válida hasta el {{ $quote->valid_until->format('d/m/Y') }}.
@endif

Adjuntamos el detalle en PDF para tu registro.

---

Esta casilla no está monitoreada — por favor no respondas este email. Si tenés dudas, escribinos desde
[arioli.dev/contacto](https://{{ config('app.landing_domain') }}/contacto) o abrí un ticket desde tu panel de cliente.

Gracias.
@endcomponent
