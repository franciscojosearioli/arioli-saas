<x-admin-layout title="Clientes">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:52px; height:52px; border-radius:14px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Clientes</h1>
                <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">Administración de clientes y licencias SaaS</p>
            </div>
        </div>
        <a href="{{ route('tenants.create') }}" class="btn btn-primary">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Cliente
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:24px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:20px;">
            <p style="font-size:12.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Total Clientes</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--text-primary); margin-top:8px; line-height:1;">{{ $totalTenants }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Licencias Activas</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--success); margin-top:8px; line-height:1;">{{ $activeLicenses }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Nuevos este mes</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--accent); margin-top:8px; line-height:1;">{{ $monthlyNew }}</h3>
        </div>
    </div>

    {{-- Search --}}
    <div class="card" style="margin-bottom:16px; padding:14px 20px;">
        <div style="display:flex; align-items:center; justify-content:space-between; gap:16px;">
            <div class="search-wrap" style="flex:1;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                </svg>
                <input type="text" id="search" class="search-input"
                    value="{{ $search ?? '' }}"
                    placeholder="Buscar por nombre, email o subdominio..."
                    oninput="debouncedFetch()">
            </div>
            <span style="font-size:12px; color:var(--text-muted); white-space:nowrap;" id="searchCount">
                {{ $tenants->total() }} clientes
            </span>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <table class="data-table" id="tenantsTable">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Dominio</th>
                    <th>Email</th>
                    <th>Alta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @include('admin.tenants.partials.tenant-table-body', [
                    'tenants' => $tenants,
                    'domains' => $domains,
                    'search'  => $search ?? '',
                ])
            </tbody>
        </table>
        <div id="tenantPagination">
            @include('admin.tenants.partials.tenant-pagination', [
                'tenants' => $tenants,
                'search'  => $search ?? '',
            ])
        </div>
    </div>

    <script>
    let searchTimeout;

    const debouncedFetch = (() => {
        return () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchTenants(1), 300);
        };
    })();

    async function fetchTenants(page = 1) {
        const search = document.getElementById('search')?.value.trim() ?? '';
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (page > 1) params.append('page', page);

        try {
            const res = await fetch(`{{ route('tenants.index') }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            document.getElementById('tableBody').innerHTML = data.tableBody;
            document.getElementById('tenantPagination').innerHTML = data.pagination;
            document.getElementById('searchCount').textContent = `${data.total} clientes`;
            window.history.replaceState({}, '', `?${params}`);
        } catch (e) {
            console.error(e);
        }
    }

    document.addEventListener('click', e => {
        const link = e.target.closest('#tenantPagination a');
        if (!link) return;
        e.preventDefault();
        const page = new URL(link.href).searchParams.get('page') ?? 1;
        fetchTenants(page);
    });
    </script>

</x-admin-layout>