@component('mail::message')
# ¡Bienvenido a Arioli.dev, {{ $order->customer_name }}!

Tu sistema está listo para usar. A continuación encontrás todos los datos de acceso.

---

## Datos de tu cuenta

**Sistema contratado:** {{ $order->plan->product->name ?? '' }}  
**Plan:** {{ $order->plan->period_label }}  
**Válido hasta:** {{ $order->plan ? now()->addMonths($order->plan->period_months)->format('d/m/Y') : '' }}

---

## Acceso a tu sistema

**URL del sistema:** {{ $systemUrl }}  
**URL de tu panel:** {{ $loginUrl }}  
**Email:** {{ $order->customer_email }}  
**Contraseña temporal:** `{{ $password }}`

@component('mail::button', ['url' => $loginUrl, 'color' => 'primary'])
Acceder a mi panel
@endcomponent

---

## Próximos pasos

1. Ingresá con tu email y contraseña temporal
2. Cambiá tu contraseña desde el perfil
3. Comenzá a usar tu sistema en {{ $systemUrl }}

Si tenés alguna consulta, respondé este email o escribinos a **soporte@arioli.dev**.

Gracias por confiar en Arioli.dev.

@endcomponent