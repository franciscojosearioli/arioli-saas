<?php

namespace App\Platform\Services;

use App\Platform\Contracts\Services\CapabilityInstallerContract;
use Illuminate\Support\Facades\DB;

/**
 * Mismo algoritmo no-destructivo que ya usaba CapabilityStatesSeeder para
 * el bootstrap manual de los primeros 5 módulos — se extrae a clase propia
 * acá porque ComponenteInstaller es la segunda vez que lo necesita
 * (repetición real, no anticipada). CapabilityStatesSeeder queda como está
 * para el bootstrap ya deployado, no se retira sin necesidad.
 */
class CapabilityInstaller implements CapabilityInstallerContract
{
    public function aplicar(array $capabilityKeys): void
    {
        foreach ($capabilityKeys as $key) {
            $existente = DB::table('capability_states')->where('capability_key', $key)->first();

            if ($existente && $existente->source === 'manual') {
                continue;
            }

            if ($existente) {
                DB::table('capability_states')->where('capability_key', $key)->update([
                    'enabled' => true,
                    'source' => 'preset',
                    'enabled_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('capability_states')->insert([
                    'capability_key' => $key,
                    'enabled' => true,
                    'source' => 'preset',
                    'enabled_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
