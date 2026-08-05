<x-admin-layout title="Instancias Demo">

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:52px; height:52px; border-radius:14px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Instancias Demo</h1>
                <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">Gestión de demos públicas del producto</p>
            </div>
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
        <div class="alert alert-danger" style="margin-bottom:20px;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Demo cards --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(340px,1fr)); gap:20px;">
        @foreach($demos as $demo)
        @php
            $product  = $demo['product'];
            $license  = $demo['license'];
            $isActive = $license !== null;
        @endphp
        <div class="card" style="padding:24px;">

            {{-- Product header --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:18px;">
                <div>
                    <h2 style="font-size:16px; font-weight:700; color:var(--text-primary); margin:0;">{{ $product->name }}</h2>
                    <span style="font-size:11px; color:var(--text-muted); font-family:monospace;">{{ $product->slug }}</span>
                </div>
                @if($isActive)
                    <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:99px; font-size:11.5px; font-weight:600; background:#d1fae5; color:#065f46;">
                        <span style="width:6px;height:6px;border-radius:50%;background:#10b981;display:inline-block;"></span>
                        Activa
                    </span>
                @else
                    <span style="display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:99px; font-size:11.5px; font-weight:600; background:#f3f4f6; color:#6b7280;">
                        <span style="width:6px;height:6px;border-radius:50%;background:#9ca3af;display:inline-block;"></span>
                        Sin provisionar
                    </span>
                @endif
            </div>

            @if($isActive)
                {{-- Demo info --}}
                <div style="background:#f9fafb; border-radius:8px; padding:14px; margin-bottom:16px; font-size:12.5px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <span style="color:var(--text-muted);">URL</span>
                        <a href="http://{{ $demo['demo_url'] }}" target="_blank"
                           style="color:var(--accent); font-weight:600; font-family:monospace; font-size:11.5px;">
                            {{ $demo['demo_url'] }}
                        </a>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <span style="color:var(--text-muted);">Email</span>
                        <span style="font-weight:600; font-family:monospace; font-size:11.5px;">{{ $demo['demo_email'] }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <span style="color:var(--text-muted);">Password</span>
                        <span style="font-weight:600; font-family:monospace; font-size:11.5px;">demo1234</span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:6px;">
                        <span style="color:var(--text-muted);">Versión</span>
                        <span style="font-weight:600;">{{ $license->installed_version ?? '—' }}</span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-muted);">Base de datos</span>
                        <span style="font-weight:600; color:{{ $demo['db_exists'] ? '#10b981' : '#ef4444' }};">
                            {{ $demo['db_exists'] ? 'OK' : 'No encontrada' }}
                        </span>
                    </div>
                </div>

                {{-- Actions for active demo --}}
                <div style="display:flex; gap:10px;">
                    <form method="POST" action="{{ route('demos.reset') }}" style="flex:1;">
                        @csrf
                        <input type="hidden" name="product" value="{{ $product->slug }}">
                        <button type="submit" class="btn btn-secondary" style="width:100%; font-size:12.5px;"
                            onclick="return confirm('¿Reiniciar demo de {{ $product->name }}? Los datos de prueba actuales serán eliminados.')">
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reiniciar datos
                        </button>
                    </form>
                </div>

            @else
                {{-- Provision button --}}
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:16px;">
                    Esta app no tiene una instancia demo activa. Provisionarla creará la base de datos
                    y sembrará datos de ejemplo.
                </p>
                <form method="POST" action="{{ route('demos.provision') }}">
                    @csrf
                    <input type="hidden" name="product" value="{{ $product->slug }}">
                    <button type="submit" class="btn btn-primary" style="width:100%; font-size:12.5px;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Provisionar demo
                    </button>
                </form>
            @endif

        </div>
        @endforeach
    </div>

    {{-- Info box --}}
    <div style="margin-top:28px; background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px; padding:16px 20px;">
        <p style="font-size:13px; color:#1e40af; margin:0; font-weight:500;">Información</p>
        <ul style="font-size:12.5px; color:#3b82f6; margin:8px 0 0; padding-left:18px;">
            <li>Los datos demo se reinician automáticamente cada noche a las 03:00 AM.</li>
            <li>El subdominio <code>demo</code> está reservado — no puede ser usado por clientes.</li>
            <li>Las licencias demo no tienen fecha de vencimiento.</li>
            <li>Contraseña pública: <code>demo1234</code></li>
        </ul>
    </div>

    {{-- Factory Reset --}}
    <div style="margin-top:32px; padding:28px; border:2px solid #dc2626; border-radius:14px; background:#fff1f2;">
        <div style="display:flex; align-items:flex-start; gap:14px; margin-bottom:20px;">
            <div style="width:44px; height:44px; border-radius:12px; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            </div>
            <div>
                <h2 style="font-size:16px; font-weight:700; color:#dc2626; margin:0 0 6px;">Factory Reset del Sistema</h2>
                <p style="font-size:13px; color:#7f1d1d; margin:0; line-height:1.6;">
                    Elimina <strong>completamente</strong> todos los tenants, licencias, bases de datos y órdenes que
                    <strong>NO sean demo</strong>. Deja el sistema en estado virgen con únicamente las tres instancias demo oficiales.
                    Esta acción es <strong>IRREVERSIBLE</strong>.
                </p>
            </div>
        </div>

        <div style="background:#fee2e2; border:1px solid #fca5a5; border-radius:8px; padding:12px 16px; margin-bottom:20px; font-size:12.5px; color:#991b1b;">
            <strong>Se eliminará permanentemente:</strong>
            <ul style="margin:6px 0 0; padding-left:16px; line-height:1.8;">
                <li>Todos los tenants no-demo y sus bases de datos físicas</li>
                <li>Todas las licencias de tipo comercial o sin tipo</li>
                <li>Todos los dominios asociados a tenants no-demo</li>
                <li>Todas las órdenes de compra no asociadas a demos</li>
                <li>Tenants huérfanos sin licencia demo activa</li>
            </ul>
        </div>

        <div style="background:#fff; border:1.5px solid #fca5a5; border-radius:8px; padding:12px 16px; margin-bottom:16px; font-size:13px; color:#7f1d1d;">
            Para confirmar, escribí <strong>FACTORY RESET</strong> en el campo de abajo:
        </div>

        <input type="text" id="factoryResetInput" placeholder="Escribí FACTORY RESET para habilitar el botón"
               oninput="checkFactoryReset()"
               style="width:100%; box-sizing:border-box; border:1.5px solid #fca5a5; border-radius:8px; padding:10px 14px; font-size:14px; margin-bottom:16px; outline:none; background:#fff; color:#111827; transition:border .15s;"
               onfocus="this.style.borderColor='#dc2626'" onblur="this.style.borderColor='#fca5a5'">

        <form method="POST" action="{{ route('demos.factory-reset') }}" id="factoryResetForm">
            @csrf
            <button type="submit" id="factoryResetBtn" disabled
                    style="padding:10px 24px; border:none; border-radius:8px; background:#9ca3af; color:#fff; font-size:14px; font-weight:700; cursor:not-allowed; transition:background .15s; display:flex; align-items:center; gap:8px;">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Ejecutar Factory Reset
            </button>
        </form>
    </div>

    <script>
    function checkFactoryReset() {
        const val = document.getElementById('factoryResetInput').value.trim();
        const btn = document.getElementById('factoryResetBtn');
        if (val === 'FACTORY RESET') {
            btn.disabled = false;
            btn.style.background = '#dc2626';
            btn.style.cursor = 'pointer';
        } else {
            btn.disabled = true;
            btn.style.background = '#9ca3af';
            btn.style.cursor = 'not-allowed';
        }
    }

    document.getElementById('factoryResetForm').addEventListener('submit', function(e) {
        const val = document.getElementById('factoryResetInput').value.trim();
        if (val !== 'FACTORY RESET') { e.preventDefault(); return; }
        if (!confirm('ÚLTIMA CONFIRMACIÓN\n\nEsta acción eliminará PERMANENTEMENTE todos los datos no-demo del sistema.\n\n¿Estás seguro?')) {
            e.preventDefault();
        }
    });
    </script>

</x-admin-layout>
