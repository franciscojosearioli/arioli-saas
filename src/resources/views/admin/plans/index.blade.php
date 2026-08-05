<x-admin-layout title="Planes">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:52px; height:52px; border-radius:14px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Planes</h1>
                <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">Gestión de planes de suscripción</p>
            </div>
        </div>
        <a href="{{ route('plans.create') }}" class="btn btn-primary">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Plan
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:20px;">
            <p style="font-size:12.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Total Planes</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--text-primary); margin-top:8px; line-height:1;">{{ $totalPlans }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Planes Activos</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--success); margin-top:8px; line-height:1;">{{ $activePlans }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Licencias totales</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--accent); margin-top:8px; line-height:1;">{{ $totalLicensesFromPlans }}</h3>
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
                    placeholder="Buscar plan..."
                    oninput="debouncedFetch()">
            </div>
            <select id="productFilter" class="form-select" style="width:200px;"
                    onchange="debouncedFetch()">
                <option value="">Todos los sistemas</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ ($productFilter ?? '') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
            <span style="font-size:12px; color:var(--text-muted); white-space:nowrap;" id="searchCount">
                {{ $plans->total() }} planes
            </span>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Sistema</th>
                    <th>Período</th>
                    <th>Precio total</th>
                    <th>Precio base</th>
                    <th>Licencias</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @include('admin.plans.partials.plan-table-body', [
                    'plans'  => $plans,
                    'search' => $search ?? '',
                ])
            </tbody>
        </table>
        <div id="planPagination">
            @include('admin.plans.partials.plan-pagination', [
                'plans'  => $plans,
                'search' => $search ?? '',
            ])
        </div>
    </div>

    <script>
    let searchTimeout;

    const debouncedFetch = (() => {
        return () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchPlans(1), 300);
        };
    })();

    async function fetchPlans(page = 1) {
        const search  = document.getElementById('search')?.value.trim() ?? '';
        const product = document.getElementById('productFilter')?.value ?? '';
        const params  = new URLSearchParams();
        if (search) params.append('search', search);
        if (product) params.append('product', product);
        if (page > 1) params.append('page', page);

        try {
            const res = await fetch(`{{ route('plans.index') }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            document.getElementById('tableBody').innerHTML = data.tableBody;
            document.getElementById('planPagination').innerHTML = data.pagination;
            document.getElementById('searchCount').textContent = data.total + ' planes';
            window.history.replaceState({}, '', `?${params}`);
        } catch (e) {
            console.error(e);
        }
    }
    </script>

</x-admin-layout>