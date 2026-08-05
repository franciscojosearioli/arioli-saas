<x-admin-layout title="Clientes">

    @php
        $statusColors = [
            'gray'  => ['bg' => '#f3f4f6', 'fg' => '#374151'],
            'amber' => ['bg' => '#fef3c7', 'fg' => '#92400e'],
            'green' => ['bg' => '#d1fae5', 'fg' => '#065f46'],
            'red'   => ['bg' => '#fee2e2', 'fg' => '#991b1b'],
        ];
    @endphp

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <div>
            <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Clientes</h1>
            <p style="font-size:13px; color:var(--text-muted); margin:4px 0 0;">
                Mantenimientos, desarrollos y proyectos — independiente de las licencias SaaS.
            </p>
        </div>
        <a href="{{ route('clients.create') }}" class="btn btn-primary">Nuevo cliente</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <div class="card" style="margin-bottom:16px; padding:14px 20px;">
        <form method="GET" style="display:flex; gap:16px; align-items:center;">
            <input type="text" name="search" class="search-input" style="flex:1;" value="{{ $search }}" placeholder="Buscar por nombre...">
            <select name="status" class="form-select" style="width:220px;">
                <option value="">Todos los estados</option>
                @foreach(\App\Enums\CommercialStatus::cases() as $s)
                    <option value="{{ $s->value }}" {{ $status === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-secondary">Filtrar</button>
        </form>
    </div>

    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Proyectos</th>
                    <th>Servicios</th>
                    <th>Licencias</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('clients.show', $client) }}'">
                        <td style="font-weight:600;">{{ $client->name }}</td>
                        <td>
                            @php $c = $statusColors[$client->commercial_status->color()] ?? $statusColors['gray']; @endphp
                            <span style="padding:3px 10px; border-radius:99px; font-size:11.5px; font-weight:600; background:{{ $c['bg'] }}; color:{{ $c['fg'] }};">
                                {{ $client->commercial_status->label() }}
                            </span>
                        </td>
                        <td>
                            @php $p = $statusColors[$client->priority->color()] ?? $statusColors['gray']; @endphp
                            <span style="padding:3px 10px; border-radius:99px; font-size:11.5px; font-weight:600; background:{{ $p['bg'] }}; color:{{ $p['fg'] }};">
                                {{ $client->priority->label() }}
                            </span>
                        </td>
                        <td>{{ $client->projects_count }}</td>
                        <td>{{ $client->services_count }}</td>
                        <td>{{ $client->licenses_count }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted);">No hay clientes todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px;">
            {{ $clients->links() }}
        </div>
    </div>

</x-admin-layout>
