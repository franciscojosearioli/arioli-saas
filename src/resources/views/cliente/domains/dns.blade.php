<x-cliente-layout title="DNS de {{ $domain->domain_name }}">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">DNS — {{ $domain->domain_name }}</h1>
            <p class="page-subtitle">Registros DNS administrados directamente en Porkbun</p>
        </div>
        <a href="{{ route('cliente.domains.index') }}" class="btn btn-secondary">← Volver a Dominios</a>
    </div>

    @if($error)
        <div class="alert alert-error">{{ $error }}</div>
    @endif

    <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
            <div class="card-title" style="margin-bottom:14px;">Agregar registro</div>
            <form method="POST" action="{{ route('cliente.domains.dns.store', $domain) }}" style="display:grid; grid-template-columns:110px 1fr 1.4fr 90px auto; gap:10px; align-items:end;">
                @csrf
                <div>
                    <label class="form-label">Tipo</label>
                    <select name="type" class="form-select" required>
                        @foreach(['A','AAAA','CNAME','MX','TXT','SRV','CAA','ALIAS'] as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Nombre (subdominio)</label>
                    <input type="text" name="name" class="form-input" placeholder="www (vacío = raíz)">
                </div>
                <div>
                    <label class="form-label">Contenido</label>
                    <input type="text" name="content" class="form-input" placeholder="192.0.2.1" required>
                </div>
                <div>
                    <label class="form-label">TTL</label>
                    <input type="number" name="ttl" class="form-input" value="600" min="600">
                </div>
                <button type="submit" class="btn btn-primary" style="padding:9px 18px;">Agregar</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div style="overflow-x:auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Nombre</th>
                        <th>Contenido</th>
                        <th>TTL</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                        <tr>
                            <td><span class="badge badge-blue">{{ $record['type'] }}</span></td>
                            <td class="mono">{{ $record['name'] }}</td>
                            <td class="mono" style="word-break:break-all;">{{ $record['content'] }}</td>
                            <td>{{ $record['ttl'] }}</td>
                            <td>
                                <form method="POST" action="{{ route('cliente.domains.dns.destroy', [$domain, $record['id']]) }}" onsubmit="return confirm('¿Eliminar este registro DNS?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-delete">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center; color:var(--text-muted); padding:30px;">No hay registros DNS cargados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-cliente-layout>
