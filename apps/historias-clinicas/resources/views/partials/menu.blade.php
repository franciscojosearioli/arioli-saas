<<nav class="sidebar-nav">
    <div class="nav-section">Principal</div>

    <a href="{{ auth()->user()->is_admin ? route('admin.dashboard.home') : route('panel.home') }}"
        data-title="Dashboard"
       class="nav-link {{ request()->is('admin/dashboard') || request()->is('dashboard') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span>Dashboard</span>
    </a>

    <div class="nav-section">Atención Clínica</div>

    @can('paciente_access')
    <a href="{{ route('panel.paciente.index') }}"
       data-title="Pacientes Activos"
       class="nav-link {{ request()->is('paciente') || request()->is('paciente/*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        <span>Pacientes Activos</span>
    </a>

    <a href="{{ route('panel.paciente_inactivo.index') }}"
       data-title="HC Inactivas"
       class="nav-link {{ request()->is('paciente_inactivo') || request()->is('paciente_inactivo/*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M5 8l6 6m0 0l6-6m-6 6V3"/>
        </svg>
        <span>HC Inactivas</span>
    </a>
    @endcan

    @can('agenda_access')
    <a href="{{ route('panel.agenda.index') }}"
       data-title="Agenda de Citas"
       class="nav-link {{ request()->is('agenda') || request()->is('agenda/*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span>Agenda de Citas</span>
    </a>
    @endcan

    @can('informe_access')
    <a href="{{ route('panel.informe.index') }}"
       data-title="Informes Clínicos"
       class="nav-link {{ request()->is('informe') || request()->is('informe/*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>Informes Clínicos</span>
    </a>
    @endcan

    @can('medicacion_access')
    <a href="{{ route('panel.medicacion.index') }}"
       data-title="Prescripciones"
       class="nav-link {{ request()->is('medicacion') || request()->is('medicacion/*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
        </svg>
        <span>Prescripciones</span>
    </a>
    @endcan

    <div class="nav-section">Comunicación</div>

    <a href="{{ auth()->user()->is_admin ? route('admin.messenger.index') : route('panel.messenger.index') }}"
       data-title="Mensajería"
       class="nav-link {{ request()->is('messenger*') || request()->is('admin/messenger*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <span>Mensajería</span>
        @php($_unreadMenu = \App\Models\QaTopic::unreadCount())
        @if($_unreadMenu > 0)
            <span style="display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:18px; padding:0 6px; border-radius:999px; background:#e11d48; color:#fff; font-size:10px; font-weight:700; margin-left:auto;">
                {{ $_unreadMenu > 99 ? '99+' : $_unreadMenu }}
            </span>
        @endif
    </a>

    @can('user_management_access')
    <div class="nav-section">Administración</div>

    <a href="{{ route('admin.users.index') }}"
       data-title="Usuarios"
       class="nav-link {{ request()->is('admin/users*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
        </svg>
        <span>Usuarios</span>
    </a>

    @can('role_access')
    <a href="{{ route('admin.roles.index') }}"
       data-title="Roles"
       class="nav-link {{ request()->is('admin/roles*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2V6"/>
        </svg>
        <span>Roles</span>
    </a>
    @endcan

    @can('permission_access')
    <a href="{{ route('admin.permissions.index') }}"
       data-title="Permisos"
       class="nav-link {{ request()->is('admin/permissions*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
        </svg>
        <span>Permisos</span>
    </a>
    @endcan

    @can('user_alert_access')
    <a href="{{ route('admin.user-alerts.index') }}"
       data-title="Alertas"
       class="nav-link {{ request()->is('admin/user-alerts*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span>Alertas</span>
    </a>
    @endcan

    @can('audit_log_access')
    <a href="{{ route('admin.audit-logs.index') }}"
       data-title="Auditoría"
       class="nav-link {{ request()->is('admin/audit-logs*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <span>Auditoría</span>
    </a>
    @endcan
    @endcan

    <div class="nav-section">Configuración</div>

    @can('especialidad_access')
    <a href="{{ route('admin.especialidades.index') }}"
       data-title="Especialidades"
       class="nav-link {{ request()->is('admin/especialidades*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
        </svg>
        <span>Especialidades</span>
    </a>
    @endcan

    @can('informe_tipo_access')
    <a href="{{ route('admin.informes.tipos.index') }}"
       data-title="Tipos de Informe"
       class="nav-link {{ request()->is('admin/informes/tipos*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
        </svg>
        <span>Tipos de Informe</span>
    </a>
    @endcan

    @if(auth()->user()->is_admin)
    <a href="{{ route('admin.configuracion.edit') }}"
       data-title="Configuración"
       class="nav-link {{ request()->is('admin/configuracion*') ? 'active' : '' }}">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span>Configuración</span>
    </a>
    @endif

</nav>
