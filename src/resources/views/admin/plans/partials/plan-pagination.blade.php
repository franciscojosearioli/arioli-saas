<div style="display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-top:1px solid var(--card-border);">
    <span style="font-size:12.5px; color:var(--text-muted);">
        @if($plans->total() > 0)
            Mostrando {{ $plans->firstItem() }} a {{ $plans->lastItem() }} de {{ $plans->total() }} plan{{ $plans->total() === 1 ? '' : 'es' }}{{ !empty($search) ? ' para "' . e($search) . '"' : '' }}
        @else
            Sin resultados
        @endif
    </span>

    @if($plans->hasPages())
        <div style="display:flex; gap:4px;">
            @if($plans->onFirstPage())
                <span style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-muted); background:var(--body-bg); cursor:not-allowed;">‹</span>
            @else
                <a href="javascript:void(0)"
                   onclick="fetchPlans({{ $plans->currentPage() - 1 }})"
                   style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-secondary); background:var(--body-bg); text-decoration:none;">‹</a>
            @endif

            @foreach($plans->getUrlRange(1, $plans->lastPage()) as $page => $url)
                @if($page == $plans->currentPage())
                    <span style="padding:6px 11px; border-radius:7px; font-size:13px; background:var(--accent); color:#fff; font-weight:600;">{{ $page }}</span>
                @else
                    <a href="javascript:void(0)"
                       onclick="fetchPlans({{ $page }})"
                       style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-secondary); background:var(--body-bg); text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach

            @if($plans->hasMorePages())
                <a href="javascript:void(0)"
                   onclick="fetchPlans({{ $plans->currentPage() + 1 }})"
                   style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-secondary); background:var(--body-bg); text-decoration:none;">›</a>
            @else
                <span style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-muted); background:var(--body-bg); cursor:not-allowed;">›</span>
            @endif
        </div>
    @endif
</div>