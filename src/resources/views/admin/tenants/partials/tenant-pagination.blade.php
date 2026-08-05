<div style="display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-top:1px solid var(--card-border);">
    <span style="font-size:12.5px; color:var(--text-muted);">
        @if($tenants->total() > 0)
            Mostrando {{ $tenants->firstItem() }} a {{ $tenants->lastItem() }} de {{ $tenants->total() }} cliente{{ $tenants->total() === 1 ? '' : 's' }}{{ !empty($search) ? ' para "' . e($search) . '"' : '' }}
        @else
            Sin resultados
        @endif
    </span>

    @if($tenants->hasPages())
        <div style="display:flex; gap:4px;">
            @if($tenants->onFirstPage())
                <span style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-muted); background:var(--body-bg); cursor:not-allowed;">‹</span>
            @else
                <a href="javascript:void(0)"
                   onclick="fetchTenants({{ $tenants->currentPage() - 1 }})"
                   style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-secondary); background:var(--body-bg); text-decoration:none;">‹</a>
            @endif

            @foreach($tenants->getUrlRange(1, $tenants->lastPage()) as $page => $url)
                @if($page == $tenants->currentPage())
                    <span style="padding:6px 11px; border-radius:7px; font-size:13px; background:var(--accent); color:#fff; font-weight:600;">{{ $page }}</span>
                @else
                    <a href="javascript:void(0)"
                       onclick="fetchTenants({{ $page }})"
                       style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-secondary); background:var(--body-bg); text-decoration:none;">{{ $page }}</span>
                @endif
            @endforeach

            @if($tenants->hasMorePages())
                <a href="javascript:void(0)"
                   onclick="fetchTenants({{ $tenants->currentPage() + 1 }})"
                   style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-secondary); background:var(--body-bg); text-decoration:none;">›</a>
            @else
                <span style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-muted); background:var(--body-bg); cursor:not-allowed;">›</span>
            @endif
        </div>
    @endif
</div>