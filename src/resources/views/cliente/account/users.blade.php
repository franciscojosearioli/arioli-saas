<x-cliente-layout title="Usuarios">

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="page-header">
        <div>
            <h1 class="page-title">Usuarios</h1>
            <p class="page-subtitle">Quiénes tienen acceso al portal de tu cuenta ({{ $portalUsers->count() }}/{{ $maxUsers }})</p>
        </div>
    </div>

    <div class="card" style="margin-bottom:20px;">
        <div class="card-body">
            @foreach($portalUsers as $portalUser)
                <div class="detail-row">
                    <span class="detail-label">
                        {{ $portalUser->name }}
                        @if($portalUser->id === $currentUserId)
                            <span class="badge badge-blue" style="margin-left:6px;">Vos</span>
                        @endif
                    </span>
                    <span style="display:flex; align-items:center; gap:14px;">
                        <span style="color:var(--text-muted); font-size:13.5px;">{{ $portalUser->email }}</span>
                        @if($portalUser->id !== $currentUserId)
                            <form method="POST" action="{{ route('cliente.account.users.destroy', $portalUser) }}" onsubmit="return confirm('¿Quitarle el acceso al portal a {{ $portalUser->name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn action-delete">Eliminar</button>
                            </form>
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title" style="margin-bottom:16px;">Agregar usuario</div>
            @if($canAddMore)
                <form method="POST" action="{{ route('cliente.account.users.store') }}" style="display:grid; grid-template-columns:1fr 1fr auto; gap:12px; align-items:end;">
                    @csrf
                    <div>
                        <label class="form-label">Nombre</label>
                        <input type="text" name="name" class="form-input" value="{{ old('name') }}" required>
                        @error('name')<div class="error" style="margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" value="{{ old('email') }}" required>
                        @error('email')<div class="error" style="margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary" style="padding:9px 18px;">Invitar</button>
                </form>
                <p style="font-size:12px; color:var(--text-muted); margin-top:10px;">Le vamos a mandar un email para que defina su propia contraseña.</p>
            @else
                <p style="font-size:13px; color:var(--text-secondary);">Llegaste al máximo de {{ $maxUsers }} usuarios para esta cuenta — escribinos si necesitás más.</p>
            @endif
        </div>
    </div>

</x-cliente-layout>
