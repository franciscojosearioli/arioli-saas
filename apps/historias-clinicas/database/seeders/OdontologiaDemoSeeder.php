<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\User;
use App\Modules\Odontologia\Models\Odontograma;
use App\Modules\Odontologia\Models\PiezaDental;
use Illuminate\Database\Seeder;

/**
 * Escenario Demo — Etapa 6.2 (ver docs/ARQUITECTURA_MODULAR.md). No es un
 * generador de datos masivos: es una historia clínica chica y coherente,
 * pensada para que un odontólogo recorra el sistema en 5 minutos y sienta
 * que está viendo su propio consultorio, no una base de prueba genérica.
 */
class OdontologiaDemoSeeder extends Seeder
{
    public function run(): void
    {
        $dra = User::firstOrCreate(
            ['email' => 'carla.sosa@demo.local'],
            ['name' => 'Dra. Carla Sosa', 'password' => bcrypt('password'), 'firma_nombre' => 'Carla Sosa', 'firma_matricula' => 'MP 12345']
        );
        $dr = User::firstOrCreate(
            ['email' => 'martin.ibanez@demo.local'],
            ['name' => 'Dr. Martín Ibáñez', 'password' => bcrypt('password'), 'firma_nombre' => 'Martín Ibáñez', 'firma_matricula' => 'MP 67890']
        );

        $maria = Paciente::firstOrCreate(
            ['dni' => '30111222'],
            $this->datosBase(['nombre' => 'María', 'apellido' => 'González', 'sexo' => 'Femenino', 'fecha_nac' => '1988-04-12', 'edad' => 38])
        );

        // Narrativa: caries detectada, luego resuelta — muestra el histórico real
        $odontogramaInicial = Odontograma::create([
            'paciente_id' => $maria->id,
            'profesional_id' => $dra->id,
            'fecha' => now()->subMonths(3),
            'observaciones' => 'Control de rutina. Se detecta caries en pieza 26.',
        ]);
        foreach (Odontograma::numerosFdiAdulto() as $numero) {
            PiezaDental::create([
                'odontograma_id' => $odontogramaInicial->id,
                'numero' => $numero,
                'estado' => $numero === 26 ? 'cariada' : 'sana',
            ]);
        }

        $odontogramaControl = Odontograma::create([
            'paciente_id' => $maria->id,
            'profesional_id' => $dra->id,
            'fecha' => now(),
            'observaciones' => 'Restauración realizada en pieza 26. Próximo control en 6 meses.',
        ]);
        foreach (Odontograma::numerosFdiAdulto() as $numero) {
            PiezaDental::create([
                'odontograma_id' => $odontogramaControl->id,
                'numero' => $numero,
                'estado' => $numero === 26 ? 'obturada' : 'sana',
            ]);
        }

        // Otros 4 pacientes, un odontograma cada uno, variados (no todo "sana")
        $otros = [
            ['nombre' => 'Jorge', 'apellido' => 'Ramírez', 'dni' => '28222333', 'fecha_nac' => '1975-09-03', 'edad' => 50, 'profesional' => $dr, 'variacion' => [37 => 'ausente', 46 => 'obturada']],
            ['nombre' => 'Sofía', 'apellido' => 'Duarte', 'dni' => '35333444', 'fecha_nac' => '1997-01-20', 'edad' => 29, 'profesional' => $dra, 'variacion' => [11 => 'corona']],
            ['nombre' => 'Lucas', 'apellido' => 'Fernández', 'dni' => '33444555', 'fecha_nac' => '1993-11-15', 'edad' => 32, 'profesional' => $dr, 'variacion' => [18 => 'ausente', 28 => 'ausente']],
            ['nombre' => 'Valentina', 'apellido' => 'Molina', 'dni' => '36555666', 'fecha_nac' => '1999-06-30', 'edad' => 27, 'profesional' => $dra, 'variacion' => [16 => 'cariada']],
        ];

        foreach ($otros as $datos) {
            $paciente = Paciente::firstOrCreate(
                ['dni' => $datos['dni']],
                $this->datosBase(['nombre' => $datos['nombre'], 'apellido' => $datos['apellido'], 'fecha_nac' => $datos['fecha_nac'], 'edad' => $datos['edad']])
            );

            $odontograma = Odontograma::create([
                'paciente_id' => $paciente->id,
                'profesional_id' => $datos['profesional']->id,
                'fecha' => now()->subWeeks(random_int(1, 8)),
                'observaciones' => 'Odontograma inicial.',
            ]);

            foreach (Odontograma::numerosFdiAdulto() as $numero) {
                PiezaDental::create([
                    'odontograma_id' => $odontograma->id,
                    'numero' => $numero,
                    'estado' => $datos['variacion'][$numero] ?? 'sana',
                ]);
            }
        }
    }

    /** Campos obligatorios de Paciente que no hacen a la narrativa del demo. */
    private function datosBase(array $overrides): array
    {
        return array_merge([
            'sexo' => 'Femenino',
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
