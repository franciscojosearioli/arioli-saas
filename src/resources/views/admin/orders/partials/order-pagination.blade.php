<div style="display:flex; align-items:center; justify-content:space-between; padding:14px 20px; border-top:1px solid var(--card-border);">
    <span style="font-size:12.5px; color:var(--text-muted);">
        @if($orders->total() > 0)
            Mostrando {{ $orders->firstItem() }} a {{ $orders->lastItem() }} de {{ $orders->total() }} orden{{ $orders->total() === 1 ? '' : 'es' }}
        @else
            Sin resultados
        @endif
    </span>

    @if($orders->hasPages())
        <div style="display:flex; gap:4px;">
            @if($orders->onFirstPage())
                <span style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-muted); background:var(--body-bg); cursor:not-allowed;">‹</span>
            @else
                <a href="javascript:void(0)" onclick="fetchOrders({{ $orders->currentPage() - 1 }})"
                   style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-secondary); background:var(--body-bg); text-decoration:none;">‹</a>
            @endif

            @foreach($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                @if($page == $orders->currentPage())
                    <span style="padding:6px 11px; border-radius:7px; font-size:13px; background:var(--accent); color:#fff; font-weight:600;">{{ $page }}</span>
                @else
                    <a href="javascript:void(0)" onclick="fetchOrders({{ $page }})"
                       style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-secondary); background:var(--body-bg); text-decoration:none;">{{ $page }}</a>
                @endif
            @endforeach

            @if($orders->hasMorePages())
                <a href="javascript:void(0)" onclick="fetchOrders({{ $orders->currentPage() + 1 }})"
                   style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-secondary); background:var(--body-bg); text-decoration:none;">›</a>
            @else
                <span style="padding:6px 11px; border-radius:7px; font-size:13px; color:var(--text-muted); background:var(--body-bg); cursor:not-allowed;">›</span>
            @endif
        </div>
    @endif
</div>