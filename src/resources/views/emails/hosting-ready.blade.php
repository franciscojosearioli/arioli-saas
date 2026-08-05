@component('mail::message')
# Tu hosting ya está listo

Hola **{{ $contact->name }}**,

Recibimos tu pago y tu cuenta de hosting ya está activa.

Tu usuario es: **{{ $username }}**

Por seguridad, no te mandamos ninguna contraseña por mail — hacé clic en el botón de abajo para definir vos mismo tu contraseña de acceso.

@component('mail::button', ['url' => $credentialsUrl])
Definir mi contraseña
@endcomponent

Este link es personal y expira en 7 días.

---

Esta casilla no está monitoreada — por favor no respondas este email. Si tenés dudas, escribinos desde
[arioli.dev/contacto](https://{{ config('app.landing_domain') }}/contacto).

Gracias.
@endcomponent
