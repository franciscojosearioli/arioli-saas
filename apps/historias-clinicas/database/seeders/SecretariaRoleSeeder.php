<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class SecretariaRoleSeeder extends Seeder
{
    // Permisos que la Secretaria tiene
    const PERMISOS = [
        // Agenda — gestión completa
        'agenda_access', 'agenda_create', 'agenda_show', 'agenda_edit', 'agenda_delete',
        // Pacientes — solo lectura
        'paciente_access', 'paciente_show', 'ficha_management_access',
        // Informes — solo lectura (puede VER todos los tipos, no crear)
        'informe_management_access', 'informe_access', 'informe_show',
        'informe_tipo_access', 'informe_tipo_show',
        'informe_psicologico_access', 'informe_psiquiatrico_access',
        'informe_clinico_access', 'informe_operador_access', 'informe_judicial_access',
        // Medicación — solo lectura
        'medicacion_management_access', 'medicacion_access', 'medicacion_show',
        // Notificaciones — puede enviar y ver
        'user_alert_access', 'user_alert_create', 'user_alert_show',
        // Perfil — cambiar contraseña propia
        'profile_password_edit',
    ];

    public function run(): void
    {
        // Crear el rol si no existe (búsqueda por título — el id lo asigna el auto-increment)
        $role = Role::firstOrCreate(['title' => 'Secretaria']);

        // Obtener los permisos que existen en esta DB
        $permissionIds = Permission::whereIn('title', self::PERMISOS)->pluck('id');

        $role->permissions()->sync($permissionIds);
    }
}
