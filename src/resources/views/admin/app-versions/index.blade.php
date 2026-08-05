<x-admin-layout title="Versiones de Apps">

    @if(session('success'))
        <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:13.5px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:28px; flex-wrap:wrap; gap:12px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:52px; height:52px; border-radius:14px; background:var(--accent-light); color:var(--accent); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-5 5a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 10V5a2 2 0 012-2z"/>
                </svg>
            </div>
            <div>
                <h1 style="font-size:22px; font-weight:700; color:var(--text-primary); margin:0;">Versiones de Apps</h1>
                <p style="font-size:13px; color:var(--text-muted); margin:3px 0 0;">Publicá nuevas versiones y gestioná actualizaciones por cliente</p>
            </div>
        </div>
        <button onclick="document.getElementById('modal-publish').style.display='flex'"
                style="display:inline-flex; align-items:center; gap:8px; background:var(--accent); color:#fff; border:none; padding:10px 18px; border-radius:10px; font-size:13.5px; font-weight:600; cursor:pointer;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Publicar versión
        </button>
    </div>

    {{-- Explicación del sistema --}}
    <div style="background:#f8fafc; border:1px solid var(--card-border); border-radius:12px; padding:16px 20px; margin-bottom:24px; font-size:13px; color:var(--text-secondary); line-height:1.6;">
        <strong style="color:var(--text-primary);">¿Cómo funciona el ciclo de versiones?</strong><br>
        Cuando modificás código en <code style="background:#e2e8f0; padding:1px 6px; border-radius:4px; font-size:12px;">apps/loteos/</code>,
        <code style="background:#e2e8f0; padding:1px 6px; border-radius:4px; font-size:12px;">apps/historias-clinicas/</code> o
        <code style="background:#e2e8f0; padding:1px 6px; border-radius:4px; font-size:12px;">apps/tallerpro/</code>,
        publicás una nueva versión acá. Los clientes <strong>estándar</strong> reciben la actualización automáticamente (migraciones de BD y limpieza de caché).
        Los clientes marcados como <strong style="color:#d97706;">Personalizado</strong> quedan fuera del ciclo y se actualizan manualmente.
    </div>

    {{-- Tabla por producto --}}
    @foreach ($products as $product)
        @php $current = $product->appVersions->firstWhere('is_current', true); @endphp
        <div class="card" style="margin-bottom:24px;">

            {{-- Header del producto --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; flex-wrap:wrap; gap:10px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:16px; font-weight:700; color:var(--text-primary);">{{ $product->name }}</span>
                    <span style="font-size:11px; font-family:var(--font-mono); color:var(--text-muted); background:#f1f5f9; padding:2px 8px; border-radius:4px;">{{ $product->slug }}</span>
                    @if($current)
                        <span style="font-size:11px; background:var(--accent-light); color:var(--accent); padding:3px 10px; border-radius:20px; font-weight:600;">
                            v{{ $current->version }} actual
                        </span>
                    @endif
                </div>

                {{-- Estado de actualizaciones --}}
                <div style="display:flex; align-items:center; gap:10px;">
                    @if(($pendingByProduct[$product->id] ?? 0) > 0)
                        <span style="font-size:12px; background:#fef2f2; color:#dc2626; padding:4px 12px; border-radius:20px; font-weight:600;">
                            {{ $pendingByProduct[$product->id] }} pendiente(s)
                        </span>
                        <form method="POST" action="{{ route('app-versions.push') }}" style="margin:0;">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit"
                                    onclick="return confirm('¿Enviar actualización v{{ $current?->version }} a {{ $pendingByProduct[$product->id] }} cliente(s) estándar?')"
                                    style="background:#dc2626; color:#fff; border:none; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; cursor:pointer;">
                                Enviar actualización
                            </button>
                        </form>
                    @else
                        <span style="font-size:12px; background:#f0fdf4; color:#16a34a; padding:4px 12px; border-radius:20px; font-weight:500;">
                            ✓ Todos actualizados
                        </span>
                    @endif

                    @if(($customByProduct[$product->id] ?? 0) > 0)
                        <span style="font-size:12px; background:#fffbeb; color:#d97706; padding:4px 12px; border-radius:20px; font-weight:500;">
                            {{ $customByProduct[$product->id] }} personalizado(s) — manual
                        </span>
                    @endif
                </div>
            </div>

            @if($product->appVersions->isEmpty())
                <p style="font-size:13px; color:var(--text-muted); text-align:center; padding:24px 0;">
                    Sin versiones publicadas. Usá el botón "Publicar versión" para registrar la primera.
                </p>
            @else
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid var(--card-border);">
                            <th style="text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:var(--text-muted); padding:0 0 10px;">Versión</th>
                            <th style="text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:var(--text-muted); padding:0 0 10px;">Tipo</th>
                            <th style="text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:var(--text-muted); padding:0 0 10px;">Lanzada</th>
                            <th style="text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:var(--text-muted); padding:0 0 10px;">Changelog</th>
                            <th style="text-align:right; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:var(--text-muted); padding:0 0 10px;">Clientes</th>
                            <th style="text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:var(--text-muted); padding:0 0 10px;">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($product->appVersions as $version)
                        <tr style="border-bottom:1px solid #f8fafc;">
                            <td style="padding:12px 0; font-family:var(--font-mono); font-size:14px; font-weight:600; color:var(--text-primary);">
                                v{{ $version->version }}
                            </td>
                            <td style="padding:12px 0;">
                                @if ($version->type === 'stable')
                                    <span style="font-size:11px; background:#f0fdf4; color:#166534; padding:2px 8px; border-radius:20px; font-weight:500;">stable</span>
                                @elseif ($version->type === 'beta')
                                    <span style="font-size:11px; background:#fffbeb; color:#92400e; padding:2px 8px; border-radius:20px; font-weight:500;">beta</span>
                                @else
                                    <span style="font-size:11px; background:#f5f3ff; color:#5b21b6; padding:2px 8px; border-radius:20px; font-weight:500;">alpha</span>
                                @endif
                            </td>
                            <td style="padding:12px 0; font-size:13px; color:var(--text-secondary);">
                                {{ $version->released_at?->format('d/m/Y') }}
                            </td>
                            <td style="padding:12px 16px 12px 0; font-size:12px; color:var(--text-secondary); max-width:300px;">
                                @if(is_array($version->changelog) && count($version->changelog))
                                    <ul style="margin:0; padding-left:14px; list-style:disc;">
                                        @foreach(array_slice($version->changelog, 0, 3) as $item)
                                            <li style="margin-bottom:2px;">{{ $item }}</li>
                                        @endforeach
                                        @if(count($version->changelog) > 3)
                                            <li style="color:var(--text-muted);">+{{ count($version->changelog) - 3 }} más...</li>
                                        @endif
                                    </ul>
                                @else
                                    <span style="color:var(--text-muted);">—</span>
                                @endif
                            </td>
                            <td style="padding:12px 0; font-size:13px; color:var(--text-secondary); text-align:right; padding-right:16px;">
                                {{ $installedCounts[$version->version] ?? 0 }}
                            </td>
                            <td style="padding:12px 0;">
                                @if ($version->is_current)
                                    <span style="font-size:11px; background:var(--accent-light); color:var(--accent); padding:2px 10px; border-radius:20px; font-weight:600;">● ACTUAL</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach

    @if ($products->every(fn($p) => $p->appVersions->isEmpty()))
        <div class="card" style="text-align:center; padding:60px 24px; color:var(--text-muted);">
            <p style="font-size:15px; margin-bottom:8px;">No hay versiones registradas aún.</p>
            <p style="font-size:13px;">Usá el botón "Publicar versión" para registrar la primera.</p>
        </div>
    @endif

    {{-- Modal: Publicar versión --}}
    <div id="modal-publish" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:32px; width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,.2); max-height:90vh; overflow-y:auto;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                <h2 style="font-size:18px; font-weight:700; color:var(--text-primary); margin:0;">Publicar nueva versión</h2>
                <button onclick="document.getElementById('modal-publish').style.display='none'"
                        style="background:none; border:none; cursor:pointer; color:var(--text-muted); font-size:20px; line-height:1;">✕</button>
            </div>

            @if($errors->any())
                <div style="background:#fef2f2; border:1px solid #fecaca; color:#dc2626; padding:10px 14px; border-radius:8px; margin-bottom:16px; font-size:13px;">
                    <ul style="margin:0; padding-left:16px;">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('app-versions.store') }}">
                @csrf
                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Producto</label>
                    <select name="product_id" required
                            style="width:100%; border:1px solid var(--card-border); border-radius:8px; padding:9px 12px; font-size:13.5px; background:#fff; color:var(--text-primary);">
                        <option value="">Seleccioná un producto</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:16px;">
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Número de versión</label>
                        <input type="text" name="version" value="{{ old('version') }}" placeholder="1.2.0" required
                               style="width:100%; border:1px solid var(--card-border); border-radius:8px; padding:9px 12px; font-size:13.5px; font-family:var(--font-mono);">
                        <p style="font-size:11px; color:var(--text-muted); margin:4px 0 0;">Formato: mayor.menor.parche</p>
                    </div>
                    <div>
                        <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">Tipo</label>
                        <select name="type" required
                                style="width:100%; border:1px solid var(--card-border); border-radius:8px; padding:9px 12px; font-size:13.5px; background:#fff;">
                            <option value="stable" {{ old('type') === 'stable' ? 'selected' : '' }}>Stable</option>
                            <option value="beta"   {{ old('type') === 'beta'   ? 'selected' : '' }}>Beta</option>
                            <option value="alpha"  {{ old('type') === 'alpha'  ? 'selected' : '' }}>Alpha</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">
                        Changelog <span style="font-weight:400; color:var(--text-muted);">(un cambio por línea)</span>
                    </label>
                    <textarea name="changelog" rows="5" required placeholder="Nueva funcionalidad de reportes&#10;Corrección de bug en login&#10;Mejora de rendimiento en listados"
                              style="width:100%; border:1px solid var(--card-border); border-radius:8px; padding:9px 12px; font-size:13px; resize:vertical;">{{ old('changelog') }}</textarea>
                </div>

                <div style="margin-bottom:24px;">
                    <label style="display:block; font-size:13px; font-weight:600; color:var(--text-primary); margin-bottom:6px;">PHP mínimo requerido <span style="font-weight:400; color:var(--text-muted);">(opcional)</span></label>
                    <input type="text" name="min_php_version" value="{{ old('min_php_version', '8.2') }}" placeholder="8.2"
                           style="width:120px; border:1px solid var(--card-border); border-radius:8px; padding:9px 12px; font-size:13.5px; font-family:var(--font-mono);">
                </div>

                <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:8px; padding:12px 14px; margin-bottom:20px; font-size:12.5px; color:#92400e;">
                    <strong>Importante:</strong> Al publicar, esta versión se marca como "actual" para el producto.
                    Los clientes estándar aparecerán como "pendientes" hasta que uses "Enviar actualización".
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <button type="button" onclick="document.getElementById('modal-publish').style.display='none'"
                            style="background:#f1f5f9; border:none; color:var(--text-secondary); padding:10px 20px; border-radius:8px; font-size:13.5px; cursor:pointer;">
                        Cancelar
                    </button>
                    <button type="submit"
                            style="background:var(--accent); color:#fff; border:none; padding:10px 20px; border-radius:8px; font-size:13.5px; font-weight:600; cursor:pointer;">
                        Publicar versión
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($errors->any())
        <script>document.getElementById('modal-publish').style.display = 'flex';</script>
    @endif

</x-admin-layout>
