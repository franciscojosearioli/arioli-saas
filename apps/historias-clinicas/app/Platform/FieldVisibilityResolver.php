<?php

namespace App\Platform;

use App\Models\FieldVisibility;

/**
 * Resuelve si un campo/sección/tab de una entidad debe mostrarse.
 * Sin fila -> visible=true (comportamiento actual, zero-risk: nada
 * desaparece hasta que alguien explícitamente lo oculte).
 * Ver docs/ARQUITECTURA_MODULAR.md sección 1.7 y "Etapa 1 — Historia Clínica".
 */
class FieldVisibilityResolver
{
    /** @var array<string, bool>|null */
    private ?array $cache = null;

    public function isVisible(string $entidad, string $campo): bool
    {
        $estados = $this->loadEstados();

        return $estados[$entidad . '.' . $campo] ?? true;
    }

    /** @return array<string, bool> "entidad.campo" => visible */
    private function loadEstados(): array
    {
        if ($this->cache !== null) {
            return $this->cache;
        }

        $this->cache = FieldVisibility::all()
            ->mapWithKeys(fn (FieldVisibility $fv) => [$fv->entidad . '.' . $fv->campo => $fv->visible])
            ->all();

        return $this->cache;
    }
}
