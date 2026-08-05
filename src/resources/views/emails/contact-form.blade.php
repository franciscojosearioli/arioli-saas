@component('mail::message')
# @if($type === 'contacto')
Nueva consulta general
@elseif($type === 'partner')
Nueva consulta de partner
@elseif($type === 'servicio')
Consulta de servicio: {{ $productName }}
@else
Consulta sobre {{ $productName }}
@endif

**Nombre:** {{ $name }}
**Email:** {{ $email }}
@if($phone)
**Teléfono:** {{ $phone }}
@endif
@if($company)
**Empresa:** {{ $company }}
@endif
@if($inquiryType)
**Tipo de contratación:** {{ $inquiryType === 'completo' ? 'Sistema completo, sin licencia' : 'Licencia SaaS' }}
@endif

---

{{ $body }}

---

Podés responder directamente a este email para contactar a {{ $name }}.
@endcomponent
