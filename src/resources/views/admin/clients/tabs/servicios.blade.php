{{-- Servicios --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    @php $hestiaProjectIds = $client->projects->filter(fn ($p) => $p->hosting?->account?->provider === 'hestiacp')->pluck('id'); @endphp
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Servicios</h3>
        <x-admin.modal id="add-service" title="Agregar servicio" trigger-label="+ Agregar servicio" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
            <form method="POST" action="{{ route('clients.services.store', $client) }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                @csrf
                <select name="service_type" class="form-select">
                    @foreach(\App\Enums\ServiceType::cases() as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                    @endforeach
                </select>
                <select name="billing_cycle" class="form-select">
                    @foreach(\App\Enums\BillingCycle::cases() as $b)
                        <option value="{{ $b->value }}">{{ $b->label() }}</option>
                    @endforeach
                </select>
                <input type="number" step="0.01" name="amount" class="form-input" placeholder="Monto" required>
                <select name="project_id" class="form-select">
                    <option value="">— Sin proyecto —</option>
                    @foreach($client->projects as $proj)
                        <option value="{{ $proj->id }}">{{ $proj->name }}{{ $hestiaProjectIds->contains($proj->id) ? ' (HestiaCP)' : '' }}</option>
                    @endforeach
                </select>
                <label style="font-size:11px; color:var(--text-secondary); display:flex; align-items:center; gap:5px; grid-column:span 2;">
                    <input type="checkbox" name="auto_maintenance_hestia" value="1">
                    Mantenimiento automático: pedir confirmación el día 1, backup en HestiaCP y cobrar después (necesita elegir un proyecto con hosting HestiaCP arriba)
                </label>
                <input type="hidden" name="status" value="active">
                <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Agregar servicio</button>
            </form>
        </x-admin.modal>
    </div>
    @foreach($client->services as $service)
        @php $sc = $badge($service->status); @endphp
        <div style="padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $service->service_type->label() }}</div>
                    <div style="font-size:11.5px; color:var(--text-muted);">
                        {{ $service->billing_cycle->label() }} — ${{ number_format($service->amount, 2) }}
                        @if($service->project) · {{ $service->project->name }} @endif
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }};">{{ $service->status->label() }}</span>
                    <form method="POST" action="{{ route('clients.services.destroy', [$client, $service]) }}" onsubmit="return confirm('¿Eliminar servicio?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;">×</button>
                    </form>
                </div>
            </div>

            @if($service->auto_maintenance_hestia)
                @php
                    $backupBadge = match($service->last_backup_status) {
                        'done' => ['#d1fae5', '#065f46', '✓ Backup al día'],
                        'failed' => ['#fee2e2', '#991b1b', '✗ Backup falló'],
                        default => ['#f3f4f6', '#374151', '— Sin backup todavía'],
                    };
                @endphp
                <div style="margin-top:6px; font-size:11px; display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                    <span style="padding:2px 8px; border-radius:99px; font-weight:600; background:{{ $backupBadge[0] }}; color:{{ $backupBadge[1] }};">{{ $backupBadge[2] }}</span>
                    <span style="color:var(--text-muted);">
                        🔄 Mantenimiento automático
                        @if($service->maintenanceConfirmedThisMonth()) · confirmado este mes @elseif($service->maintenanceRequestedThisMonth()) · esperando confirmación @endif
                        @if($service->last_backup_at) · último backup {{ $service->last_backup_at->format('d/m/Y H:i') }} @endif
                    </span>
                </div>
            @endif

            <x-admin.modal id="edit-service-{{ $service->id }}" title="Editar servicio" trigger-label="✎ Editar">
                <form method="POST" action="{{ route('clients.services.update', [$client, $service]) }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                    @csrf @method('PATCH')
                    <select name="service_type" class="form-select">
                        @foreach(\App\Enums\ServiceType::cases() as $t)
                            <option value="{{ $t->value }}" {{ $service->service_type === $t ? 'selected' : '' }}>{{ $t->label() }}</option>
                        @endforeach
                    </select>
                    <select name="billing_cycle" class="form-select">
                        @foreach(\App\Enums\BillingCycle::cases() as $b)
                            <option value="{{ $b->value }}" {{ $service->billing_cycle === $b ? 'selected' : '' }}>{{ $b->label() }}</option>
                        @endforeach
                    </select>
                    <input type="number" step="0.01" name="amount" class="form-input" value="{{ $service->amount }}" required>
                    <select name="project_id" class="form-select">
                        <option value="">— Sin proyecto —</option>
                        @foreach($client->projects as $proj)
                            <option value="{{ $proj->id }}" {{ $service->project_id === $proj->id ? 'selected' : '' }}>{{ $proj->name }}{{ $hestiaProjectIds->contains($proj->id) ? ' (HestiaCP)' : '' }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="form-select">
                        <option value="active" {{ $service->status->value === 'active' ? 'selected' : '' }}>Activo</option>
                        <option value="cancelled" {{ $service->status->value === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                        <option value="completed" {{ $service->status->value === 'completed' ? 'selected' : '' }}>Completado</option>
                    </select>
                    <label style="font-size:12px; color:var(--text-secondary); display:flex; align-items:center; gap:5px;">
                        <input type="checkbox" name="auto_renew" value="1" {{ $service->auto_renew ? 'checked' : '' }}> Renovación automática
                    </label>
                    <label style="font-size:12px; color:var(--text-secondary); display:flex; align-items:center; gap:5px; grid-column:span 2;">
                        <input type="checkbox" name="auto_maintenance_hestia" value="1" {{ $service->auto_maintenance_hestia ? 'checked' : '' }}>
                        Mantenimiento automático: pedir confirmación el día 1, backup en HestiaCP y cobrar después
                    </label>
                    <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Guardar cambios</button>
                </form>
            </x-admin.modal>
        </div>
    @endforeach
</div>
