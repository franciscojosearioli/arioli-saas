<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'title' => 'user_management_access',
            ],
            [
                'title' => 'permission_create',
            ],
            [
                'title' => 'permission_edit',
            ],
            [
                'title' => 'permission_show',
            ],
            [
                'title' => 'permission_delete',
            ],
            [
                'title' => 'permission_access',
            ],
            [
                'title' => 'role_create',
            ],
            [
                'title' => 'role_edit',
            ],
            [
                'title' => 'role_show',
            ],
            [
                'title' => 'role_delete',
            ],
            [
                'title' => 'role_access',
            ],
            [
                'title' => 'user_create',
            ],
            [
                'title' => 'user_edit',
            ],
            [
                'title' => 'user_show',
            ],
            [
                'title' => 'user_delete',
            ],
            [
                'title' => 'user_access',
            ],
            [
                'title' => 'audit_log_show',
            ],
            [
                'title' => 'audit_log_access',
            ],
            [
                'title' => 'user_alert_create',
            ],
            [
                'title' => 'user_alert_show',
            ],
            [
                'title' => 'user_alert_delete',
            ],
            [
                'title' => 'user_alert_access',
            ],
            [
                'title' => 'profile_password_edit',
            ],
            [
                'title' => 'informe_management_access',
            ],
            [
                'title' => 'informe_access',
            ],
            [
                'title' => 'informe_create',
            ],
            [
                'title' => 'informe_show',
            ],
            [
                'title' => 'informe_edit',
            ],
            [
                'title' => 'informe_delete',
            ],
            [
                'title' => 'informe_psicologico_access',
            ],
            [
                'title' => 'informe_psicologico_create',
            ],
            [
                'title' => 'informe_psiquiatrico_access',
            ],
            [
                'title' => 'informe_psiquiatrico_create',
            ],
            [
                'title' => 'informe_clinico_access',
            ],
            [
                'title' => 'informe_clinico_create',
            ],
            [
                'title' => 'informe_operador_access',
            ],
            [
                'title' => 'informe_operador_create',
            ],
            [
                'title' => 'informe_judicial_access',
            ],
            [
                'title' => 'informe_judicial_create',
            ],
            [
                'title' => 'ficha_management_access',
            ],
            [
                'title' => 'paciente_access',
            ],
            [
                'title' => 'paciente_create',
            ],
            [
                'title' => 'paciente_show',
            ],
            [
                'title' => 'paciente_edit',
            ],
            [
                'title' => 'paciente_delete',
            ],
            [
                'title' => 'informe_tipo_access',
            ],
            [
                'title' => 'informe_tipo_create',
            ],
            [
                'title' => 'informe_tipo_show',
            ],
            [
                'title' => 'informe_tipo_edit',
            ],
            [
                'title' => 'informe_tipo_delete',
            ],
            [
                'title' => 'medicacion_management_access',
            ],
            [
                'title' => 'medicacion_access',
            ],
            [
                'title' => 'medicacion_create',
            ],
            [
                'title' => 'medicacion_show',
            ],
            [
                'title' => 'medicacion_edit',
            ],
            [
                'title' => 'medicacion_delete',
            ],
        ];

        Permission::insert($permissions);
    }
}