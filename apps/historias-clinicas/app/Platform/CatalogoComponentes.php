<?php

namespace App\Platform;

use App\Platform\DTO\Perfil;

/**
 * Etapa 7.1 (ver docs/CATALOGO_COMPONENTES.md): "qué perfiles usan este
 * Componente" se pidió como dato del catálogo, pero declararlo como campo
 * en `Componente` habría creado exactamente el tipo de duplicación
 * bidireccional que esta etapa existe para eliminar — `Perfil::$componentes`
 * ya dice "qué Componentes instalo"; si `Componente` también dijera "qué
 * Perfiles me usan", las dos listas tendrían que mantenerse sincronizadas
 * a mano en direcciones opuestas cada vez que un Perfil cambia.
 *
 * En cambio, es una consulta derivada — un solo lugar de autoría
 * (`Perfil::$componentes`), la vista inversa se calcula, nunca se declara
 * dos veces. `Componente`/`Perfil` se mantienen puramente descriptivos, sin
 * conocer `config()` — esta clase es la que sí lo consulta.
 */
final class CatalogoComponentes
{
    /** @return string[] keys de Perfil que instalan el Componente dado */
    public static function perfilesQueUsan(string $componenteKey): array
    {
        return collect(config('perfiles', []))
            ->filter(fn (Perfil $perfil) => in_array($componenteKey, $perfil->componentes, true))
            ->keys()
            ->all();
    }
}
