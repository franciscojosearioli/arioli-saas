<x-admin-layout title="{{ $client->name }}">

    @php
        $statusColors = [
            'gray'  => ['bg' => '#f3f4f6', 'fg' => '#374151'],
            'amber' => ['bg' => '#fef3c7', 'fg' => '#92400e'],
            'green' => ['bg' => '#d1fae5', 'fg' => '#065f46'],
            'red'   => ['bg' => '#fee2e2', 'fg' => '#991b1b'],
        ];
        $badge = fn($enum) => $statusColors[$enum->color()] ?? $statusColors['gray'];
        $scoreColor = $healthScore['total'] >= 80 ? '#065f46' : ($healthScore['total'] >= 50 ? '#92400e' : '#991b1b');
        $sectionHeader = 'font-size:11px; font-weight:700; color:var(--accent); text-transform:uppercase; letter-spacing:.08em; margin:24px 0 10px; padding-left:2px;';
    @endphp

    <div style="margin-bottom:24px;">
        <a href="{{ route('clients.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Clientes</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error" style="margin-bottom:20px;">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    {{-- Header --}}
    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:20px; gap:20px; flex-wrap:wrap;">
        <div>
            <div style="display:flex; align-items:center; gap:10px;">
                <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0;">{{ $client->name }}</h1>
                @php $c = $badge($client->commercial_status); @endphp
                <span style="padding:3px 10px; border-radius:99px; font-size:11.5px; font-weight:600; background:{{ $c['bg'] }}; color:{{ $c['fg'] }};">{{ $client->commercial_status->label() }}</span>
                @php $p = $badge($client->priority); @endphp
                <span style="padding:3px 10px; border-radius:99px; font-size:11.5px; font-weight:600; background:{{ $p['bg'] }}; color:{{ $p['fg'] }};">Prioridad {{ $client->priority->label() }}</span>
            </div>
            <div style="display:flex; gap:6px; margin-top:8px; flex-wrap:wrap;">
                @foreach($client->tags as $tag)
                    <form method="POST" action="{{ route('clients.tags.destroy', [$client, $tag]) }}" style="display:inline;" onsubmit="return confirm('¿Quitar etiqueta?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="border:1px solid #e5e7eb; background:#f9fafb; border-radius:99px; padding:2px 10px; font-size:11px; color:var(--text-muted); cursor:pointer;">{{ $tag->name }} ×</button>
                    </form>
                @endforeach
                <form method="POST" action="{{ route('clients.tags.store', $client) }}" style="display:inline;">
                    @csrf
                    <input type="text" name="name" placeholder="+ etiqueta" style="border:1px dashed #d1d5db; border-radius:99px; padding:2px 10px; font-size:11px; width:100px;">
                </form>
            </div>
            <div style="display:flex; gap:10px; margin-top:14px;">
                <a href="{{ route('clients.edit', $client) }}" class="btn btn-secondary">Editar</a>
                @if($client->portalUsers->isNotEmpty())
                    <form method="POST" action="{{ route('clients.impersonate', $client) }}" target="_blank">
                        @csrf
                        <button type="submit" class="btn btn-primary">Entrar como este cliente ↗</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('¿Eliminar este cliente? Se eliminan también sus proyectos, servicios y cobros.')">
                    @csrf @method('DELETE')
                    <button class="btn btn-secondary" style="color:#dc2626;">Eliminar</button>
                </form>
            </div>
        </div>

        <div style="display:flex; align-items:center; gap:24px;">
            <div style="text-align:right;">
                <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">Valor mensual</div>
                <div style="font-size:19px; font-weight:700; color:var(--text-primary);">${{ number_format($economicSummary['mensual'], 0, ',', '.') }}</div>
                <div style="font-size:11px; color:var(--text-muted);">${{ number_format($economicSummary['anual'], 0, ',', '.') }} / año</div>
            </div>
            <div style="text-align:right;">
                <div style="font-size:11px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em;">Pagado / Pendiente</div>
                @forelse($pendingChargesSummary['pagado']->keys()->merge($pendingChargesSummary['pendiente']->keys())->unique() as $cur)
                    <div style="font-size:13px; font-weight:700; color:var(--success);">
                        {{ $cur }} {{ number_format($pendingChargesSummary['pagado']->get($cur, 0), 0, ',', '.') }}
                        <span style="color:#92400e; font-weight:700;">/ {{ number_format($pendingChargesSummary['pendiente']->get($cur, 0), 0, ',', '.') }}</span>
                    </div>
                @empty
                    <div style="font-size:13px; color:var(--text-muted);">Sin cobros</div>
                @endforelse
            </div>
            <div style="display:flex; align-items:center; gap:12px;">
                <div style="width:56px; height:56px; border-radius:50%; background:conic-gradient({{ $scoreColor }} {{ $healthScore['total'] }}%, #e5e7eb 0); display:flex; align-items:center; justify-content:center; flex-shrink:0; position:relative;">
                    <div style="position:absolute; inset:5px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:{{ $scoreColor }};">{{ $healthScore['total'] }}</div>
                </div>
                <div style="font-size:11px; min-width:130px;">
                    <div style="display:flex; justify-content:space-between; gap:8px; color:var(--text-muted);"><span>Infraestructura</span><span>{{ $healthScore['infraestructura'] }}/25</span></div>
                    <div style="display:flex; justify-content:space-between; gap:8px; color:var(--text-muted);"><span>Seguridad</span><span>{{ $healthScore['seguridad'] }}/25</span></div>
                    <div style="display:flex; justify-content:space-between; gap:8px; color:var(--text-muted);"><span>Mantenimiento</span><span>{{ $healthScore['mantenimiento'] }}/25</span></div>
                    <div style="display:flex; justify-content:space-between; gap:8px; color:var(--text-muted);"><span>Documentación</span><span>{{ $healthScore['documentacion'] }}/25</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div style="display:flex; gap:4px; border-bottom:1px solid var(--card-border); margin-bottom:24px; overflow-x:auto;">
        @foreach($tabs as $key => $label)
            <a href="{{ route('clients.show', ['client' => $client, 'tab' => $key]) }}"
               style="padding:10px 16px; font-size:13.5px; font-weight:600; white-space:nowrap; text-decoration:none;
                      color:{{ $activeTab === $key ? 'var(--accent)' : 'var(--text-muted)' }};
                      border-bottom:2px solid {{ $activeTab === $key ? 'var(--accent)' : 'transparent' }};">
                {{ $label }}
            </a>
        @endforeach
    </div>

    @include("admin.clients.tabs.{$activeTab}")

</x-admin-layout>
