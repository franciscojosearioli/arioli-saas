<?php

namespace App\Platform\Modules;

use App\Platform\Contracts\ModuleDefinition;
use App\Platform\DTO\ModuleManifest;
use App\Platform\DTO\NavigationContribution;
use App\Platform\DTO\NavigationItem;

/**
 * Apunta al código de Agenda tal como ya existe hoy (app/Models/Agenda.php,
 * app/Http/Controllers/Panel/AgendaController.php — y su duplicado en
 * Admin/, ver docs/ARQUITECTURA_MODULAR.md Fase 2 sobre esa duplicación
 * pendiente de unificar). No se movió ni un archivo.
 *
 * eventosEmitidos()/eventosEscuchados() devuelven [] a propósito: eventos de
 * dominio como TurnoCancelado/TurnoConfirmado son el candidato natural acá,
 * pero requieren decidir desde dónde se disparan (AgendaController@cambiarEstado)
 * y el catálogo de eventos de PlatformRegistry todavía no existe — se agregan
 * cuando ese mecanismo se construya, no antes.
 */
class AgendaModule implements ModuleDefinition
{
    public function manifest(): ModuleManifest
    {
        return new ModuleManifest(
            key: 'agenda',
            nombre: 'Agenda',
            descripcion: 'Turnos y citas: pacientes, profesionales (internos o externos), estados y modalidad.',
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
        return ['agenda'];
    }

    public function permisos(): array
    {
        return [
            'agenda_access',
            'agenda_create',
            'agenda_show',
            'agenda_edit',
            'agenda_delete',
        ];
    }

    public function contributions(): iterable
    {
        yield new NavigationContribution(new NavigationItem(
            key: 'agenda',
            label: 'Agenda de Citas',
            route: 'panel.agenda.index',
            capabilityRequerida: 'agenda',
            permisoRequerido: 'agenda_access',
            orden: 90,
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
