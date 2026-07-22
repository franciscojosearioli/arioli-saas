<?php

namespace App\Platform\Modules;

use App\Platform\Contracts\ModuleDefinition;
use App\Platform\DTO\ModuleManifest;
use App\Platform\DTO\NavigationContribution;
use App\Platform\DTO\NavigationItem;

/**
 * Módulo de prueba de concepto de la Fase 0. Apunta al código de
 * Especialidades tal como ya existe hoy (app/Models/Especialidad.php,
 * app/Http/Controllers/Admin/EspecialidadController.php, rutas en
 * routes/web.php) — no se movió ni un archivo, este manifiesto solo
 * describe lo que ya está.
 */
class EspecialidadesModule implements ModuleDefinition
{
    public function manifest(): ModuleManifest
    {
        return new ModuleManifest(
            key: 'especialidades',
            nombre: 'Especialidades',
            descripcion: 'Catálogo de especialidades y su asignación a profesionales.',
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
        return ['especialidades'];
    }

    public function permisos(): array
    {
        return [
            'especialidad_access',
            'especialidad_create',
            'especialidad_edit',
            'especialidad_delete',
        ];
    }

    public function contributions(): iterable
    {
        yield new NavigationContribution(new NavigationItem(
            key: 'especialidades',
            label: 'Especialidades',
            route: 'admin.especialidades.index',
            capabilityRequerida: 'especialidades',
            permisoRequerido: 'especialidad_access',
            orden: 100,
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
