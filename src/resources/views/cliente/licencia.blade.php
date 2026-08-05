<x-cliente-layout :title="'Gestionar — ' . ($license->plan->product->name ?? 'Licencia')">

    @if(session('success'))
        <div class="alert alert-success">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        </div>
    @endif

    @php
        $isValid   = $license->isValid();
        $isExpired = $license->isExpired();
        $days      = $license->daysRemaining();
        $product   = $license->plan->product;
    @endphp

    {{-- Back link + header --}}
    <div style="margin-bottom:24px;">
        <a href="{{ route('cliente.licencias') }}"
           style="font-size:13px; color:var(--text-muted); text-decoration:none; display:inline-flex; align-items:center; gap:5px; margin-bottom:12px;">
            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Mis Licencias
        </a>
        <div class="page-header" style="margin-bottom:0;">
            <div>
                <h1 class="page-title">{{ $product->name }}</h1>
                <p class="page-subtitle">Gestión de licencia y configuración</p>
            </div>
            @if($domain)
                <a href="http://{{ $domain->domain }}" target="_blank" class="btn btn-primary">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Acceder al sistema
                </a>
            @endif
        </div>
    </div>

    {{-- ── 1. ESTADO DE LA LICENCIA ── --}}
    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">

            <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:14px;">
                    <div style="width:44px; height:44px; border-radius:12px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div>
                        <div style="font-size:17px; font-weight:700; color:var(--text-primary);">{{ $product->name }}</div>
                        <div style="font-size:13px; color:var(--text-muted); margin-top:2px;">
                            Plan {{ $license->plan->period_label ?? '-' }}
                            @if($license->installed_version)
                                &middot; v{{ $license->installed_version }}
                            @endif
                        </div>
                    </div>
                </div>
                @if($isExpired)
                    <span class="badge badge-red">Expirada</span>
                @elseif(!$isValid)
                    <span class="badge badge-red">Inactiva</span>
                @elseif($days !== null && $days <= 7)
                    <span class="badge badge-yellow">⚠ {{ $days }} días</span>
                @elseif($days !== null && $days <= 30)
                    <span class="badge badge-yellow">{{ $days }} días restantes</span>
                @else
                    <span class="badge badge-green">● Activa</span>
                @endif
            </div>

            {{-- Stats: 2 columns on mobile, 4 on desktop --}}
            <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:12px; margin-bottom:20px;">
                <div style="background:var(--body-bg); border-radius:10px; padding:14px 16px;">
                    <div class="stat-label">Inicio</div>
                    <div style="font-size:14px; font-weight:600; color:var(--text-primary); margin-top:6px;">
                        {{ $license->starts_at->format('d/m/Y') }}
                    </div>
                </div>
                <div style="background:var(--body-bg); border-radius:10px; padding:14px 16px;">
                    <div class="stat-label">Próximo vencimiento</div>
                    <div style="font-size:14px; font-weight:600; margin-top:6px;
                         color:{{ $days !== null && $days <= 30 ? 'var(--warning)' : 'var(--text-primary)' }};">
                        {{ $license->expires_at?->format('d/m/Y') ?? '—' }}
                    </div>
                </div>
                <div style="background:var(--body-bg); border-radius:10px; padding:14px 16px;">
                    <div class="stat-label">Días restantes</div>
                    <div style="font-size:14px; font-weight:700; margin-top:6px;
                         color:{{ $days === null ? 'var(--success)' : ($days > 30 ? 'var(--success)' : ($days > 7 ? 'var(--warning)' : 'var(--danger)')) }};">
                        {{ $days !== null ? $days . ' días' : '∞' }}
                    </div>
                </div>
                <div style="background:var(--body-bg); border-radius:10px; padding:14px 16px;">
                    <div class="stat-label">Precio del plan</div>
                    <div style="font-size:14px; font-weight:600; color:var(--text-primary); margin-top:6px;">
                        ${{ number_format($license->plan->price, 0, ',', '.') }} ARS
                    </div>
                </div>
            </div>

            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <a href="{{ route('cliente.renovar', $license->id) }}" class="btn btn-primary" style="font-size:13px;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Renovar licencia
                </a>
                @if($isValid)
                    <form method="POST" action="{{ route('cliente.baja', $license->id) }}"
                          onsubmit="return confirm('¿Dar de baja esta licencia? El sistema seguirá funcionando hasta el vencimiento.')">
                        @csrf
                        <button type="submit" class="btn btn-secondary" style="font-size:13px; color:var(--danger);">
                            Dar de baja
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>

    {{-- ── 2. ACCESO AL SISTEMA ── --}}
    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">

            <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                <div style="width:36px; height:36px; border-radius:10px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:15px; font-weight:700; color:var(--text-primary);">Acceso al sistema</div>
                    <div style="font-size:13px; color:var(--text-muted); margin-top:1px;">URL y credenciales de ingreso</div>
                </div>
            </div>

            @if($domain)
                <div style="background:var(--body-bg); border-radius:10px; padding:14px 16px; margin-bottom:12px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                    <div>
                        <div class="stat-label">URL del sistema</div>
                        <div style="font-size:13px; font-family:var(--font-mono); color:var(--accent); margin-top:6px; word-break:break-all;">
                            http://{{ $domain->domain }}
                        </div>
                    </div>
                    <a href="http://{{ $domain->domain }}" target="_blank" class="btn btn-primary" style="font-size:13px; flex-shrink:0;">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Acceder
                    </a>
                </div>
            @endif

            @if($license->custom_domain)
                <div style="background:var(--success-bg); border:1px solid var(--success-border); border-radius:10px; padding:14px 16px; margin-bottom:12px; display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap;">
                    <div>
                        <div class="stat-label" style="color:var(--success);">Dominio personalizado activo</div>
                        <div style="font-size:13px; font-family:var(--font-mono); color:var(--success); margin-top:6px; word-break:break-all;">
                            https://{{ $license->custom_domain }}
                        </div>
                    </div>
                    <a href="https://{{ $license->custom_domain }}" target="_blank" class="btn btn-secondary" style="font-size:13px; flex-shrink:0;">
                        Abrir
                    </a>
                </div>
            @endif

            @if($accessUser)
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div style="background:var(--body-bg); border-radius:10px; padding:14px 16px;">
                        <div class="stat-label">Email de acceso</div>
                        <div style="font-size:13px; font-family:var(--font-mono); color:var(--text-primary); margin-top:6px; word-break:break-all;">
                            {{ $accessUser->email }}
                        </div>
                    </div>
                    <div style="background:var(--body-bg); border-radius:10px; padding:14px 16px;">
                        <div class="stat-label">Contraseña</div>
                        <div style="font-size:13px; color:var(--text-muted); margin-top:6px;">La que configuraste al contratar</div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">Podés resetearla desde el login del sistema</div>
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- ── 3. DOMINIO PERSONALIZADO ── --}}
    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">

            <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                <div style="width:36px; height:36px; border-radius:10px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:15px; font-weight:700; color:var(--text-primary);">Dominio personalizado</div>
                    <div style="font-size:13px; color:var(--text-muted); margin-top:1px;">Vinculá tu propio dominio al sistema</div>
                </div>
            </div>

            <p style="font-size:13px; color:var(--text-secondary); margin-bottom:16px; line-height:1.6;">
                Podés usar tu propio dominio (ej: <span style="font-family:var(--font-mono); font-size:12px; background:var(--body-bg); padding:2px 7px; border-radius:5px;">sistema.tuempresa.com</span>)
                en lugar del subdominio asignado. Necesitás crear un registro DNS de tipo <strong>CNAME</strong> en tu proveedor.
            </p>

            <form method="POST" action="{{ route('cliente.licencia.custom-domain', $license->id) }}">
                @csrf
                <div style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; margin-bottom:20px;">
                    <div style="flex:1; min-width:200px;">
                        <label style="display:block; font-size:12px; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.4px; margin-bottom:6px;">
                            Tu dominio propio
                        </label>
                        <input type="text" name="custom_domain"
                               value="{{ old('custom_domain', $license->custom_domain) }}"
                               placeholder="sistema.tuempresa.com"
                               style="width:100%; border:1.5px solid var(--card-border); border-radius:9px; padding:9px 14px; font-size:13px; font-family:var(--font-mono); color:var(--text-primary); background:var(--body-bg); outline:none; transition:border-color .2s;"
                               onfocus="this.style.borderColor='var(--accent)'" onblur="this.style.borderColor='var(--card-border)'">
                    </div>
                    <button type="submit" class="btn btn-primary" style="font-size:13px; white-space:nowrap;">
                        Guardar dominio
                    </button>
                    @if($license->custom_domain)
                        <button type="submit" name="custom_domain" value=""
                                onclick="return confirm('¿Eliminar el dominio personalizado?')"
                                class="btn btn-secondary" style="font-size:13px; color:var(--danger); white-space:nowrap;">
                            Eliminar
                        </button>
                    @endif
                </div>
            </form>

            @if($domain)
                <div style="background:var(--body-bg); border-radius:10px; padding:16px;">
                    <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; margin-bottom:12px;">
                        Configuración DNS requerida
                    </div>
                    <div style="overflow-x:auto;">
                        <table style="width:100%; border-collapse:collapse; font-size:13px;">
                            <thead>
                                <tr style="background:var(--card-bg);">
                                    <th style="padding:8px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--card-border); white-space:nowrap;">Tipo</th>
                                    <th style="padding:8px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--card-border); white-space:nowrap;">Nombre</th>
                                    <th style="padding:8px 12px; text-align:left; font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.5px; border-bottom:1px solid var(--card-border); white-space:nowrap;">CNAME apunta a</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:10px 12px; font-family:var(--font-mono); color:var(--accent); font-weight:600;">CNAME</td>
                                    <td style="padding:10px 12px; font-family:var(--font-mono); color:var(--text-primary);">{{ $license->custom_domain ? explode('.', $license->custom_domain)[0] : '@' }}</td>
                                    <td style="padding:10px 12px; font-family:var(--font-mono); color:var(--text-primary);">{{ $domain->domain }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p style="font-size:12px; color:var(--text-muted); margin-top:12px; line-height:1.5;">
                        Los cambios DNS pueden tardar hasta 48hs en propagarse. Una vez configurado, contactá a soporte para activar el dominio en el servidor.
                    </p>
                </div>
            @endif

        </div>
    </div>

    {{-- ── 4. ZONA DE PELIGRO ── --}}
    <div class="card" style="border-color:var(--danger-border); margin-bottom:20px;">
        <div class="card-body">

            <div style="display:flex; align-items:center; gap:10px; margin-bottom:20px;">
                <div style="width:36px; height:36px; border-radius:10px; background:var(--danger-bg); color:var(--danger); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:15px; font-weight:700; color:var(--danger);">Zona de peligro</div>
                    <div style="font-size:13px; color:var(--text-muted); margin-top:1px;">Acciones irreversibles sobre el sistema</div>
                </div>
            </div>

            <div style="background:var(--body-bg); border-radius:10px; padding:16px; display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                <div style="flex:1; min-width:200px;">
                    <div style="font-size:14px; font-weight:600; color:var(--text-primary); margin-bottom:4px;">Reset de fábrica</div>
                    <div style="font-size:13px; color:var(--text-muted); line-height:1.5;">
                        Borra <strong>todos los datos</strong> del sistema y lo deja en su estado inicial. Esta acción no se puede deshacer.
                    </div>
                </div>
                <button onclick="document.getElementById('reset-modal').style.display='flex'"
                        class="btn btn-danger" style="font-size:13px; white-space:nowrap; flex-shrink:0;">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Resetear sistema
                </button>
            </div>

        </div>
    </div>

    {{-- Modal confirmación reset --}}
    <div id="reset-modal"
         style="display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); z-index:1000; align-items:center; justify-content:center; padding:20px;">
        <div style="background:var(--card-bg); border-radius:16px; padding:28px; width:100%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,.2);">

            <div style="display:flex; align-items:center; gap:12px; margin-bottom:16px;">
                <div style="width:44px; height:44px; border-radius:50%; background:var(--danger-bg); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <svg width="22" height="22" fill="none" stroke="var(--danger)" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size:17px; font-weight:700; color:var(--text-primary);">¿Resetear el sistema?</div>
                    <div style="font-size:13px; color:var(--text-muted); margin-top:2px;">{{ $product->name }} &middot; {{ $user->tenant_id }}</div>
                </div>
            </div>

            <p style="font-size:13px; color:var(--text-secondary); margin-bottom:16px; line-height:1.6;">
                Esto borrará <strong>permanentemente</strong> todos los datos del sistema. Para confirmar, escribí
                <strong style="color:var(--danger); font-family:var(--font-mono);">RESETEAR</strong> en el campo de abajo.
            </p>

            <form method="POST" action="{{ route('cliente.licencia.reset', $license->id) }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <input type="text" name="confirm_reset"
                           placeholder="RESETEAR"
                           autocomplete="off"
                           style="width:100%; border:1.5px solid var(--danger-border); border-radius:8px; padding:10px 14px; font-size:14px; font-family:var(--font-mono); color:var(--danger); letter-spacing:.05em; background:var(--card-bg); outline:none;">
                </div>
                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button"
                            onclick="document.getElementById('reset-modal').style.display='none'"
                            class="btn btn-secondary">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        Resetear ahora
                    </button>
                </div>
            </form>

        </div>
    </div>

</x-cliente-layout>
