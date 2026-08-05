@component('mail::message')
# Contrato firmado por todas las partes

**{{ $contract->title }}** ya fue firmado por todos los firmantes requeridos.

**Firmantes:**
@foreach($contract->signers as $signer)
- {{ $signer->name }} ({{ $signer->role->label() }}) — {{ $signer->signed_at?->format('d/m/Y H:i') }}
@endforeach

Este email es tu constancia de que el proceso de firma se completó correctamente. Adjuntamos el
documento firmado en PDF.

---

Esta casilla no está monitoreada — por favor no respondas este email. Si tenés dudas, escribinos desde
[arioli.dev/contacto](https://{{ config('app.landing_domain') }}/contacto) o abrí un ticket desde tu panel de cliente.

Gracias.
@endcomponent
