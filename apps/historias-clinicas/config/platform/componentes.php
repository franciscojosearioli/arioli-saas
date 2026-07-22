<?php

use App\Modules\Odontologia\OdontologiaExtension;
use App\Platform\DTO\Componente;

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
        capabilities: [],
        fieldVisibilitySeed: ['ficha_psicosocial_extendida'],
    ),

    'odontologia' => new Componente(
        key: 'odontologia',
        nombre: 'Odontología',
        descripcion: 'Módulo odontológico — versión mínima, solo permisos provisionados dinámicamente.',
        capabilities: ['odontologia'],
        extension: new OdontologiaExtension(),
    ),
];
