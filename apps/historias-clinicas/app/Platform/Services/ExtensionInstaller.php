<?php

namespace App\Platform\Services;

use App\Platform\Contracts\ComponenteExtension;
use App\Platform\Contracts\Services\ExtensionInstallerContract;
use Illuminate\Support\Facades\DB;

/**
 * Corre ComponenteExtension::instalar() solo si no está instalada, o si la
 * versión declarada cambió (reinstalación / actualización futura). Registra
 * el resultado en componente_extensiones — "este tenant tiene instalada
 * esta extensión, en esta versión", el dato que Salud Mental nunca necesitó
 * porque no traía comportamiento propio.
 */
class ExtensionInstaller implements ExtensionInstallerContract
{
    public function aplicar(string $componenteKey, ComponenteExtension $extension): void
    {
        $registro = DB::table('componente_extensiones')
            ->where('componente_key', $componenteKey)
            ->first();

        if ($registro && $registro->version === $extension->version()) {
            return;
        }

        $extension->instalar();

        DB::table('componente_extensiones')->updateOrInsert(
            ['componente_key' => $componenteKey],
            [
                'extension_key' => get_class($extension),
                'version' => $extension->version(),
                'instalado_en' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }
}
