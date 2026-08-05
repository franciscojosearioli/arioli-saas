@component('mail::message')
# Nuevo cobro: {{ $charge->concept }}

Hola **{{ $contact->name }}**,

Te informamos que se generó un cobro por **{{ $charge->currency->value }} {{ number_format($charge->amount, 2) }}**
en concepto de **{{ $charge->concept }}**.

@if($charge->due_date)
Vencimiento: **{{ $charge->due_date->format('d/m/Y') }}**.
@endif

@if($charge->payment_url)
**Opción 1 — Mercado Pago**
@if($charge->amount_with_fee && $charge->amount_with_fee != $charge->amount)
Monto: **{{ $charge->currency->value }} {{ number_format($charge->amount_with_fee, 2) }}** (incluye la comisión de Mercado Pago).
@else
Monto: **{{ $charge->currency->value }} {{ number_format($charge->amount, 2) }}**.
@endif

@component('mail::button', ['url' => $charge->payment_url, 'color' => 'primary'])
Pagar con Mercado Pago
@endcomponent

@php $transferAlias = \App\Models\Setting::get('mercadopago.transfer_alias'); @endphp
@if($transferAlias)
**Opción 2 — Transferencia bancaria (sin comisión)**
Monto: **{{ $charge->currency->value }} {{ number_format($charge->amount, 2) }}** — el monto original, sin ningún cargo extra.
Alias: **{{ $transferAlias }}**

Si pagás por esta vía, **respondé este mismo mail adjuntando el comprobante de la transferencia** para que podamos confirmarlo.
@endif
@else
Nos vamos a poner en contacto para coordinar el medio de pago.
@endif

---

@if($charge->payment_url && \App\Models\Setting::get('mercadopago.transfer_alias'))
Si pagaste por transferencia, respondé este email con el comprobante adjunto. Para cualquier otra consulta, escribinos desde
[arioli.dev/contacto](https://{{ config('app.landing_domain') }}/contacto) o abrí un ticket desde tu panel de cliente.
@else
Esta casilla no está monitoreada — por favor no respondas este email. Si tenés dudas, escribinos desde
[arioli.dev/contacto](https://{{ config('app.landing_domain') }}/contacto) o abrí un ticket desde tu panel de cliente.
@endif

Gracias.
@endcomponent
