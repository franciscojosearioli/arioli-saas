<x-cliente-layout title="Dominios">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @php
        $badgeClass = fn ($color) => match ($color) {
            'green' => 'badge-green',
            'red' => 'badge-red',
            'amber' => 'badge-yellow',
            default => 'badge-gray',
        };
    @endphp

    <div class="page-header">
        <div>
            <h1 class="page-title">Dominios</h1>
            <p class="page-subtitle">Tus dominios, DNS y nameservers</p>
        </div>
    </div>

    @forelse($domains as $domain)
        @php
            $isPorkbun = $domain->dns_provider === 'porkbun';
            $system = $domain->projects->first();
        @endphp
        <div class="card" style="margin-bottom:20px;">
            <div class="card-body">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; flex-wrap:wrap; gap:10px;">
                    <div>
                        <div style="font-size:16px; font-weight:700; color:var(--text-primary);">{{ $domain->domain_name }}</div>
                        <div style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">
                            Vencimiento: {{ $domain->expires_at?->format('d/m/Y') ?? '—' }}
                        </div>
                        @if($system?->license)
                            <span class="badge badge-green" style="margin-top:6px;">Sistema con licencia: {{ $system->license->plan?->product?->name ?? $system->name }}</span>
                        @elseif($system?->hosting)
                            <span class="badge badge-gray" style="margin-top:6px;">Para el hosting: {{ $system->hosting->provider }}</span>
                        @endif
                    </div>
                    <span class="badge {{ $badgeClass($domain->status->color()) }}">{{ $domain->status->label() }}</span>
                </div>

                @php $ticketUrl = route('cliente.tickets.create', ['related' => \App\Models\ClientDomain::class . ':' . $domain->id]); @endphp
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <form method="POST" action="{{ route('cliente.domains.renew', $domain) }}">
                        @csrf
                        <button type="submit" class="btn {{ $domain->renewalStatusLabel() !== 'Activo' ? 'btn-primary' : 'btn-secondary' }}" style="font-size:12px; padding:7px 14px;">
                            Renovar
                        </button>
                    </form>

                    @if($isPorkbun)
                        <a href="{{ route('cliente.domains.dns', $domain) }}" class="btn btn-secondary" style="font-size:12px; padding:7px 14px;">Administrar DNS</a>
                        <a href="{{ route('cliente.domains.nameservers', $domain) }}" class="btn btn-secondary" style="font-size:12px; padding:7px 14px;">Cambiar Nameservers</a>
                    @else
                        <a href="{{ $ticketUrl }}" class="btn btn-secondary" style="font-size:12px; padding:7px 14px;">Administrar DNS</a>
                        <a href="{{ $ticketUrl }}" class="btn btn-secondary" style="font-size:12px; padding:7px 14px;">Cambiar Nameservers</a>
                    @endif

                    <form method="POST" action="{{ route('cliente.domains.transfer', $domain) }}" onsubmit="return confirm('¿Confirmás que querés solicitar la transferencia de {{ $domain->domain_name }} a otro registrador? Te vamos a contactar con los datos.');">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="font-size:12px; padding:7px 14px;">Transferir</button>
                    </form>
                </div>

                @if(! $isPorkbun)
                    <p style="font-size:12px; color:var(--text-muted); margin-top:10px;">
                        Para hacer cualquier gestión sobre este dominio (DNS, nameservers, transferencia) envianos un
                        <a href="{{ $ticketUrl }}" style="color:var(--accent);">ticket de soporte</a> solicitando el trámite.
                    </p>
                @endif
            </div>
        </div>
    @empty
        <div class="card">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM3.6 9h16.8M3.6 15h16.8M12 3a15 15 0 010 18M12 3a15 15 0 000 18"/></svg>
                <strong>No tenés dominios registrados</strong>
                <p style="margin-top:4px;">Escribinos si querés registrar uno.</p>
            </div>
        </div>
    @endforelse

</x-cliente-layout>
