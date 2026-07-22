<?php

namespace App\Platform\Modules;

use App\Platform\Contracts\ModuleDefinition;
use App\Platform\DTO\ModuleManifest;
use App\Platform\DTO\NavigationContribution;
use App\Platform\DTO\NavigationItem;

/**
 * Apunta al código existente (app/Models/Medicacion.php,
 * Panel/MedicacionController.php — y su duplicado en Admin/, misma
 * deuda de Fase 2 que Agenda). Mismo patrón que Especialidades/Agenda:
 * permiso propio existente, gating vía AuthGates.
 */
class MedicacionModule implements ModuleDefinition
{
    public function manifest(): ModuleManifest
    {
        return new ModuleManifest(
            key: 'medicacion',
            nombre: 'Medicación',
            descripcion: 'Registro/kardex de administración de medicación por paciente y fecha.',
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
        return ['medicacion'];
    }

    public function permisos(): array
    {
        return [
            'medicacion_management_access',
            'medicacion_access',
            'medicacion_create',
            'medicacion_show',
            'medicacion_edit',
            'medicacion_delete',
        ];
    }

    public function contributions(): iterable
    {
        yield new NavigationContribution(new NavigationItem(
            key: 'medicacion',
            label: 'Prescripciones',
            route: 'panel.medicacion.index',
            capabilityRequerida: 'medicacion',
            permisoRequerido: 'medicacion_access',
            orden: 95,
        ));
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
