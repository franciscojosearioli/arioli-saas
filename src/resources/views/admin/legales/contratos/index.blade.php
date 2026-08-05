<x-admin-layout title="Contratos">

    <div style="margin-bottom:24px;">
        <a href="{{ route('legales.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Legales</a>
    </div>

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Contratos</h1>
        <a href="{{ route('legales.contratos.create') }}" class="btn btn-primary">Nuevo contrato</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <div class="card" style="margin-bottom:16px; padding:14px 20px;">
        <form method="GET" style="display:flex; gap:16px; align-items:center;">
            <input type="text" name="search" class="search-input" style="flex:1;" value="{{ $search }}" placeholder="Buscar por título o tenant...">
            <select name="status" class="form-select" style="width:200px;">
                <option value="">Todos los estados</option>
                @foreach(\App\Enums\ContractStatus::cases() as $s)
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
                    <th>Título</th>
                    <th>Tenant</th>
                    <th>Tipo</th>
                    <th>Firmantes</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                @forelse($contracts as $contract)
                    <tr style="cursor:pointer;" onclick="window.location='{{ route('legales.contratos.show', $contract) }}'">
                        <td>{{ $contract->title }}</td>
                        <td style="font-family:monospace; font-size:12px;">{{ $contract->tenant_id }}</td>
                        <td>{{ $contract->type->label() }}</td>
                        <td>{{ $contract->signers->where('status', \App\Enums\SignerStatus::Signed)->count() }}/{{ $contract->signers->count() }}</td>
                        <td>
                            @php
                                $colors = [
                                    'gray'  => ['bg' => '#f3f4f6', 'fg' => '#374151'],
                                    'amber' => ['bg' => '#fef3c7', 'fg' => '#92400e'],
                                    'green' => ['bg' => '#d1fae5', 'fg' => '#065f46'],
                                    'red'   => ['bg' => '#fee2e2', 'fg' => '#991b1b'],
                                ];
                                $c = $colors[$contract->status->color()] ?? $colors['gray'];
                            @endphp
                            <span style="padding:3px 10px; border-radius:99px; font-size:11.5px; font-weight:600; background:{{ $c['bg'] }}; color:{{ $c['fg'] }};">
                                {{ $contract->status->label() }}
                            </span>
                        </td>
                        <td style="color:var(--text-muted); font-size:12.5px;">{{ $contract->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center; padding:32px; color:var(--text-muted);">No hay contratos todavía.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding:16px 20px;">
            {{ $contracts->links() }}
        </div>
    </div>

</x-admin-layout>
