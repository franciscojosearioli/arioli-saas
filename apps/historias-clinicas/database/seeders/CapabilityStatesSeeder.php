<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Habilita las capabilities de los módulos ya registrados en la Fase 0,
 * con source='preset' — nada cambia de comportamiento el día del deploy,
 * solo queda el estado explícito para que AuthGates/PlatformRegistry lo
 * puedan consultar.
 */
class CapabilityStatesSeeder extends Seeder
{
    public function run(): void
    {
        $capabilities = ['especialidades', 'agenda', 'consentimientos', 'medicacion', 'historia_clinica'];

        foreach ($capabilities as $key) {
            DB::table('capability_states')->updateOrInsert(
                ['capability_key' => $key],
                [
                    'enabled' => true,
                    'source' => 'preset',
                    'enabled_at' => now(),
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
