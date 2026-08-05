<x-cliente-layout title="Hosting">

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
            <h1 class="page-title">Hosting</h1>
            <p class="page-subtitle">Tus cuentas de hosting y el acceso a cada una</p>
        </div>
    </div>

    @forelse($hostings as $hosting)
        @php
            $account = $hosting->account;
            $system = $hosting->projects->first();
            $domain = $system?->domain;
            $specs = $hosting->hostingPlan?->specs_json;
        @endphp
        <div class="card" style="margin-bottom:20px;">
            <div class="card-body">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px; flex-wrap:wrap; gap:10px;">
                    <div>
                        <div style="font-size:16px; font-weight:700; color:var(--text-primary);">
                            {{ $hosting->hostingPlan?->name ?? $hosting->plan ?? 'Hosting' }}
                        </div>
                        <div style="font-size:12.5px; color:var(--text-muted); margin-top:2px;">
                            {{ $hosting->provider }}{{ $domain ? ' · ' . $domain->domain_name : '' }}
                        </div>
                        @if($system?->license)
                            <span class="badge badge-green" style="margin-top:6px;">Sistema con licencia: {{ $system->license->plan?->product?->name ?? $system->name }}</span>
                        @endif
                    </div>
                    <span class="badge {{ $badgeClass($hosting->status->color()) }}">{{ $hosting->status->label() }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Vencimiento</span>
                    <span class="detail-value">{{ $hosting->expires_at?->format('d/m/Y') ?? 'Sin vencimiento' }}</span>
                </div>
                @if($hosting->provisioned_at)
                    <div class="detail-row">
                        <span class="detail-label">Activo desde</span>
                        <span class="detail-value">{{ $hosting->provisioned_at->format('d/m/Y') }}</span>
                    </div>
                @endif

                @if($specs)
                    <div style="margin-top:14px; padding-top:14px; border-top:1px solid var(--card-border);">
                        <div class="card-title" style="margin-bottom:10px;">Recursos incluidos</div>
                        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:10px;">
                            @foreach($specs as $key => $value)
                                <div style="font-size:12.5px;">
                                    <div style="color:var(--text-muted); text-transform:capitalize;">{{ str_replace('_', ' ', $key) }}</div>
                                    <div style="color:var(--text-primary); font-weight:600;">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div style="margin-top:16px; padding-top:16px; border-top:1px solid var(--card-border); display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                    @if($domain)
                        <a href="https://{{ $domain->domain_name }}" target="_blank" class="btn btn-secondary" style="font-size:12.5px; padding:8px 16px;">Ver sitio</a>
                        <a href="https://webmail.{{ $domain->domain_name }}" target="_blank" class="btn btn-secondary" style="font-size:12.5px; padding:8px 16px;">Webmail</a>
                    @endif
                    @if($account)
                        @if($account->credential_claimed_at)
                            <a href="{{ $account->panel_url }}" target="_blank" class="btn btn-primary" style="font-size:12.5px; padding:8px 16px;">
                                Acceder al panel de hosting
                            </a>
                            <span style="font-size:12px; color:var(--text-muted);">
                                Usuario: <span class="mono">{{ $account->remote_username }}</span>
                            </span>
                        @else
                            <form method="POST" action="{{ route('cliente.hosting.resend-access', $hosting) }}">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="font-size:12.5px; padding:8px 16px;">
                                    Reenviar acceso al hosting
                                </button>
                            </form>
                            <span style="font-size:12px; color:var(--text-muted);">Todavía no definiste tu contraseña de acceso.</span>
                        @endif
                    @else
                        <span style="font-size:12.5px; color:var(--text-muted);">Tu cuenta de hosting todavía se está preparando — te avisamos apenas esté lista.</span>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="empty-state">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>
                <strong>No tenés hosting contratado</strong>
                <p style="margin-top:4px;">Escribinos si querés contratar uno.</p>
            </div>
        </div>
    @endforelse

</x-cliente-layout>
