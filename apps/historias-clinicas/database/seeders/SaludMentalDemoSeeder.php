<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\InformeTipo;
use App\Models\Paciente;
use App\Models\PacienteFichaAdmision;
use App\Models\PacienteHistorialTratamientos;
use App\Models\PacienteProblematica;
use Illuminate\Database\Seeder;

/**
 * Escenario Demo — Etapa 6.2. A diferencia de Odontología/Medicina
 * Laboral, Salud Mental no tiene entidades de dominio propias todavía
 * (sesiones, escalas clínicas) — este seeder usa exclusivamente las
 * sub-fichas de Paciente que YA EXISTEN en el Core (mismas que
 * field_visibility hace opcionales desde Etapa 1/2/3). No se inventó
 * nada nuevo para este escenario.
 */
class SaludMentalDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Especialidad y tipos de informe del perfil — sin esto el centro de
        // salud mental nace con esos catálogos vacíos (ver docs/ARQUITECTURA_MODULAR.md).
        foreach ([
            ['Psicología Clínica',       'Evaluación y tratamiento de trastornos psicológicos'],
            ['Psiquiatría',              'Diagnóstico y tratamiento médico de trastornos mentales'],
            ['Salud Mental Comunitaria', 'Atención integral en contexto comunitario y familiar'],
            ['Trabajo Social',           'Intervención social, acompañamiento y orientación familiar'],
        ] as [$nombre, $desc]) {
            Especialidad::firstOrCreate(['nombre' => $nombre], ['descripcion' => $desc]);
        }

        foreach (['Informe Psicológico', 'Informe Psiquiátrico', 'Informe de Operador Terapéutico', 'Informe Clínico General'] as $tipo) {
            InformeTipo::firstOrCreate(['name' => $tipo]);
        }

        $laura = Paciente::firstOrCreate(
            ['dni' => '31555999'],
            $this->datosBase(['nombre' => 'Laura', 'apellido' => 'Fernández', 'sexo' => 'Femenino', 'estado_civil' => 'Soltero', 'fecha_nac' => '1990-10-05', 'edad' => 35])
        );

        PacienteFichaAdmision::updateOrCreate(
            ['paciente_id' => $laura->id],
            ['fecha_ingreso' => now()->subMonths(4), 'modalidad' => 'Ambulatorio']
        );

        PacienteProblematica::updateOrCreate(
            ['paciente_id' => $laura->id],
            ['problematica' => 'Consumo problemático', 'problematica_detalles' => 'Consumo de alcohol, en tratamiento ambulatorio desde hace 4 meses.']
        );

        PacienteHistorialTratamientos::updateOrCreate(
            ['paciente_id' => $laura->id, 'lugar' => 'Centro de Día Renacer'],
            ['duracion' => '6 meses (2024)']
        );
    }

    /** Campos obligatorios de Paciente que no hacen a la narrativa del demo. */
    private function datosBase(array $overrides): array
    {
        return array_merge([
            'obra_social' => 'Particular',
            'n_afiliado' => '-',
            'provincia' => 'Buenos Aires',
            'localidad' => 'Ciudad Autónoma de Buenos Aires',
            'calle' => 'Av. Corrientes',
            'calle_numero' => '1234',
        ], $overrides);
    }
}
