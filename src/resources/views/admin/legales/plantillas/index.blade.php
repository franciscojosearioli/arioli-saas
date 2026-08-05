<x-admin-layout title="Plantillas de Contratos">

    <div style="margin-bottom:24px;">
        <a href="{{ route('legales.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Legales</a>
    </div>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Plantillas de Contratos</h1>
        <a href="{{ route('legales.plantillas.create') }}" class="btn btn-primary">Nueva plantilla</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Tipo</th>
                    <th>Versión</th>
                    <th>Contratos generados</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($templates as $template)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('legales.plantillas.edit', $template) }}'">
                        <td>{{ $template->name }}</td>
                        <td>{{ $template->type->label() }}</td>
                        <td>
                            v{{ $template->version }}
                            <a href="{{ route('legales.plantillas.versions', $template) }}" onclick="event.stopPropagation()" style="font-size:11px; color:var(--accent); margin-left:6px;">historial</a>
                        </td>
                        <td>{{ $template->contracts_count }}</td>
                        <td>
                            @if($template->active)
                                <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:#d1fae5; color:#065f46;">Activa</span>
                            @else
                                <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:#f3f4f6; color:#6b7280;">Inactiva</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center; padding:32px; color:var(--text-muted);">No hay plantillas todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px;">
            {{ $templates->links() }}
        </div>
    </div>

</x-admin-layout>
