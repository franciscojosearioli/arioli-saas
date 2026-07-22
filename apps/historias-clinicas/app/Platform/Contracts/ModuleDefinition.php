<?php

namespace App\Platform\Contracts;

use App\Platform\DTO\ModuleManifest;

/**
 * Contrato que implementa cada módulo/componente de la plataforma.
 * Ver docs/ARQUITECTURA_MODULAR.md sección 2.
 *
 * No todos los métodos tienen uso todavía: los módulos de Fase 0 devuelven
 * arrays vacíos para lo que no aplica (extensionPoints, eventos, etc.) — el
 * contrato se declaró completo desde el diseño para que no haga falta romper
 * la interfaz cuando un módulo futuro sí los necesite.
 */
interface ModuleDefinition
{
    public function manifest(): ModuleManifest;

    /** @return string[] module keys de los que depende */
    public function dependencias(): array;

    /** @return string[] module keys con los que no puede convivir habilitado */
    public function conflicts(): array;

    /** @return string[] capability_key que este módulo declara */
    public function capabilities(): array;

    /** @return string[] permission titles que pertenecen a este módulo */
    public function permisos(): array;

    /** @return iterable<Contribution> */
    public function contributions(): iterable;

    /**
     * Puntos donde este módulo acepta ser extendido por otro (mecanismo de
     * Contribution + ExtensionPointContract, ver docs/ARQUITECTURA_MODULAR.md
     * sección 2/4.3) — sin uso todavía en Fase 0, los módulos actuales
     * devuelven [].
     *
     * @return array
     */
    public function extensionPoints(): array;

    /** @return class-string[] FQCN de eventos de dominio que este módulo emite (declarativo, catálogo) */
    public function eventosEmitidos(): array;

    /** @return class-string[] FQCN de eventos de dominio que este módulo escucha (declarativo, catálogo) */
    public function eventosEscuchados(): array;

    public function migrationsPath(): ?string;
}
