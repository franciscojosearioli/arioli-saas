@component('mail::message')
# Un firmante rechazó un contrato

**{{ $signer->name }}** ({{ $signer->role->label() }}) rechazó la firma de **{{ $contract->title }}**.

Revisá el contrato desde el panel de administración para decidir los próximos pasos.

@component('mail::button', ['url' => url('/legales/contratos/' . $contract->id), 'color' => 'primary'])
Ver contrato
@endcomponent
@endcomponent
