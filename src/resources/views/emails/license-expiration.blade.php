@component('mail::message')
# Tu licencia vence en {{ $daysRemaining }} días

Hola **{{ $user->name }}**,

Te avisamos que tu licencia de **{{ $productName }}** vence el **{{ $expiresAt }}**.

@if($daysRemaining <= 7)
⚠️ **Quedan solo {{ $daysRemaining }} días.** Renová ahora para no perder el acceso a tu sistema.
@else
Tenés **{{ $daysRemaining }} días** para renovar tu licencia y seguir usando el sistema sin interrupciones.
@endif

---

**Detalles de tu licencia:**
- **Sistema:** {{ $productName }}
- **Plan:** {{ $license->plan->period_label ?? '-' }}
- **Vencimiento:** {{ $expiresAt }}
- **Días restantes:** {{ $daysRemaining }}

@component('mail::button', ['url' => $panelUrl, 'color' => 'primary'])
Ir a mi panel
@endcomponent

Si tenés alguna consulta, respondé este email o escribinos a **soporte@arioli.dev**.

Gracias,
**El equipo de Arioli.dev**

@endcomponent