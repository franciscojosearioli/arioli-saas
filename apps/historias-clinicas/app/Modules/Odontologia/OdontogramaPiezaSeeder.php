<?php

namespace App\Modules\Odontologia;

use App\Modules\Odontologia\Models\Odontograma;
use App\Modules\Odontologia\Models\PiezaOdontologica;
use App\Modules\Odontologia\Models\SuperficieOdontologica;

/**
 * Etapa 6.6.5: sembrar piezas+superficies para una dentición hace falta en
 * dos lugares reales — OdontologiaController (cuando se crea el
 * odontograma de un paciente, o se agrega dentición temporal) y
 * OdontologiaDemoSeeder (Escenario Demo). Segunda ocurrencia real,
 * extraída acá en vez de duplicar el loop — no es una abstracción
 * anticipada, ya tiene 2 consumidores.
 */
class OdontogramaPiezaSeeder
{
    public static function sembrar(Odontograma $odontograma, string $denticion): void
    {
        $catalogo = config('piezas_dentales_catalogo.piezas', []);

        foreach ($catalogo as $numero => $datos) {
            if ($datos['denticion'] !== $denticion) {
                continue;
            }

            $pieza = PiezaOdontologica::firstOrCreate([
                'odontograma_id' => $odontograma->id,
                'numero_fdi' => $numero,
            ]);

            foreach ($datos['superficies'] as $superficie) {
                SuperficieOdontologica::firstOrCreate([
                    'pieza_odontologica_id' => $pieza->id,
                    'superficie' => $superficie,
                ], ['estado' => 'sana']);
            }
        }
    }
}
