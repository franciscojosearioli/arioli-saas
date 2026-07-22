<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Etapa 1 (ver docs/ARQUITECTURA_MODULAR.md): lleva la ficha de Paciente que
 * YA EXISTE hoy al mecanismo de field_visibility, sin agregar ninguna
 * funcionalidad nueva. Todas las secciones quedan visible=true,
 * origen=preset — cero cambio de comportamiento el día del deploy.
 */
class FieldVisibilitySeeder extends Seeder
{
    public function run(): void
    {
        $secciones = [
            'problematica',
            'datos_adicionales',
            'familia',
            'educacion',
            'laboral',
            'historial_tratamientos',
        ];

        foreach ($secciones as $campo) {
            DB::table('field_visibility')->updateOrInsert(
                ['entidad' => 'paciente', 'campo' => $campo],
                [
                    'tipo' => 'seccion',
                    'visible' => true,
                    'origen' => 'preset',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
