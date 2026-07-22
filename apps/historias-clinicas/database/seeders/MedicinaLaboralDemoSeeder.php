<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\PacienteLaboral;
use App\Models\User;
use App\Modules\MedicinaLaboral\Models\EvaluacionLaboral;
use Illuminate\Database\Seeder;

/**
 * Escenario Demo — Etapa 6.2. Reutiliza PacienteLaboral, que ya existe en
 * el Core (ficha psicosocial extendida) — el "dato de empresa" no
 * necesitó ninguna entidad nueva, ya estaba cubierto.
 */
class MedicinaLaboralDemoSeeder extends Seeder
{
    public function run(): void
    {
        $medico = User::firstOrCreate(
            ['email' => 'roberto.aguirre@demo.local'],
            ['name' => 'Dr. Roberto Aguirre', 'password' => bcrypt('password'), 'firma_nombre' => 'Roberto Aguirre', 'firma_matricula' => 'MP 54321']
        );

        $juan = Paciente::firstOrCreate(
            ['dni' => '32777888'],
            $this->datosBase(['nombre' => 'Juan', 'apellido' => 'Pérez', 'sexo' => 'Masculino', 'fecha_nac' => '1985-02-18', 'edad' => 41])
        );

        PacienteLaboral::updateOrCreate(
            ['paciente_id' => $juan->id],
            [
                'actividad_laboral' => 'Si',
                'empresa_laboral' => 'Metalúrgica Delta',
                'cargo_laboral' => 'Operario de planta',
                'antiguedad_laboral' => '3 años',
            ]
        );

        EvaluacionLaboral::create([
            'paciente_id' => $juan->id,
            'profesional_id' => $medico->id,
            'tipo' => 'preocupacional',
            'fecha' => now()->subYears(3),
            'estado' => 'apto',
            'observaciones' => 'Incluye audiometría y espirometría, ambos dentro de parámetros normales.',
        ]);

        EvaluacionLaboral::create([
            'paciente_id' => $juan->id,
            'profesional_id' => $medico->id,
            'tipo' => 'periodico',
            'fecha' => now()->subMonths(2),
            'estado' => 'apto_con_restricciones',
            'observaciones' => 'Apto para tareas habituales. Se recomienda control auditivo anual por exposición a ruido.',
        ]);

        // Segundo empleado, empresa distinta — para que no parezca un caso único
        $ana = Paciente::firstOrCreate(
            ['dni' => '34999000'],
            $this->datosBase(['nombre' => 'Ana', 'apellido' => 'Torres', 'sexo' => 'Femenino', 'fecha_nac' => '1992-07-09', 'edad' => 34])
        );

        PacienteLaboral::updateOrCreate(
            ['paciente_id' => $ana->id],
            ['actividad_laboral' => 'Si', 'empresa_laboral' => 'Logística del Sur SA', 'cargo_laboral' => 'Administrativa', 'antiguedad_laboral' => '1 año']
        );

        EvaluacionLaboral::create([
            'paciente_id' => $ana->id,
            'profesional_id' => $medico->id,
            'tipo' => 'preocupacional',
            'fecha' => now()->subMonths(1),
            'estado' => 'apto',
            'observaciones' => 'Sin hallazgos de relevancia.',
        ]);
    }

    /** Campos obligatorios de Paciente que no hacen a la narrativa del demo. */
    private function datosBase(array $overrides): array
    {
        return array_merge([
            'estado_civil' => 'Soltero',
            'obra_social' => 'Particular',
            'n_afiliado' => '-',
            'provincia' => 'Buenos Aires',
            'localidad' => 'Ciudad Autónoma de Buenos Aires',
            'calle' => 'Av. Corrientes',
            'calle_numero' => '1234',
        ], $overrides);
    }
}
