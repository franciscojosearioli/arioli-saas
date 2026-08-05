<?php

use App\Modules\Odontologia\OdontologiaExtension;
use App\Platform\DTO\Componente;
use App\Platform\DTO\NavigationItem;

/*
 * Catálogo de Componentes — Etapa 3/4 (ver docs/ARQUITECTURA_MODULAR.md).
 * "salud_mental" es deliberadamente aburrido: solo declara datos, no sabe
 * cómo se instala. capabilities=[] es honesto — hoy no existe ninguna
 * capability nueva que Salud Mental deba encender (historia_clinica,
 * agenda, consentimientos ya están siempre activas); lo único que
 * realmente aporta es el fieldVisibilitySeed.
 *
 * "odontologia" es el primer Componente con ComponenteExtension real —
 * mínima a propósito (ver OdontologiaExtension), solo provisiona permisos.
 */
return [
    'salud_mental' => new Componente(
        key: 'salud_mental',
        nombre: 'Salud Mental / Adicciones',
        descripcion: 'Habilita la ficha psicosocial extendida: familia, educación, situación laboral, problemática y antecedentes.',
        categoria: 'especialidad',
        core: false,
        demo: true,
        contratable: true,
        dependencias: [],
        capabilities: [],
        fieldVisibilitySeed: ['ficha_psicosocial_extendida'],
    ),

    'odontologia' => new Componente(
        key: 'odontologia',
        nombre: 'Odontología',
        descripcion: 'Módulo odontológico — versión mínima, permisos provisionados dinámicamente + un ítem de menú real.',
        categoria: 'especialidad',
        core: false,
        demo: true,
        contratable: true,
        dependencias: [],
        capabilities: ['odontologia'],
        // 'medicacion' es seguimiento longitudinal de medicación por
        // paciente (dosis/horarios/fecha, ver MedicacionController) — no
        // "Recetas" (RecetaController, documento de prescripción ligado a
        // un Informe, que sí corresponde a Odontología y queda intacto).
        // 'consentimientos' (Etapa 6.6.2, pedido explícito de Francisco
        // tras probar el flujo real): no aplica al consultorio
        // odontológico — no hay consentimientos informados de tipo
        // psicosocial/tratamiento clínico que este perfil use.
        capabilitiesDisabled: ['medicacion', 'consentimientos'],
        fieldVisibilitySeed: ['ficha_clinica_basica'],
        extension: new OdontologiaExtension(),
        navegacionSeed: [
            new NavigationItem(
                key: 'odontologia',
                label: 'Odontología',
                route: 'panel.odontologia.index',
                capabilityRequerida: 'odontologia',
                permisoRequerido: 'odontologia_access',
                orden: 96,
            ),
        ],
    ),
];
