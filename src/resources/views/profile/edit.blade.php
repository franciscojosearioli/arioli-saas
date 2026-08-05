<x-admin-layout title="Mi Perfil">

    <div class="page-header">
        <div>
            <h1 class="page-title">Mi Perfil</h1>
            <p class="page-subtitle">Administrá tus datos personales y contraseña</p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; max-width:1000px;">

        {{-- Datos personales --}}
        <div class="card">
            <div style="padding:24px;">
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:20px;">
                    Datos personales
                </div>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        {{-- Contraseña --}}
        <div class="card">
            <div style="padding:24px;">
                <div style="font-size:12px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin-bottom:20px;">
                    Cambiar contraseña
                </div>
                @include('profile.partials.update-password-form')
            </div>
        </div>

    </div>

</x-admin-layout>