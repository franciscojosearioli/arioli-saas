<x-admin-layout title="Licencias">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:52px; height:52px; border-radius:14px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Licencias</h1>
                <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">Gestión de licencias de la plataforma</p>
            </div>
        </div>
        <a href="{{ route('licenses.create') }}" class="btn btn-primary">
            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva Licencia
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

    {{-- Stats --}}
    <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:24px;">
        <div class="card" style="padding:20px;">
            <p style="font-size:12.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Total Licencias</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--text-primary); margin-top:8px; line-height:1;">{{ $totalLicenses }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Activas</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--success); margin-top:8px; line-height:1;">{{ $activeLicenses }}</h3>
        </div>
        <div class="card" style="padding:20px;">
            <p style="font-size:12.5px; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; font-weight:600;">Expiradas</p>
            <h3 style="font-size:32px; font-weight:700; color:var(--danger); margin-top:8px; line-height:1;">{{ $expiredLicenses }}</h3>
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
                       placeholder="Buscar por cliente o plan..."
                       oninput="debouncedFetch()">
            </div>
            <span style="font-size:12px; color:var(--text-muted); white-space:nowrap;" id="searchCount">
                {{ $licenses->total() }} licencias
            </span>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Cliente</th>
                    <th>Plan</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Inicio</th>
                    <th>Vencimiento</th>
                    <th>Días restantes</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                @include('admin.licenses.partials.license-table-body', [
                    'licenses' => $licenses,
                    'search'   => $search ?? '',
                ])
            </tbody>
        </table>
        <div id="licensePagination">
            @include('admin.licenses.partials.license-pagination', [
                'licenses' => $licenses,
                'search'   => $search ?? '',
            ])
        </div>
    </div>

    <script>
    let searchTimeout;

    const debouncedFetch = (() => {
        return () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => fetchLicenses(1), 300);
        };
    })();

    async function fetchLicenses(page = 1) {
        const search = document.getElementById('search')?.value.trim() ?? '';
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (page > 1) params.append('page', page);

        try {
            const res = await fetch(`{{ route('licenses.index') }}?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            document.getElementById('tableBody').innerHTML = data.tableBody;
            document.getElementById('licensePagination').innerHTML = data.pagination;
            document.getElementById('searchCount').textContent = data.total + ' licencias';
            window.history.replaceState({}, '', `?${params}`);
        } catch (e) {
            console.error(e);
        }
    }
    </script>

</x-admin-layout>