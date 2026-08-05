<x-admin-layout title="Órdenes">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:52px; height:52px; border-radius:14px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Órdenes</h1>
                <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">Historial de pagos y contrataciones</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Total Órdenes</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--text-primary); margin-top:8px; line-height:1;">{{ $totalOrders }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Aprobadas</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--success); margin-top:8px; line-height:1;">{{ $approvedOrders }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Pendientes</p>
            <h3 style="font-size:32px; font-weight:700; color:#f59e0b; margin-top:8px; line-height:1;">{{ $pendingOrders }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Ingresos totales</p>
            <h3 style="font-size:24px; font-weight:700; color:var(--accent); margin-top:8px; line-height:1;">
                ${{ number_format($totalRevenue, 0, ',', '.') }}
            </h3>
            <p style="font-size:11px; color:var(--text-muted); margin-top:4px;">ARS</p>
        </div>
    </div>

    {{-- Search + Filter --}}
    <div class="card" style="margin-bottom:16px; padding:14px 20px;">
        <div style="display:flex; align-items:center; gap:16px;">
            <div class="search-wrap" style="flex:1;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" id="search" class="search-input"
                       value="{{ $search ?? '' }}"
                       placeholder="Buscar por cliente, email o empresa..."
                       oninput="debouncedFetch()">
            </div>
            <select id="statusFilter" class="form-select" style="width:180px;" onchange="debouncedFetch()">
                <option value="">Todos los estados</option>
                <option value="approved"  {{ $status === 'approved'  ? 'selected' : '' }}>Aprobados</option>
                <option value="pending"   {{ $status === 'pending'   ? 'selected' : '' }}>Pendientes</option>
                <option value="rejected"  {{ $status === 'rejected'  ? 'selected' : '' }}>Rechazados</option>
            </select>
            <span style="font-size:12px; color:var(--text-muted); white-space:nowrap;" id="searchCount">
                {{ $orders->total() }} órdenes
            </span>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Sistema</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>Tenant</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @include('admin.orders.partials.order-table-body', [
                    'orders' => $orders,
                    'search' => $search ?? '',
                ])
            </tbody>
        </table>
        <div id="orderPagination">
            @include('admin.orders.partials.order-pagination', [
                'orders' => $orders,
                'search' => $search ?? '',
                'status' => $status ?? '',
            ])
        </div>
    </div>

    <script>
    let searchTimeout;
    const debouncedFetch = () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => fetchOrders(1), 300);
    };

    async function fetchOrders(page = 1) {
        const search = document.getElementById('search')?.value.trim() ?? '';
        const status = document.getElementById('statusFilter')?.value ?? '';
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (status) params.append('status', status);
        if (page > 1) params.append('page', page);

        try {
            const res = await fetch(`{{ route('orders.index') }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            document.getElementById('tableBody').innerHTML = data.tableBody;
            document.getElementById('orderPagination').innerHTML = data.pagination;
            document.getElementById('searchCount').textContent = data.total + ' órdenes';
            window.history.replaceState({}, '', `?${params}`);
        } catch (e) {
            console.error(e);
        }
    }
    </script>

</x-admin-layout>