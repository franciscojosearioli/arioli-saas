<x-cliente-layout title="Nameservers de {{ $domain->domain_name }}">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">Nameservers — {{ $domain->domain_name }}</h1>
            <p class="page-subtitle">Cambiar los nameservers afecta cómo resuelve tu sitio y tu email — hacelo solo si sabés lo que estás haciendo</p>
        </div>
        <a href="{{ route('cliente.domains.index') }}" class="btn btn-secondary">← Volver a Dominios</a>
    </div>

    @if($error)
        <div class="alert alert-error">{{ $error }}</div>
    @endif

    <div class="alert alert-warning">
        Un cambio de nameservers puede dejar tu sitio o email fuera de línea durante la propagación (hasta 48hs). Si no estás seguro, escribinos antes de confirmar.
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title" style="margin-bottom:14px;">Nameservers actuales</div>

            <form method="POST" action="{{ route('cliente.domains.nameservers.update', $domain) }}" onsubmit="return confirm('¿Confirmás el cambio de nameservers para {{ $domain->domain_name }}?');">
                @csrf
                @php $current = array_pad($nameservers, 4, ''); @endphp
                <div style="display:grid; gap:12px; margin-bottom:18px;">
                    @foreach($current as $i => $ns)
                        <div>
                            <label class="form-label">Nameserver {{ $i + 1 }}{{ $i < 2 ? ' (obligatorio)' : ' (opcional)' }}</label>
                            <input type="text" name="nameservers[]" class="form-input mono" value="{{ $ns }}" placeholder="ns1.ejemplo.com" {{ $i < 2 ? 'required' : '' }}>
                        </div>
                    @endforeach
                </div>
                <button type="submit" class="btn btn-primary">Guardar nameservers</button>
            </form>
        </div>
    </div>

</x-cliente-layout>
