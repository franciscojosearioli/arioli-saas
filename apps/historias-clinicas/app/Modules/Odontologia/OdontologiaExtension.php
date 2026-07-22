<?php

namespace App\Modules\Odontologia;

use App\Platform\Contracts\ComponenteExtension;
use Illuminate\Support\Facades\DB;

/**
 * Primera ComponenteExtension real. Deliberadamente mínima: no crea tablas
 * propias (piezas dentarias/tratamientos/odontograma quedan para cuando
 * Odontología se construya de verdad como funcionalidad — acá el objetivo
 * es probar el mecanismo, no resolver Odontología).
 *
 * Hace UNA cosa que un seed declarativo no cubre bien: provisiona permisos
 * nuevos en tiempo de instalación, no vía una migración que los fuerce en
 * todos los tenants. Los módulos "siempre activos" (Especialidades, Agenda,
 * Consentimientos, Medicación, Informes) tienen sentido con permisos vía
 * migración porque TODOS los tenants los usan hoy. Odontología es
 * genuinamente opcional — solo el tenant que la instala debería tener sus
 * permisos.
 */
class OdontologiaExtension implements ComponenteExtension
{
    public function version(): string
    {
        return '1.0.0';
    }

    public function instalar(): void
    {
        $permisos = ['odontologia_access', 'odontologia_create', 'odontologia_edit'];

        foreach ($permisos as $titulo) {
            $id = DB::table('permissions')->where('title', $titulo)->value('id');

            if (! $id) {
                $id = DB::table('permissions')->insertGetId([
                    'title' => $titulo,
                    'capability_key' => 'odontologia',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $yaAsignado = DB::table('permission_role')
                ->where('permission_id', $id)
                ->where('role_id', 1) // Admin
                ->exists();

            if (! $yaAsignado) {
                DB::table('permission_role')->insert([
                    'permission_id' => $id,
                    'role_id' => 1,
                ]);
            }
        }
    }
}
