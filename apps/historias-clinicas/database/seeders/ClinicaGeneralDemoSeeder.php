<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use App\Models\InformeTipo;
use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Escenario Demo del perfil clinica_general (Etapa 6.2, ver
 * docs/ARQUITECTURA_MODULAR.md). El perfil "Clínica / Consultorio Médico"
 * no instala ningún Componente opcional (sin odontograma, sin ficha
 * psicosocial extendida) — es la historia clínica base del Core. Antes de
 * este seeder nacía sin ningún dato de ejemplo: sin este seeder, un
 * consultorio general demo arrancaba con especialidades y tipos de informe
 * vacíos (a diferencia de Odontología/Salud Mental, que ya tenían el suyo).
 */
class ClinicaGeneralDemoSeeder extends Seeder
{
    public function run(): void
    {
        $medica = User::firstOrCreate(
            ['email' => 'silvina.paez@demo.local'],
            ['name' => 'Dra. Silvina Páez', 'password' => bcrypt('password'), 'firma_nombre' => 'Silvina Páez', 'firma_matricula' => 'MP 24680']
        );

        $especialidad = Especialidad::firstOrCreate(
            ['nombre' => 'Clínica Médica'],
            ['descripcion' => 'Atención médica general, diagnóstico y seguimiento de patologías prevalentes.']
        );
        $especialidad->profesionales()->syncWithoutDetaching([$medica->id]);

        foreach (['Informe Clínico General', 'Certificado Médico'] as $tipo) {
            InformeTipo::firstOrCreate(['name' => $tipo]);
        }

        Paciente::firstOrCreate(
            ['dni' => '27888999'],
            $this->datosBase(['nombre' => 'Pedro', 'apellido' => 'Álvarez', 'sexo' => 'Masculino', 'fecha_nac' => '1968-05-14', 'edad' => 58])
        );

        Paciente::firstOrCreate(
            ['dni' => '40123456'],
            $this->datosBase(['nombre' => 'Rosa', 'apellido' => 'Benítez', 'sexo' => 'Femenino', 'fecha_nac' => '1995-09-27', 'edad' => 30])
        );
    }

    /** Campos obligatorios de Paciente que no hacen a la narrativa del demo. */
    private function datosBase(array $overrides): array
    {
        return array_merge([
            'estado_civil' => 'Casado',
            'obra_social' => 'Particular',
            'n_afiliado' => '-',
            'provincia' => 'Buenos Aires',
            'localidad' => 'Ciudad Autónoma de Buenos Aires',
            'calle' => 'Av. Corrientes',
            'calle_numero' => '1234',
        ], $overrides);
    }
}
