<x-admin-layout title="Historial de versiones">

    <div style="margin-bottom:24px;">
        <a href="{{ route('legales.plantillas.edit', $template) }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a {{ $template->name }}</a>
    </div>

    <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0 0 20px;">Historial — {{ $template->name }}</h1>

    <div class="card" style="padding:24px; margin-bottom:16px; border:2px solid var(--accent);">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
            <span style="font-size:13px; font-weight:700; color:var(--accent);">Versión actual — v{{ $template->version }}</span>
        </div>
        <div style="white-space:pre-wrap; font-size:13px; color:var(--text-primary); background:#f9fafb; border-radius:8px; padding:14px; max-height:200px; overflow-y:auto;">{{ $template->content }}</div>
    </div>

    @forelse($versions as $version)
        <div class="card" style="padding:24px; margin-bottom:16px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                <span style="font-size:13px; font-weight:700; color:var(--text-primary);">v{{ $version->version }}</span>
                <span style="font-size:12px; color:var(--text-muted);">
                    {{ $version->created_at->format('d/m/Y H:i') }}
                    @if($version->createdBy) · {{ $version->createdBy->name }} @endif
                </span>
            </div>
            <div style="white-space:pre-wrap; font-size:13px; color:var(--text-muted); background:#f9fafb; border-radius:8px; padding:14px; max-height:200px; overflow-y:auto;">{{ $version->content }}</div>
        </div>
    @empty
        <div class="card" style="padding:32px; text-align:center; color:var(--text-muted); font-size:13px;">
            Todavía no hay versiones anteriores — esta plantilla no fue editada desde que se creó.
        </div>
    @endforelse

</x-admin-layout>
