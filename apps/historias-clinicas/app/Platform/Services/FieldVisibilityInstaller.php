<?php

namespace App\Platform\Services;

use App\Models\FieldVisibility;
use App\Platform\Contracts\Services\FieldVisibilityInstallerContract;
use App\Platform\DTO\FieldVisibilitySeed;

/**
 * Aplica presets de visibilidad — servicio separado de ComponenteInstaller
 * a propósito (Etapa 2, ver docs/ARQUITECTURA_MODULAR.md): instalar un
 * Componente y aplicar visibilidad de campos son responsabilidades
 * distintas, aunque compartan el mismo algoritmo no-destructivo que ya
 * probó capability_states (nunca pisa una fila con origen='manual').
 *
 * Todavía NO conectado a ComponenteInstaller (eso es Etapa 3) — hoy se
 * invoca directamente con un preset, para validar el mecanismo en sí.
 */
class FieldVisibilityInstaller implements FieldVisibilityInstallerContract
{
    public function aplicar(array $seeds): void
    {
        foreach ($seeds as $seed) {
            $existente = FieldVisibility::where('entidad', $seed->entidad)
                ->where('campo', $seed->campo)
                ->first();

            if ($existente && $existente->origen === 'manual') {
                continue;
            }

            FieldVisibility::updateOrCreate(
                ['entidad' => $seed->entidad, 'campo' => $seed->campo],
                [
                    'tipo' => $seed->tipo,
                    'visible' => $seed->visible,
                    'requerido' => $seed->requerido,
                    'origen' => 'preset',
                ]
            );
        }
    }
}
