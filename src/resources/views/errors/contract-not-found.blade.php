<x-admin-layout title="Contrato no disponible">

    <div style="margin-bottom:24px;">
        <a href="{{ route('legales.contratos.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Contratos</a>
    </div>

    <div class="card" style="padding:48px; text-align:center;">
        <svg width="40" height="40" fill="none" stroke="var(--text-muted)" viewBox="0 0 24 24" style="margin:0 auto 16px;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p style="font-size:14px; color:var(--text-primary); font-weight:600; margin:0 0 6px;">
            Ese contrato ya no está disponible
        </p>
        <p style="font-size:13px; color:var(--text-muted); margin:0;">
            Puede haber sido eliminado o el link que usaste ya no es válido.
        </p>
    </div>

</x-admin-layout>
