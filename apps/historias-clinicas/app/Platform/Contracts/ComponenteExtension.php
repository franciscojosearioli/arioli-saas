<?php

namespace App\Platform\Contracts;

/**
 * Hook de instalación custom de un Componente — para comportamiento que
 * los seeds declarativos (capabilities, fieldVisibilitySeed,
 * tiposDocumentoSeed, configuracionInicial) no cubren.
 *
 * NO es lo mismo que ExtensionContribution (ver docs/ARQUITECTURA_MODULAR.md
 * sección 2, v6-v7): aquel es para inyectar contenido tipado dentro de un
 * extension point que otro módulo declaró (ej. una pestaña en la ficha de
 * paciente). Esto es el hook de instalación de un Componente en sí mismo —
 * dos problemas distintos que comparten un nombre parecido a propósito
 * evitado.
 *
 * Deliberadamente mínima: solo version() + instalar(). Nada de routes(),
 * migrations(), controllers(), policies() todavía — se agregan cuando un
 * caso real lo obligue, no antes.
 */
interface ComponenteExtension
{
    /** Cambiar el string dispara una reinstalación (ver ExtensionInstaller). */
    public function version(): string;

    /** Debe ser idempotente — puede volver a ejecutarse en cada bump de version(). */
    public function instalar(): void;
}
