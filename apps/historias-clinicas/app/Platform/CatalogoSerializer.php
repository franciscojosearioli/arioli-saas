<?php

namespace App\Platform;

use App\Platform\DTO\Componente;
use App\Platform\DTO\Perfil;

/**
 * Etapa 7.2 (ver docs/CATALOGO_COMPONENTES.md): único lugar que sabe
 * convertir el catálogo (`config('componentes')`/`config('perfiles')`) a
 * una representación serializable — el controller del endpoint no arma
 * arrays, solo llama acá. El objetivo es que cualquier consumidor externo
 * vea la misma realidad que ve historias-clinicas, no una reinterpretación
 * distinta armada ad-hoc en un controller.
 *
 * No serializa campos de instalación (`capabilities`, `capabilitiesDisabled`,
 * `fieldVisibilitySeed`, `tiposDocumentoSeed`, `configuracionInicial`,
 * `extension`, `navegacionSeed`) ni `Perfil::$demoSeeder` — son mecánica
 * interna de cómo se aplica el catálogo dentro de historias-clinicas, no
 * información comercial que un consumidor externo (checkout, ficha de
 * producto) necesite o deba conocer. `extension` tampoco es serializable
 * (instancia de `ComponenteExtension`, no un dato).
 */
final class CatalogoSerializer
{
    /** @return array{key: string, nombre: string, descripcion: string, categoria: ?string, core: bool, demo: bool, contratable: bool, dependencias: string[], perfiles: string[]} */
    public function componente(Componente $componente): array
    {
        return [
            'key' => $componente->key,
            'nombre' => $componente->nombre,
            'descripcion' => $componente->descripcion,
            'categoria' => $componente->categoria,
            'core' => $componente->core,
            'demo' => $componente->demo,
            'contratable' => $componente->contratable,
            'dependencias' => $componente->dependencias,
            'perfiles' => CatalogoComponentes::perfilesQueUsan($componente->key),
        ];
    }

    /** @return array{key: string, nombre: string, descripcion: string, componentes: string[], nombre_sistema: string, caracteristicas: string[]} */
    public function perfil(Perfil $perfil): array
    {
        return [
            'key' => $perfil->key,
            'nombre' => $perfil->nombre,
            'descripcion' => $perfil->descripcion,
            'componentes' => $perfil->componentes,
            'nombre_sistema' => $perfil->nombreSistema,
            'caracteristicas' => $perfil->caracteristicas,
        ];
    }

    /** @return array{componentes: array[], perfiles: array[]} */
    public function catalogoCompleto(): array
    {
        return [
            'componentes' => collect(config('componentes', []))
                ->map(fn (Componente $c) => $this->componente($c))
                ->values()
                ->all(),
            'perfiles' => collect(config('perfiles', []))
                ->map(fn (Perfil $p) => $this->perfil($p))
                ->values()
                ->all(),
        ];
    }
}
