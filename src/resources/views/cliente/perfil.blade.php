<x-cliente-layout title="Mi Perfil">

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
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">Mi Perfil</h1>
            <p class="page-subtitle">Administrá tus datos personales y contraseña</p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; max-width:900px;">

        {{-- Datos personales --}}
        <div class="card">
            <div class="card-body">
                <div class="card-title">Datos personales</div>

                <form method="POST" action="{{ route('cliente.perfil.update') }}">
                    @csrf
                    @method('PATCH')

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label">Nombre completo</label>
                        <input type="text" name="name" class="form-input"
                               value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input"
                               value="{{ old('email', $user->email) }}" required>
                    </div>

                    <div style="display:flex; align-items:center; gap:12px; padding-top:4px; border-top:1px solid var(--card-border);">
                        <div style="width:40px; height:40px; border-radius:50%; background:var(--accent); color:#fff; display:flex; align-items:center; justify-content:center; font-size:16px; font-weight:700; flex-shrink:0;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $user->name }}</div>
                            <div style="font-size:12px; color:var(--text-muted);">{{ $user->email }}</div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; margin-top:20px; justify-content:center;">
                        Guardar cambios
                    </button>
                </form>
            </div>
        </div>

        {{-- Cambiar contraseña --}}
        <div class="card">
            <div class="card-body">
                <div class="card-title">Cambiar contraseña</div>

                <form method="POST" action="{{ route('cliente.perfil.update') }}">
                    @csrf
                    @method('PATCH')

                    {{-- Mantener nombre y email actuales --}}
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label">Contraseña actual</label>
                        <div class="password-wrap">
                            <input type="password" name="current_password" class="form-input"
                                placeholder="••••••••" id="current_password">
                            <button type="button" class="password-toggle" onclick="togglePassword('current_password', this)">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:16px;">
                        <label class="form-label">Nueva contraseña</label>
                        <div class="password-wrap">
                            <input type="password" name="password" class="form-input"
                                placeholder="Mínimo 8 caracteres" id="new_password">
                            <button type="button" class="password-toggle" onclick="togglePassword('new_password', this)">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:20px;">
                        <label class="form-label">Confirmar nueva contraseña</label>
                        <div class="password-wrap">
                            <input type="password" name="password_confirmation" class="form-input"
                                placeholder="Repetí la nueva contraseña" id="password_confirm">
                            <button type="button" class="password-toggle" onclick="togglePassword('password_confirm', this)">
                                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-secondary" style="width:100%; justify-content:center;">
                        Actualizar contraseña
                    </button>
                </form>
            </div>
        </div>

        {{-- Info de cuenta --}}
        <div class="card" style="grid-column:1 / -1;">
            <div class="card-body">
                <div class="card-title">Información de cuenta</div>
                <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:0;">
                    <div class="detail-row" style="grid-column:1; padding-right:24px;">
                        <span class="detail-label">Tenant ID</span>
                        <span class="detail-value" style="font-family:var(--font-mono); font-size:12px;">{{ $user->tenant_id }}</span>
                    </div>
                    <div class="detail-row" style="grid-column:1; padding-right:24px;">
                        <span class="detail-label">Email de acceso</span>
                        <span class="detail-value">{{ $user->email }}</span>
                    </div>
                    <div class="detail-row" style="grid-column:1; padding-right:24px; border-bottom:none;">
                        <span class="detail-label">Miembro desde</span>
                        <span class="detail-value">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

</x-cliente-layout>