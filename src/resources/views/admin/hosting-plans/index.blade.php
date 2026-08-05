<x-admin-layout title="Planes de Hosting">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
        <div>
            <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Planes de Hosting</h1>
            <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">Catálogo de planes vendibles en /contratar-hosting</p>
        </div>
        <a href="{{ route('hosting-plans.create') }}" class="btn btn-primary">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Plan de Hosting
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Ciclo</th>
                    <th>Hostings vendidos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plans as $plan)
                    <tr>
                        <td>
                            <div style="font-weight:600;">{{ $plan->name }}</div>
                            @if($plan->marketing_description)
                                <div style="font-size:12px; color:var(--text-muted);">{{ $plan->marketing_description }}</div>
                            @endif
                        </td>
                        <td class="mono">${{ number_format($plan->price, 0, ',', '.') }} {{ $plan->currency }}</td>
                        <td>{{ $plan->billing_cycle->label() }}</td>
                        <td>{{ $plan->hostings_count }}</td>
                        <td>
                            @if($plan->active)
                                <span class="badge badge-green">Activo</span>
                            @else
                                <span class="badge badge-gray">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('hosting-plans.edit', $plan) }}" class="action-btn action-edit">Editar</a>
                            <form method="POST" action="{{ route('hosting-plans.destroy', $plan) }}" style="display:inline;" onsubmit="return confirm('¿Eliminar este plan de hosting?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-btn action-delete">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">
                        <div class="empty-state"><strong>Todavía no hay planes de hosting cargados</strong></div>
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $plans->links() }}

</x-admin-layout>
