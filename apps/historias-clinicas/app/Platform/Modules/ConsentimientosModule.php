<?php

namespace App\Platform\Modules;

use App\Platform\Contracts\ModuleDefinition;
use App\Platform\DTO\ModuleManifest;

/**
 * Apunta al código existente (app/Models/Consentimiento.php,
 * app/Models/TipoConsentimiento.php, Panel/ConsentimientoController.php,
 * Admin/TipoConsentimientoController.php, FirmaPublicaController.php) sin
 * mover nada.
 *
 * Caso distinto a Especialidades/Agenda: Consentimientos no tiene permisos
 * propios — el controller usa paciente_edit/paciente_show prestados. Por
 * eso permisos() devuelve [] (sería deshonesto inventar un permiso que no
 * existe) y el gating de la capability se hace a nivel de ruta
 * (middleware 'capability:consentimientos' en routes/web.php,
 * EnsureCapabilityEnabled), no vía AuthGates.
 *
 * Tampoco hay contribución de navegación: no existe un ítem de menú propio,
 * se accede contextualmente desde la ficha del paciente.
 */
class ConsentimientosModule implements ModuleDefinition
{
    public function manifest(): ModuleManifest
    {
        return new ModuleManifest(
            key: 'consentimientos',
            nombre: 'Consentimientos',
            descripcion: 'Consentimientos informados por tipo, con firma presencial o remota por token y generación de PDF.',
            category: 'core',
        );
    }

    public function dependencias(): array
    {
        return [];
    }

    public function conflicts(): array
    {
        return [];
    }

    public function capabilities(): array
    {
        return ['consentimientos'];
    }

    public function permisos(): array
    {
        return [];
    }

    public function contributions(): iterable
    {
        return [];
    }

    public function extensionPoints(): array
    {
        return [];
    }

    public function eventosEmitidos(): array
    {
        return [];
    }

    public function eventosEscuchados(): array
    {
        return [];
    }

    public function migrationsPath(): ?string
    {
        return null;
    }
}
