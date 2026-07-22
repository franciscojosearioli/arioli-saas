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
        nombre: 'Clínica General',
        descripcion: 'Historia clínica, agenda y turnos — sin especialidades adicionales.',
        componentes: [],
    ),
    'odontologia' => new Perfil(
        key: 'odontologia',
        nombre: 'Odontología',
        descripcion: 'Consultorio odontológico.',
        componentes: ['odontologia'],
    ),
    'medicina_laboral' => new Perfil(
        key: 'medicina_laboral',
        nombre: 'Medicina Laboral',
        descripcion: 'Evaluaciones y aptos laborales.',
        componentes: ['medicina_laboral'],
    ),
    'salud_mental' => new Perfil(
        key: 'salud_mental',
        nombre: 'Salud Mental / Adicciones',
        descripcion: 'Centro de tratamiento con ficha psicosocial extendida.',
        componentes: ['salud_mental'],
    ),
];
