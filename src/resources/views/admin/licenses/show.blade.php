<x-admin-layout title="Detalle de Licencia">

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
                <div style="display:flex; align-items:center; gap:10px;">
                    <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Licencia #{{ $license->id }}</h1>
                    @if($license->isDemo())
                        <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 12px; border-radius:99px; background:#fef3c7; color:#92400e; font-size:11px; font-weight:700; letter-spacing:.05em; text-transform:uppercase;">
                            <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                            DEMO
                        </span>
                    @else
                        <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 12px; border-radius:99px; background:#d1fae5; color:#065f46; font-size:11px; font-weight:700; letter-spacing:.05em; text-transform:uppercase;">
                            <svg width="8" height="8" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                            PRODUCCIÓN
                        </span>
                    @endif
                </div>
                <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">{{ $tenant?->id ?? $license->tenant_id }}</p>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="{{ route('licenses.edit', $license) }}" class="btn btn-primary">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <a href="{{ route('licenses.index') }}" class="btn" style="background:var(--bg-secondary); color:var(--text-primary); border:1px solid var(--border);">
                Volver
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert" style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; border-radius:8px; padding:12px 16px; display:flex; align-items:center; gap:8px; margin-bottom:20px; font-size:13.5px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

        {{-- Main info --}}
        <div class="card" style="padding:24px;">
            <h2 style="font-size:15px; font-weight:700; color:var(--text-primary); margin:0 0 18px;">Información de la licencia</h2>
            <div style="display:flex; flex-direction:column; gap:14px;">

                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                    <span style="font-size:13px; color:var(--text-muted);">Estado</span>
                    @if($license->isValid())
                        <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:99px; background:#d1fae5; color:#065f46; font-size:12px; font-weight:600;">
                            <svg width="10" height="10" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="4"/></svg>
                            Activa
                        </span>
                    @elseif($license->isExpired())
                        <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:99px; background:#fee2e2; color:#991b1b; font-size:12px; font-weight:600;">
                            Expirada
                        </span>
                    @else
                        <span style="display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:99px; background:#f3f4f6; color:#6b7280; font-size:12px; font-weight:600;">
                            Inactiva
                        </span>
                    @endif
                </div>

                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                    <span style="font-size:13px; color:var(--text-muted);">Tipo</span>
                    @if($license->isDemo())
                        <span style="display:inline-flex; align-items:center; padding:3px 10px; border-radius:99px; background:#fef3c7; color:#92400e; font-size:12px; font-weight:600;">Demo</span>
                    @elseif(in_array($license->license_type, ['comercial', 'commercial']))
                        <span style="display:inline-flex; align-items:center; padding:3px 10px; border-radius:99px; background:#d1fae5; color:#065f46; font-size:12px; font-weight:600;">Comercial</span>
                    @else
                        <span style="font-size:13px; color:var(--text-muted);">{{ $license->license_type ?? '—' }}</span>
                    @endif
                </div>

                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                    <span style="font-size:13px; color:var(--text-muted);">Producto</span>
                    <span style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $license->plan?->product?->name ?? '—' }}</span>
                </div>

                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                    <span style="font-size:13px; color:var(--text-muted);">Plan</span>
                    <span style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $license->plan?->name ?? '—' }}</span>
                </div>

                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                    <span style="font-size:13px; color:var(--text-muted);">Tenant</span>
                    <span style="font-size:13px; font-weight:600; color:var(--text-primary); font-family:var(--font-mono);">{{ $license->tenant_id }}</span>
                </div>

                @if($license->plan?->product?->public_domain)
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                        <span style="font-size:13px; color:var(--text-muted);">URL de acceso</span>
                        @php $accessUrl = 'https://' . $license->tenant_id . '.' . $license->plan->product->public_domain . '.' . config('app.tenant_domain'); @endphp
                        <a href="{{ $accessUrl }}" target="_blank" style="font-size:12px; color:var(--accent); font-family:var(--font-mono); text-decoration:none;">
                            {{ $license->tenant_id }}.{{ $license->plan->product->public_domain }}.{{ config('app.tenant_domain') }}
                            <svg style="display:inline; vertical-align:middle; margin-left:3px;" width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                @endif

                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                    <span style="font-size:13px; color:var(--text-muted);">Fecha de inicio</span>
                    <span style="font-size:13px; color:var(--text-primary);">{{ $license->starts_at ? \Carbon\Carbon::parse($license->starts_at)->format('d/m/Y') : '—' }}</span>
                </div>

                <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                    <span style="font-size:13px; color:var(--text-muted);">Fecha de vencimiento</span>
                    <span style="font-size:13px; color:var(--text-primary);">
                        @if($license->isDemo())
                            <span style="color:var(--success); font-weight:600;">Sin vencimiento</span>
                        @else
                            {{ $license->expires_at ? \Carbon\Carbon::parse($license->expires_at)->format('d/m/Y') : '—' }}
                        @endif
                    </span>
                </div>

                @if(!$license->isDemo() && $license->expires_at)
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                        <span style="font-size:13px; color:var(--text-muted);">Días restantes</span>
                        @php $days = now()->diffInDays(\Carbon\Carbon::parse($license->expires_at), false); @endphp
                        <span style="font-size:13px; font-weight:600; color:{{ $days < 0 ? 'var(--danger)' : ($days < 30 ? '#d97706' : 'var(--success)') }};">
                            {{ $days < 0 ? abs($days).' días vencido' : $days.' días' }}
                        </span>
                    </div>
                @endif

                <div style="display:flex; justify-content:space-between;">
                    <span style="font-size:13px; color:var(--text-muted);">Versión instalada</span>
                    <span style="font-size:13px; color:var(--text-primary);">{{ $license->installed_version ?? '—' }}</span>
                </div>

            </div>
        </div>

        {{-- Tenant info --}}
        <div class="card" style="padding:24px;">
            <h2 style="font-size:15px; font-weight:700; color:var(--text-primary); margin:0 0 18px;">Datos del tenant</h2>
            @if($tenant)
                <div style="display:flex; flex-direction:column; gap:14px;">
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                        <span style="font-size:13px; color:var(--text-muted);">ID</span>
                        <span style="font-size:13px; font-weight:600; color:var(--text-primary); font-family:var(--font-mono);">{{ $tenant->id }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                        <span style="font-size:13px; color:var(--text-muted);">Tipo</span>
                        @if($tenant->is_demo ?? false)
                            <span style="padding:2px 8px; border-radius:99px; background:#fef3c7; color:#92400e; font-size:11px; font-weight:700; text-transform:uppercase;">Demo</span>
                        @else
                            <span style="padding:2px 8px; border-radius:99px; background:#d1fae5; color:#065f46; font-size:11px; font-weight:700; text-transform:uppercase;">Producción</span>
                        @endif
                    </div>
                    <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                        <span style="font-size:13px; color:var(--text-muted);">Creado</span>
                        <span style="font-size:13px; color:var(--text-primary);">{{ $tenant->created_at?->format('d/m/Y H:i') ?? '—' }}</span>
                    </div>
                    @foreach($tenant->data ?? [] as $key => $value)
                        @if(is_string($value) || is_numeric($value))
                            <div style="display:flex; justify-content:space-between; padding-bottom:12px; border-bottom:1px solid var(--border);">
                                <span style="font-size:13px; color:var(--text-muted);">{{ ucfirst(str_replace('_',' ',$key)) }}</span>
                                <span style="font-size:13px; color:var(--text-primary);">{{ $value }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p style="font-size:13px; color:var(--text-muted);">Información del tenant no disponible.</p>
            @endif
        </div>

    </div>

    {{-- Demo credentials --}}
    @if($license->isDemo() && !empty($demoCredentials))
    <div class="card" style="padding:24px; margin-top:20px; border:1.5px solid #f59e0b;">
        <h2 style="font-size:15px; font-weight:700; color:#92400e; margin:0 0 6px; display:flex; align-items:center; gap:8px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
            Credenciales de acceso demo
        </h2>
        <p style="font-size:12.5px; color:#a16207; margin:0 0 16px;">Estas son las credenciales configuradas para el entorno demo de este producto.</p>
        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #fde68a;">
                    <th style="text-align:left; padding:6px 12px 8px 0; font-size:11px; font-weight:700; color:#78350f; text-transform:uppercase; letter-spacing:.06em;">Rol</th>
                    <th style="text-align:left; padding:6px 12px 8px 0; font-size:11px; font-weight:700; color:#78350f; text-transform:uppercase; letter-spacing:.06em;">Email</th>
                    <th style="text-align:left; padding:6px 0 8px; font-size:11px; font-weight:700; color:#78350f; text-transform:uppercase; letter-spacing:.06em;">Contraseña</th>
                </tr>
            </thead>
            <tbody>
                @foreach($demoCredentials as $cred)
                <tr style="border-bottom:1px solid #fef3c7;">
                    <td style="padding:8px 12px 8px 0; font-size:13px; font-weight:600; color:#92400e;">{{ $cred['role'] }}</td>
                    <td style="padding:8px 12px 8px 0; font-size:13px; color:#78350f; font-family:var(--font-mono);">{{ $cred['email'] }}</td>
                    <td style="padding:8px 0; font-size:13px; color:#78350f; font-family:var(--font-mono);">{{ $cred['password'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Danger zone --}}
    <div class="card" style="padding:24px; margin-top:20px; border:1px solid var(--danger);">
        <h2 style="font-size:15px; font-weight:700; color:var(--danger); margin:0 0 12px;">Zona peligrosa</h2>

        @if($license->isDemo() && $productSlug)
        <div style="padding-bottom:20px; margin-bottom:20px; border-bottom:1px solid #fee2e2;">
            <p style="font-size:13px; color:var(--text-muted); margin:0 0 12px;">
                Resetear el entorno demo elimina todos los datos generados por usuarios y restaura el estado inicial.
                Esta acción se ejecuta en segundo plano.
            </p>
            <button type="button" onclick="openResetModal()"
                    class="btn" style="background:#f59e0b; color:#fff; border:none;">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:inline; vertical-align:middle; margin-right:5px;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Resetear Demo
            </button>
        </div>
        @endif

        <p style="font-size:13px; color:var(--text-muted); margin:0 0 16px;">Eliminar esta licencia es una acción irreversible.</p>
        <form method="POST" action="{{ route('licenses.destroy', $license) }}"
              onsubmit="return confirm('¿Confirmar eliminación de la licencia #{{ $license->id }}?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn" style="background:var(--danger); color:#fff; border:none;">
                Eliminar licencia
            </button>
        </form>
    </div>

    {{-- Reset Demo Modal --}}
    @if($license->isDemo() && $productSlug)
    <div id="resetModal" style="display:none; position:fixed; inset:0; z-index:1000; background:rgba(0,0,0,.55); align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:32px; max-width:440px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,.25);">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                <div style="width:44px; height:44px; border-radius:12px; background:#fef3c7; color:#d97706; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0;">Resetear entorno demo</h3>
                    <p style="font-size:12.5px; color:#6b7280; margin:2px 0 0;">Producto: <strong>{{ $productSlug }}</strong></p>
                </div>
            </div>
            <p style="font-size:13.5px; color:#374151; line-height:1.6; margin:0 0 16px;">
                Esta acción eliminará <strong>todos los datos generados</strong> en el entorno demo y los restaurará al estado inicial configurado por el seeder.
            </p>
            <div style="background:#fef3c7; border:1px solid #fde68a; border-radius:8px; padding:10px 14px; margin-bottom:20px; font-size:12.5px; color:#92400e;">
                Para confirmar, escribí <strong>RESET</strong> en el campo de abajo:
            </div>
            <input type="text" id="resetConfirmInput" placeholder="Escribí RESET para confirmar"
                   oninput="checkResetConfirm()"
                   style="width:100%; box-sizing:border-box; border:1.5px solid #d1d5db; border-radius:8px; padding:9px 12px; font-size:14px; margin-bottom:16px; outline:none; transition:border .15s;"
                   onfocus="this.style.borderColor='#f59e0b'" onblur="this.style.borderColor='#d1d5db'">
            <div style="display:flex; gap:10px; justify-content:flex-end;">
                <button type="button" onclick="closeResetModal()"
                        style="padding:8px 18px; border:1px solid #d1d5db; border-radius:8px; background:#fff; color:#374151; font-size:13.5px; cursor:pointer;">
                    Cancelar
                </button>
                <form method="POST" action="{{ route('licenses.reset-demo', $license) }}" id="resetDemoForm">
                    @csrf
                    <button type="submit" id="resetDemoBtn" disabled
                            style="padding:8px 18px; border:none; border-radius:8px; background:#9ca3af; color:#fff; font-size:13.5px; cursor:not-allowed; font-weight:600; transition:background .15s;">
                        Confirmar Reset
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function openResetModal() {
        document.getElementById('resetModal').style.display = 'flex';
        document.getElementById('resetConfirmInput').value = '';
        document.getElementById('resetDemoBtn').disabled = true;
        document.getElementById('resetDemoBtn').style.background = '#9ca3af';
        document.getElementById('resetDemoBtn').style.cursor = 'not-allowed';
        setTimeout(() => document.getElementById('resetConfirmInput').focus(), 50);
    }

    function closeResetModal() {
        document.getElementById('resetModal').style.display = 'none';
    }

    function checkResetConfirm() {
        const val = document.getElementById('resetConfirmInput').value.trim();
        const btn = document.getElementById('resetDemoBtn');
        if (val === 'RESET') {
            btn.disabled = false;
            btn.style.background = '#d97706';
            btn.style.cursor = 'pointer';
        } else {
            btn.disabled = true;
            btn.style.background = '#9ca3af';
            btn.style.cursor = 'not-allowed';
        }
    }

    document.getElementById('resetModal').addEventListener('click', function(e) {
        if (e.target === this) closeResetModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeResetModal();
    });
    </script>
    @endif

</x-admin-layout>
