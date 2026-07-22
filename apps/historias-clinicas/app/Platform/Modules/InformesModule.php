<?php

namespace App\Platform\Modules;

use App\Platform\Contracts\ModuleDefinition;
use App\Platform\DTO\ModuleManifest;
use App\Platform\DTO\NavigationContribution;
use App\Platform\DTO\NavigationItem;

/**
 * Apunta al código existente (app/Models/Informe.php, InformeTipo.php,
 * Panel/InformeController.php — y su duplicado en Admin/). Es el primer
 * módulo que además trae infraestructura genuinamente nueva: el motor
 * TipoDocumento -> PlantillaDocumento -> versión -> Documento (ver
 * app/Models/PlantillaDocumento.php, PlantillaDocumentoVersion.php).
 *
 * El motor está construido y probado con datos reales, pero todavía NO
 * está conectado al formulario real de creación de Informe (Panel/InformeController
 * sigue guardando `redaccion` como texto libre, como siempre) — conectar la
 * UI real para que un profesional elija plantilla/versión al crear un
 * informe es el siguiente incremento, deliberadamente no incluido acá para
 * no mezclar "el motor funciona" con "cambiar el flujo de creación en vivo".
 */
class InformesModule implements ModuleDefinition
{
    public function manifest(): ModuleManifest
    {
        return new ModuleManifest(
            key: 'informes',
            nombre: 'Informes / Historia Clínica',
            descripcion: 'Informes clínicos por tipo, con motor de plantillas versionadas y políticas de firma/portal.',
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
        return ['historia_clinica'];
    }

    public function permisos(): array
    {
        return [
            'informe_management_access',
            'informe_access',
            'informe_create',
            'informe_show',
            'informe_edit',
            'informe_delete',
            'informe_psicologico_access',
            'informe_psicologico_create',
            'informe_psiquiatrico_access',
            'informe_psiquiatrico_create',
            'informe_clinico_access',
            'informe_clinico_create',
            'informe_operador_access',
            'informe_operador_create',
            'informe_judicial_access',
            'informe_judicial_create',
            'informe_tipo_access',
            'informe_tipo_create',
            'informe_tipo_show',
            'informe_tipo_edit',
            'informe_tipo_delete',
        ];
    }

    public function contributions(): iterable
    {
        yield new NavigationContribution(new NavigationItem(
            key: 'informes',
            label: 'Informes Clínicos',
            route: 'panel.informe.index',
            capabilityRequerida: 'historia_clinica',
            permisoRequerido: 'informe_access',
            orden: 80,
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
