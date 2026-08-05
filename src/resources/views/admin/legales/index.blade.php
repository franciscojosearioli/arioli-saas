<x-admin-layout title="Legales">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:52px; height:52px; border-radius:14px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Legales</h1>
                <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">Contratos, plantillas y firma electrónica</p>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('legales.plantillas.index') }}" class="btn btn-secondary">Plantillas</a>
            <a href="{{ route('legales.contratos.create') }}" class="btn btn-primary">Nuevo contrato</a>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Borradores</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--text-primary); margin-top:8px; line-height:1;">{{ $stats['draft'] }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Pendientes de firma</p>
            <h3 style="font-size:32px; font-weight:700; color:#f59e0b; margin-top:8px; line-height:1;">{{ $stats['pending'] }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Firmados</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--success); margin-top:8px; line-height:1;">{{ $stats['signed'] }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Rechazados</p>
            <h3 style="font-size:32px; font-weight:700; color:#dc2626; margin-top:8px; line-height:1;">{{ $stats['rejected'] }}</h3>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        {{-- Pendientes de firma --}}
        <div class="card">
            <div style="padding:18px 20px; border-bottom:1px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:15px; font-weight:700; color:var(--text-primary); margin:0;">Pendientes de firma</h3>
                <a href="{{ route('legales.contratos.index', ['status' => 'pending_signature']) }}" style="font-size:12.5px; color:var(--accent); text-decoration:none;">Ver todos</a>
            </div>
            <div>
                @forelse($pending as $contract)
                    <a href="{{ route('legales.contratos.show', $contract) }}" style="display:block; padding:14px 20px; border-bottom:1px solid #f9fafb; text-decoration:none; color:inherit;">
                        <div style="font-size:13.5px; font-weight:600; color:var(--text-primary);">{{ $contract->title }}</div>
                        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">
                            {{ $contract->tenant_id }} —
                            {{ $contract->signers->where('status', \App\Enums\SignerStatus::Signed)->count() }}/{{ $contract->signers->count() }} firmas
                        </div>
                    </a>
                @empty
                    <div style="padding:32px; text-align:center; color:var(--text-muted); font-size:13px;">Nada pendiente.</div>
                @endforelse
            </div>
        </div>

        {{-- Firmados recientemente --}}
        <div class="card">
            <div style="padding:18px 20px; border-bottom:1px solid #f3f4f6;">
                <h3 style="font-size:15px; font-weight:700; color:var(--text-primary); margin:0;">Firmados recientemente</h3>
            </div>
            <div>
                @forelse($recentlySigned as $contract)
                    <a href="{{ route('legales.contratos.show', $contract) }}" style="display:block; padding:14px 20px; border-bottom:1px solid #f9fafb; text-decoration:none; color:inherit;">
                        <div style="font-size:13.5px; font-weight:600; color:var(--text-primary);">{{ $contract->title }}</div>
                        <div style="font-size:12px; color:var(--text-muted); margin-top:2px;">{{ $contract->tenant_id }} — {{ $contract->updated_at->format('d/m/Y') }}</div>
                    </a>
                @empty
                    <div style="padding:32px; text-align:center; color:var(--text-muted); font-size:13px;">Todavía no hay contratos firmados.</div>
                @endforelse
            </div>
        </div>

    </div>

</x-admin-layout>
