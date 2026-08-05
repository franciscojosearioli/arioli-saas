@component('mail::message')
# Tu backup ya está listo

Hola **{{ $contact->name }}**,

Terminamos el mantenimiento de este mes — hicimos un backup completo de tus archivos y tu base de datos antes de cualquier cambio.

@component('mail::button', ['url' => $downloadUrl, 'color' => 'primary'])
Descargar mi backup
@endcomponent

Este link es personal y vale por 7 días — pasado ese plazo dejará de funcionar, pero el backup queda igual guardado de nuestro lado.

En breve te llega por separado el cobro del mantenimiento de este mes con el link de pago.

---

Esta casilla no está monitoreada — por favor no respondas este email. Si tenés dudas, escribinos desde
[arioli.dev/contacto](https://{{ config('app.landing_domain') }}/contacto).

Gracias.
@endcomponent
