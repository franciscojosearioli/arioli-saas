<?php

namespace App\Modules\MedicinaLaboral;

use App\Platform\Contracts\ComponenteExtension;
use Illuminate\Support\Facades\DB;

/**
 * Segunda ComponenteExtension real (Etapa 5) — mismo patrón exacto que
 * OdontologiaExtension: provisiona permisos en tiempo de instalación, no
 * vía migración. Se repite deliberadamente igual, para poder observar si
 * la repetición en sí misma es la señal de generalizar (todavía no se
 * generaliza en esta pasada).
 */
class MedicinaLaboralExtension implements ComponenteExtension
{
    public function version(): string
    {
        return '1.0.0';
    }

    public function instalar(): void
    {
        $permisos = ['medicina_laboral_access', 'medicina_laboral_create', 'medicina_laboral_edit'];

        foreach ($permisos as $titulo) {
            $id = DB::table('permissions')->where('title', $titulo)->value('id');

            if (! $id) {
                $id = DB::table('permissions')->insertGetId([
                    'title' => $titulo,
                    'capability_key' => 'medicina_laboral',
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
