@component('mail::message')
# @if($isReminder)Recordatorio: tenés un documento pendiente de firma@else Tenés un documento para firmar @endif

Hola {{ $signer->name }},

@if($isReminder)
Te recordamos que **{{ $contract->title }}** está esperando tu firma.
@else
**{{ $contract->title }}** requiere tu firma como **{{ $signer->role->label() }}**.
@endif

@component('mail::button', ['url' => $signingUrl, 'color' => 'primary'])
Revisar y firmar
@endcomponent

Este link es personal y vence el {{ $signer->signing_token_expires_at->format('d/m/Y') }}.

Adjuntamos el documento en PDF para tu registro.

---

Esta casilla no está monitoreada — por favor no respondas este email. Si tenés dudas, escribinos desde
[arioli.dev/contacto](https://{{ config('app.landing_domain') }}/contacto) o abrí un ticket desde tu panel de cliente.

Gracias.
@endcomponent
