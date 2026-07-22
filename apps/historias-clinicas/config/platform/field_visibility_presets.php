<?php

use App\Platform\DTO\FieldVisibilitySeed;

/*
 * Presets de visibilidad — Etapa 2 (ver docs/ARQUITECTURA_MODULAR.md).
 * Todavía no los dispara ningún Componente (eso es Etapa 3): se aplican
 * hoy directamente vía FieldVisibilityInstaller::aplicar() para validar
 * el mecanismo con más de un preset real.
 */
return [
    'ficha_psicosocial_extendida' => [
        new FieldVisibilitySeed('paciente', 'problematica', 'seccion', true),
        new FieldVisibilitySeed('paciente', 'datos_adicionales', 'seccion', true),
        new FieldVisibilitySeed('paciente', 'familia', 'seccion', true),
        new FieldVisibilitySeed('paciente', 'educacion', 'seccion', true),
        new FieldVisibilitySeed('paciente', 'laboral', 'seccion', true),
        new FieldVisibilitySeed('paciente', 'historial_tratamientos', 'seccion', true),
    ],

    'ficha_clinica_basica' => [
        new FieldVisibilitySeed('paciente', 'problematica', 'seccion', false),
        new FieldVisibilitySeed('paciente', 'datos_adicionales', 'seccion', false),
        new FieldVisibilitySeed('paciente', 'familia', 'seccion', false),
        new FieldVisibilitySeed('paciente', 'educacion', 'seccion', false),
        new FieldVisibilitySeed('paciente', 'laboral', 'seccion', false),
        new FieldVisibilitySeed('paciente', 'historial_tratamientos', 'seccion', false),
    ],
];
