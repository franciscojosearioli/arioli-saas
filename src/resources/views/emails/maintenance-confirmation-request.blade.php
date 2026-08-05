@component('mail::message')
# ¿Hacemos el mantenimiento de este mes?

Hola **{{ $contact->name }}**,

Como todos los meses, te consultamos si querés que hagamos el mantenimiento de tu sitio — incluye un backup completo de tus archivos y tu base de datos antes de cualquier otra cosa.

Hacé clic para confirmar y arrancamos:

@component('mail::button', ['url' => $confirmUrl, 'color' => 'primary'])
Confirmar mantenimiento de este mes
@endcomponent

Apenas confirmes, generamos el backup y te lo mandamos por mail con un link de descarga. Después de eso te llega el link de pago del mantenimiento.

Este link es personal y vale por los próximos días — si no lo usás, no pasa nada, simplemente no se hace el mantenimiento de este mes.

---

Esta casilla no está monitoreada — por favor no respondas este email. Si tenés dudas, escribinos desde
[arioli.dev/contacto](https://{{ config('app.landing_domain') }}/contacto).

Gracias.
@endcomponent
