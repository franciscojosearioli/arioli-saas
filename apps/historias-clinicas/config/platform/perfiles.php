<?php

use App\Platform\DTO\Perfil;

/*
 * Catálogo de Perfiles — Etapa 5.1 (ver docs/ARQUITECTURA_MODULAR.md).
 * Define cómo nace un tenant (qué Componentes se instalan de entrada),
 * distinto del catálogo de Componentes en sí (qué existe en la
 * plataforma). Un perfil es la respuesta a "¿qué tipo de institución es
 * esta?" en el momento de provisionar — hoy sin UI que lo consuma (eso es
 * el sistema de Demo Provisioning, todavía futuro), pero ya usable
 * directamente vía ComponenteInstaller::instalar($perfil->componentes).
 */
return [
    'clinica_general' => new Perfil(
        key: 'clinica_general',
        nombre: 'Clínica / Consultorio Médico',
        descripcion: 'Historia clínica, agenda y turnos — sin especialidades adicionales.',
        componentes: [],
        nombreSistema: 'Sistema de Salud Médico',
        caracteristicas: ['Agenda y turnos', 'Historia clínica completa', 'Recetas', 'Consentimientos informados', 'Especialidades médicas'],
        demoSeeder: \Database\Seeders\ClinicaGeneralDemoSeeder::class,
    ),
    'odontologia' => new Perfil(
        key: 'odontologia',
        nombre: 'Consultorio Odontológico',
        descripcion: 'Consultorio odontológico.',
        componentes: ['odontologia'],
        nombreSistema: 'Sistema de Salud Odontología',
        caracteristicas: ['Agenda y turnos', 'Historia clínica del paciente', 'Odontograma por pieza dental', 'Recetas odontológicas'],
        demoSeeder: \Database\Seeders\OdontologiaDemoSeeder::class,
    ),
    'salud_mental' => new Perfil(
        key: 'salud_mental',
        nombre: 'Centro de Salud Mental',
        descripcion: 'Centro de tratamiento con ficha psicosocial extendida.',
        componentes: ['salud_mental'],
        nombreSistema: 'Sistema de Salud Mental',
        caracteristicas: ['Agenda y turnos', 'Historia clínica con ficha psicosocial extendida', 'Antecedentes, familia, educación y situación laboral', 'Recetas'],
        demoSeeder: \Database\Seeders\SaludMentalDemoSeeder::class,
    ),
];
