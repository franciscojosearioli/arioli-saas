<x-admin-layout title="Detalle del Plan">

    <div style="margin-bottom:24px;">
        <a href="{{ route('plans.index') }}"
           style="font-size:13px; color:var(--text-muted); text-decoration:none;">
            ← Volver al listado
        </a>
    </div>

    <div class="card" style="max-width:520px; padding:28px;">

        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:24px;">
            <div>
                <h2 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0;">{{ $plan->name }}</h2>
                <p style="font-size:13px; color:var(--text-muted); margin:4px 0 0;">Plan de suscripción</p>
            </div>
            @if($plan->active)
                <span class="badge badge-green">Activo</span>
            @else
                <span class="badge badge-red">Inactivo</span>
            @endif
        </div>

        <dl style="display:flex; flex-direction:column; gap:14px; margin-bottom:28px;">
            <div style="display:flex; justify-content:space-between; font-size:13.5px; padding-bottom:14px; border-bottom:1px solid var(--card-border);">
                <dt style="color:var(--text-muted);">Precio mensual</dt>
                <dd style="font-weight:700; font-size:18px; color:var(--text-primary);">${{ number_format($plan->price, 2) }}</dd>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                <dt style="color:var(--text-muted);">Usuarios máx.</dt>
                <dd style="color:var(--text-primary); font-weight:600;">{{ $plan->max_users ?? 'Ilimitados' }}</dd>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                <dt style="color:var(--text-muted);">Sucursales máx.</dt>
                <dd style="color:var(--text-primary); font-weight:600;">{{ $plan->max_branches ?? 'Ilimitadas' }}</dd>
            </div>
            <div style="display:flex; justify-content:space-between; font-size:13.5px;">
                <dt style="color:var(--text-muted);">Licencias asignadas</dt>
                <dd style="color:var(--accent); font-weight:700; font-size:16px;">{{ $plan->licenses_count }}</dd>
            </div>
        </dl>

        <div style="display:flex; gap:10px;">
            <a href="{{ route('plans.edit', $plan->id) }}" class="btn btn-primary">Editar</a>
            <form method="POST" action="{{ route('plans.destroy', $plan->id) }}"
                  onsubmit="return confirm('¿Eliminar este plan?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
        </div>

    </div>

</x-admin-layout>