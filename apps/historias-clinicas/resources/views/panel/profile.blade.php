@extends('layouts.panel')
@section('title', trans('global.my_profile'))
@section('content')

@push('styles')
<style>
    .prf-wrap {
        display: flex; flex-direction: column; gap: 24px;
        max-width: 900px;
        animation: prfFadeUp .35s ease both;
    }
    @@keyframes prfFadeUp {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    /* ── header ── */
    .prf-header { display: flex; align-items: center; gap: 16px; }
    .prf-avatar {
        width: 56px; height: 56px; border-radius: 50%; flex-shrink: 0;
        background: var(--accent, #1d4ed8);
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; font-weight: 700; color: #fff;
    }
    .prf-header-text h1 {
        font-size: 20px; font-weight: 700;
        color: var(--text-primary, #0f172a);
        letter-spacing: -.02em; margin: 0;
    }
    .prf-header-text p { font-size: 13px; color: var(--text-secondary, #64748b); margin: 2px 0 0; }

    /* ── grid ── */
    .prf-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: start;
    }
    @@media (max-width: 700px) { .prf-grid { grid-template-columns: 1fr; } }
    .prf-full { grid-column: 1 / -1; }

    /* ── card ── */
    .prf-card {
        background: var(--card-bg, #fff);
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: var(--card-radius, 14px);
        box-shadow: var(--card-shadow); overflow: hidden;
    }
    .prf-card-head {
        display: flex; align-items: center; gap: 9px;
        padding: 14px 20px;
        border-bottom: 1px solid var(--card-border, #e8edf2);
        font-size: 13px; font-weight: 600; color: var(--text-primary, #0f172a);
    }
    .prf-card-head svg { width: 15px; height: 15px; color: var(--accent, #1d4ed8); flex-shrink: 0; }
    .prf-card-body { padding: 20px; display: flex; flex-direction: column; gap: 16px; }

    /* ── field ── */
    .prf-field { display: flex; flex-direction: column; gap: 5px; }
    .prf-label {
        font-size: 11px; font-weight: 600; text-transform: uppercase;
        letter-spacing: .06em; color: var(--text-muted, #94a3b8);
    }
    .prf-input {
        border: 1px solid var(--card-border, #e8edf2);
        border-radius: 8px; padding: 9px 12px;
        font-size: 13px; color: var(--text-primary, #0f172a);
        background: var(--body-bg, #f8fafc);
        font-family: var(--font-sans, inherit);
        width: 100%; outline: none; transition: border-color .15s, box-shadow .15s;
    }
    .prf-input:focus {
        border-color: var(--accent, #1d4ed8);
        box-shadow: 0 0 0 3px rgba(29,78,216,.08);
    }
    .prf-input.err { border-color: #e11d48; }
    html.dark .prf-input { background: #0f172a; color: #f1f5f9; border-color: #1e293b; }
    .prf-err { font-size: 11px; color: #e11d48; }

    /* ── 2FA status ── */
    .prf-2fa-status {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 14px; border-radius: 10px;
        font-size: 13px; font-weight: 500;
    }
    .prf-2fa-status.on  { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .prf-2fa-status.off { background: var(--body-bg, #f8fafc); color: var(--text-secondary, #64748b); border: 1px solid var(--card-border, #e8edf2); }
    .prf-2fa-status svg { width: 16px; height: 16px; flex-shrink: 0; }
    .prf-2fa-desc { font-size: 12px; color: var(--text-muted, #94a3b8); line-height: 1.5; }

    /* ── buttons ── */
    .prf-btn {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 9px;
        font-size: 13px; font-weight: 600; border: none; cursor: pointer;
        font-family: var(--font-sans, inherit);
        transition: background .15s, transform .15s;
    }
    .prf-btn:hover { transform: translateY(-1px); }
    .prf-btn svg { width: 14px; height: 14px; }
    .prf-btn.primary {
        background: var(--accent, #1d4ed8); color: #fff;
        box-shadow: 0 2px 8px rgba(29,78,216,.22);
    }
    .prf-btn.primary:hover { background: var(--accent-hover, #1e40af); }
    .prf-btn.success {
        background: #16a34a; color: #fff;
        box-shadow: 0 2px 8px rgba(22,163,74,.22);
    }
    .prf-btn.success:hover { background: #15803d; }
    .prf-btn.warning {
        background: #f59e0b; color: #fff;
        box-shadow: 0 2px 8px rgba(245,158,11,.22);
    }
    .prf-btn.warning:hover { background: #d97706; }
    .prf-btn.danger-ghost {
        background: #fff1f2; color: #e11d48;
        border: 1px solid #fecaca;
    }
    .prf-btn.danger-ghost:hover { background: #ffe4e6; }
</style>
@endpush

<div class="prf-wrap">

    {{-- ── Header ── --}}
    <div class="prf-header">
        <div class="prf-avatar">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="prf-header-text">
            <h1>{{ auth()->user()->name }}</h1>
            <p>{{ auth()->user()->email }}</p>
        </div>
    </div>

    <div class="prf-grid">

        {{-- ── Datos personales ── --}}
        <div class="prf-card">
            <div class="prf-card-head">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ trans('global.my_profile') }}
            </div>
            <div class="prf-card-body">
                <form method="POST" action="{{ route('panel.profile.update') }}">
                    @csrf

                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div class="prf-field">
                            <label class="prf-label" for="name">{{ trans('cruds.user.fields.name') }}</label>
                            <input type="text" name="name" id="name"
                                   class="prf-input {{ $errors->has('name') ? 'err' : '' }}"
                                   value="{{ old('name', auth()->user()->name) }}" required>
                            @if($errors->has('name'))
                                <span class="prf-err">{{ $errors->first('name') }}</span>
                            @endif
                        </div>

                        <div class="prf-field">
                            <label class="prf-label" for="email">{{ trans('cruds.user.fields.email') }}</label>
                            <input type="email" name="email" id="email"
                                   class="prf-input {{ $errors->has('email') ? 'err' : '' }}"
                                   value="{{ old('email', auth()->user()->email) }}" required>
                            @if($errors->has('email'))
                                <span class="prf-err">{{ $errors->first('email') }}</span>
                            @endif
                        </div>

                        <div>
                            <button type="submit" class="prf-btn primary">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ trans('global.save') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Cambiar contraseña ── --}}
        <div class="prf-card">
            <div class="prf-card-head">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                </svg>
                {{ trans('global.change_password') }}
            </div>
            <div class="prf-card-body">
                <form method="POST" action="{{ route('panel.profile.password') }}">
                    @csrf

                    <div style="display:flex;flex-direction:column;gap:16px;">
                        <div class="prf-field">
                            <label class="prf-label" for="password">
                                Nueva {{ trans('cruds.user.fields.password') }}
                            </label>
                            <input type="password" name="password" id="password"
                                   class="prf-input {{ $errors->has('password') ? 'err' : '' }}"
                                   required>
                            @if($errors->has('password'))
                                <span class="prf-err">{{ $errors->first('password') }}</span>
                            @endif
                        </div>

                        <div class="prf-field">
                            <label class="prf-label" for="password_confirmation">
                                Repetir nueva {{ trans('cruds.user.fields.password') }}
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="prf-input" required>
                        </div>

                        <div>
                            <button type="submit" class="prf-btn success">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Cambiar contraseña
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── Verificación en dos pasos ── --}}
        @if(Route::has('panel.profile.toggle-two-factor'))
        <div class="prf-card prf-full">
            <div class="prf-card-head">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                {{ trans('global.two_factor.title') }}
            </div>
            <div class="prf-card-body">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;">
                    <div style="display:flex;flex-direction:column;gap:8px;flex:1;min-width:0;">
                        @if(auth()->user()->two_factor)
                            <div class="prf-2fa-status on">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Verificación en dos pasos activa
                            </div>
                        @else
                            <div class="prf-2fa-status off">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                </svg>
                                Verificación en dos pasos desactivada
                            </div>
                        @endif
                        <p class="prf-2fa-desc">
                            Al habilitarla, se enviará un código a su correo electrónico cada vez que inicie sesión.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('panel.profile.toggle-two-factor') }}">
                        @csrf
                        <button type="submit" class="prf-btn {{ auth()->user()->two_factor ? 'danger-ghost' : 'warning' }}">
                            @if(auth()->user()->two_factor)
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                {{ trans('global.two_factor.disable') }}
                            @else
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                {{ trans('global.two_factor.enable') }}
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- ── Firma Digital ── --}}
        <div class="prf-card prf-full">
            <div class="prf-card-head">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                </svg>
                Firma Digital
            </div>
            <div class="prf-card-body">

                {{-- Preview firma actual --}}
                @if(auth()->user()->firma_nombre)
                <div style="display:flex;align-items:flex-start;gap:20px;padding:14px;background:var(--body-bg);border-radius:10px;border:1px solid var(--card-border,#e8edf2);flex-wrap:wrap;">
                    <div style="flex-shrink:0;">
                        @if(auth()->user()->firma_imagen)
                        <img src="{{ asset('storage/' . auth()->user()->firma_imagen) }}"
                             style="max-height:70px;max-width:200px;border:1px solid var(--card-border);border-radius:6px;background:#fff;padding:4px;">
                        @else
                        <div style="width:160px;height:60px;border:1px dashed #ccc;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--text-muted);">Sin imagen</div>
                        @endif
                    </div>
                    <div style="flex:1;min-width:160px;">
                        <div style="font-size:13px;font-weight:700;color:var(--text-primary);">{{ auth()->user()->firma_nombre }}</div>
                        @if(auth()->user()->firma_dni)
                        <div style="font-size:12px;color:var(--text-secondary);">DNI: {{ auth()->user()->firma_dni }}</div>
                        @endif
                        @if(auth()->user()->firma_matricula)
                        <div style="font-size:12px;color:var(--text-secondary);">M.P. {{ auth()->user()->firma_matricula }}</div>
                        @endif
                        @if(auth()->user()->firma_especialidad_texto)
                        <div style="font-size:12px;color:var(--text-muted);">{{ auth()->user()->firma_especialidad_texto }}</div>
                        @endif
                        <div style="margin-top:8px;">
                            <form method="POST" action="{{ route('panel.profile.firma.delete') }}" onsubmit="return confirm('¿Eliminar la firma digital?');">
                                @csrf
                                <button type="submit" class="prf-btn danger-ghost" style="font-size:12px;padding:6px 14px;">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:13px;height:13px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Eliminar firma
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                <form method="POST" action="{{ route('panel.profile.firma') }}" enctype="multipart/form-data" id="firma-form">
                    @csrf

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                        <div class="prf-field">
                            <label class="prf-label">Nombre y apellido <span style="color:#dc2626;">*</span></label>
                            <input type="text" name="firma_nombre" class="prf-input {{ $errors->has('firma_nombre') ? 'err' : '' }}"
                                   value="{{ old('firma_nombre', auth()->user()->firma_nombre) }}"
                                   placeholder="Dr. Juan Pérez" required>
                            @if($errors->has('firma_nombre'))<span class="prf-err">{{ $errors->first('firma_nombre') }}</span>@endif
                        </div>
                        <div class="prf-field">
                            <label class="prf-label">DNI</label>
                            <input type="text" name="firma_dni" class="prf-input"
                                   value="{{ old('firma_dni', auth()->user()->firma_dni) }}"
                                   placeholder="25.123.456">
                        </div>
                        <div class="prf-field">
                            <label class="prf-label">Matrícula profesional</label>
                            <input type="text" name="firma_matricula" class="prf-input"
                                   value="{{ old('firma_matricula', auth()->user()->firma_matricula) }}"
                                   placeholder="12345">
                        </div>
                        <div class="prf-field">
                            <label class="prf-label">Especialidad</label>
                            <input type="text" name="firma_especialidad_texto" class="prf-input"
                                   value="{{ old('firma_especialidad_texto', auth()->user()->firma_especialidad_texto) }}"
                                   placeholder="Psicología clínica">
                        </div>
                    </div>

                    {{-- Imagen de firma --}}
                    <div style="margin-top:16px;">
                        <div class="prf-label" style="margin-bottom:8px;">Firma hológrafa</div>

                        {{-- Tabs --}}
                        <div style="display:flex;gap:8px;margin-bottom:12px;" id="firma-tabs">
                            <button type="button" class="firma-tab-btn active" data-tab="dibujar"
                                    style="padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;border:1.5px solid var(--accent,#1d4ed8);background:var(--accent,#1d4ed8);color:#fff;cursor:pointer;transition:all .15s;">
                                ✏️ Dibujar firma
                            </button>
                            <button type="button" class="firma-tab-btn" data-tab="subir"
                                    style="padding:7px 14px;border-radius:8px;font-size:12px;font-weight:600;border:1.5px solid var(--card-border);background:var(--card-bg);color:var(--text-secondary);cursor:pointer;transition:all .15s;">
                                📎 Subir imagen
                            </button>
                        </div>

                        {{-- Panel dibujar --}}
                        <div id="tab-dibujar">
                            <div style="border:1.5px solid var(--card-border,#e8edf2);border-radius:10px;overflow:hidden;background:#fff;display:inline-block;">
                                <canvas id="firma-canvas" width="500" height="120"
                                        style="display:block;cursor:crosshair;touch-action:none;"></canvas>
                            </div>
                            <div style="display:flex;gap:8px;margin-top:8px;flex-wrap:wrap;">
                                <button type="button" onclick="clearCanvas()"
                                        style="padding:6px 14px;border-radius:7px;border:1px solid var(--card-border);background:var(--body-bg);font-size:12px;font-weight:600;cursor:pointer;color:var(--text-secondary);">
                                    Limpiar
                                </button>
                                <span style="font-size:11px;color:var(--text-muted);align-self:center;">
                                    Dibujá tu firma con el mouse o el dedo (táctil).
                                </span>
                            </div>
                            <input type="hidden" name="firma_canvas_data" id="firma-canvas-data">
                        </div>

                        {{-- Panel subir --}}
                        <div id="tab-subir" style="display:none;">
                            <input type="file" name="firma_imagen_file" id="firma-file-input"
                                   accept="image/png,image/jpeg,image/webp"
                                   style="display:none;">
                            <div onclick="document.getElementById('firma-file-input').click()"
                                 style="border:2px dashed var(--card-border,#e8edf2);border-radius:10px;padding:24px;text-align:center;cursor:pointer;background:var(--body-bg);transition:border-color .15s;"
                                 onmouseover="this.style.borderColor='var(--accent,#1d4ed8)'"
                                 onmouseout="this.style.borderColor=''">
                                <div style="font-size:13px;color:var(--text-secondary);">
                                    Hacé clic para seleccionar una imagen (PNG, JPG, WEBP)
                                </div>
                                <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">Preferentemente PNG con fondo transparente. Máx 3 MB.</div>
                            </div>
                            <div id="firma-file-preview" style="margin-top:8px;display:none;">
                                <img id="firma-file-img" style="max-height:80px;border-radius:6px;border:1px solid var(--card-border);">
                                <div id="firma-file-name" style="font-size:11px;color:var(--text-muted);margin-top:4px;"></div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top:20px;">
                        <button type="submit" class="prf-btn primary" onclick="prepareCanvasSubmit()">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar firma digital
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
/* ── Canvas signature ── */
const canvas  = document.getElementById('firma-canvas');
const ctx     = canvas.getContext('2d');
let drawing   = false;
let hasDrawn  = false;

ctx.strokeStyle = '#1a3561';
ctx.lineWidth   = 2;
ctx.lineCap     = 'round';
ctx.lineJoin    = 'round';

function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    const src  = e.touches ? e.touches[0] : e;
    return { x: src.clientX - rect.left, y: src.clientY - rect.top };
}

canvas.addEventListener('mousedown', (e) => { drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); });
canvas.addEventListener('mousemove', (e) => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); hasDrawn = true; });
canvas.addEventListener('mouseup', () => drawing = false);
canvas.addEventListener('mouseleave', () => drawing = false);
canvas.addEventListener('touchstart', (e) => { e.preventDefault(); drawing = true; const p = getPos(e); ctx.beginPath(); ctx.moveTo(p.x, p.y); }, { passive: false });
canvas.addEventListener('touchmove', (e) => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); hasDrawn = true; }, { passive: false });
canvas.addEventListener('touchend', () => drawing = false);

function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    hasDrawn = false;
    document.getElementById('firma-canvas-data').value = '';
}

function prepareCanvasSubmit() {
    const activeTab = document.querySelector('.firma-tab-btn.active')?.dataset.tab;
    if (activeTab === 'dibujar' && hasDrawn) {
        document.getElementById('firma-canvas-data').value = canvas.toDataURL('image/png');
    }
}

/* ── Tabs ── */
document.querySelectorAll('.firma-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.firma-tab-btn').forEach(b => {
            b.style.background = 'var(--card-bg)';
            b.style.color      = 'var(--text-secondary)';
            b.style.borderColor= 'var(--card-border)';
            b.classList.remove('active');
        });
        btn.style.background  = 'var(--accent,#1d4ed8)';
        btn.style.color       = '#fff';
        btn.style.borderColor = 'var(--accent,#1d4ed8)';
        btn.classList.add('active');

        document.getElementById('tab-dibujar').style.display = btn.dataset.tab === 'dibujar' ? 'block' : 'none';
        document.getElementById('tab-subir').style.display   = btn.dataset.tab === 'subir'   ? 'block' : 'none';
    });
});

/* ── File preview ── */
document.getElementById('firma-file-input').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('firma-file-img').src = e.target.result;
        document.getElementById('firma-file-name').textContent = file.name;
        document.getElementById('firma-file-preview').style.display = 'block';
    };
    reader.readAsDataURL(file);
});
</script>
@endpush

@endsection
