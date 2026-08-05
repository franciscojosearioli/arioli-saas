@component('mail::message')
# {{ $assetLabel }} vence en {{ $daysRemaining }} días

Hola **{{ $contact->name }}**,

Te avisamos que tu **{{ $assetLabel }}** vence el **{{ $expiresAt }}**.

@if($daysRemaining <= 7)
⚠️ **Quedan solo {{ $daysRemaining }} días.** Te recomendamos renovarlo cuanto antes.
@else
Tenés **{{ $daysRemaining }} días** para renovarlo sin quedarte sin servicio.
@endif

---

@if($renewalPayer === 'arioli')
La renovación de este servicio la gestiona Arioli.dev por vos — nos vamos a poner en contacto
para coordinar el pago si todavía no lo hiciste.
@else
La renovación corre por tu cuenta — si querés que te ayudemos a gestionarla, escribinos.
@endif

---

Esta casilla no está monitoreada — por favor no respondas este email. Si tenés dudas, escribinos desde
[arioli.dev/contacto](https://{{ config('app.landing_domain') }}/contacto) o abrí un ticket desde tu panel de cliente.

Gracias.
@endcomponent
