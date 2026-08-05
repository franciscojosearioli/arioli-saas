<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\InformeTipo;
use App\Models\Paciente;
use App\Models\User;
use App\Modules\Odontologia\Models\HistorialOdontologico;
use App\Modules\Odontologia\Models\Odontograma;
use App\Modules\Odontologia\OdontogramaPiezaSeeder;
use Illuminate\Database\Seeder;

/**
 * Escenario Demo — Etapa 6.2 (ver docs/ARQUITECTURA_MODULAR.md). No es un
 * generador de datos masivos: es una historia clínica chica y coherente,
 * pensada para que un odontólogo recorra el sistema en 5 minutos y sienta
 * que está viendo su propio consultorio, no una base de prueba genérica.
 *
 * Etapa 6.6.5: reescrito sobre el modelo nuevo — Odontograma es 1 por
 * paciente, no 1 por visita. La narrativa "caries detectada → resuelta"
 * de María González ya no se representa con dos Odontograma separados
 * (imposible ahora, la cardinalidad es 1x1): se representa con el estado
 * ACTUAL de la superficie (obturada) más dos filas de HistorialOdontologico
 * con fecha retroactiva mostrando de dónde vino — literalmente para lo que
 * se construyó HistorialOdontologico.
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

        // Especialidad y tipos de informe del perfil — sin esto el consultorio
        // odontológico nace con esos catálogos vacíos (ver docs/ARQUITECTURA_MODULAR.md).
        $especialidad = Especialidad::firstOrCreate(
            ['nombre' => 'Odontología General'],
            ['descripcion' => 'Diagnóstico, prevención y tratamiento de la salud bucodental.']
        );
        $especialidad->profesionales()->syncWithoutDetaching([$dra->id, $dr->id]);

        foreach (['Informe Odontológico', 'Presupuesto de Tratamiento', 'Informe Clínico General'] as $tipo) {
            InformeTipo::firstOrCreate(['name' => $tipo]);
        }

        $maria = Paciente::firstOrCreate(
            ['dni' => '30111222'],
            $this->datosBase(['nombre' => 'María', 'apellido' => 'González', 'sexo' => 'Femenino', 'fecha_nac' => '1988-04-12', 'edad' => 38])
        );

        $odontogramaMaria = Odontograma::create([
            'paciente_id' => $maria->id,
            'profesional_id' => $dra->id,
            'fecha' => now()->subMonths(3),
        ]);
        OdontogramaPiezaSeeder::sembrar($odontogramaMaria, 'permanente');

        // Narrativa: caries detectada hace 3 meses, restaurada hoy —
        // estado actual = obturada, con el historial mostrando de dónde vino.
        $pieza26 = $odontogramaMaria->piezas()->where('numero_fdi', 26)->first();
        $superficieOclusal = $pieza26->superficies()->where('superficie', 'oclusal')->first();
        $superficieOclusal->update(['estado' => 'obturada', 'observaciones' => 'Restauración realizada. Próximo control en 6 meses.']);

        HistorialOdontologico::create([
            'entidad_tipo' => 'superficie',
            'entidad_id' => $superficieOclusal->id,
            'estado_anterior' => null,
            'estado_nuevo' => 'cariada',
            'profesional_id' => $dra->id,
            'motivo' => 'Control de rutina. Se detecta caries.',
            'created_at' => now()->subMonths(3),
        ]);
        HistorialOdontologico::create([
            'entidad_tipo' => 'superficie',
            'entidad_id' => $superficieOclusal->id,
            'estado_anterior' => 'cariada',
            'estado_nuevo' => 'obturada',
            'profesional_id' => $dra->id,
            'motivo' => 'Restauración realizada.',
            'created_at' => now(),
        ]);

        // Otros 4 pacientes, un odontograma cada uno, con variaciones reales
        // (no todo "sana") en distintas superficies.
        $otros = [
            ['nombre' => 'Jorge', 'apellido' => 'Ramírez', 'dni' => '28222333', 'fecha_nac' => '1975-09-03', 'edad' => 50, 'profesional' => $dr, 'variacion' => [37 => ['general' => 'ausente'], 46 => ['oclusal' => 'obturada']]],
            ['nombre' => 'Sofía', 'apellido' => 'Duarte', 'dni' => '35333444', 'fecha_nac' => '1997-01-20', 'edad' => 29, 'profesional' => $dra, 'variacion' => [11 => ['vestibular' => 'corona']]],
            ['nombre' => 'Lucas', 'apellido' => 'Fernández', 'dni' => '33444555', 'fecha_nac' => '1993-11-15', 'edad' => 32, 'profesional' => $dr, 'variacion' => [18 => ['general' => 'ausente'], 28 => ['general' => 'ausente']]],
            ['nombre' => 'Valentina', 'apellido' => 'Molina', 'dni' => '36555666', 'fecha_nac' => '1999-06-30', 'edad' => 27, 'profesional' => $dra, 'variacion' => [16 => ['oclusal' => 'cariada']]],
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
            ]);
            OdontogramaPiezaSeeder::sembrar($odontograma, 'permanente');

            foreach ($datos['variacion'] as $numero => $cambios) {
                $pieza = $odontograma->piezas()->where('numero_fdi', $numero)->first();

                if (isset($cambios['general'])) {
                    $pieza->update(['estado_general' => $cambios['general']]);
                    continue;
                }

                foreach ($cambios as $superficieKey => $estado) {
                    $pieza->superficies()->where('superficie', $superficieKey)->first()?->update(['estado' => $estado]);
                }
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
