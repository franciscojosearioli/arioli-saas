<?php

namespace App\Platform\DTO;

/**
 * Puramente descriptivo, igual que Componente — no sabe cómo se aplica.
 * "Aplicar" un Perfil es literalmente ComponenteInstaller::instalar($perfil->componentes)
 * — no hace falta una clase instaladora nueva (ver docs/ARQUITECTURA_MODULAR.md, Etapa 5.1).
 *
 * Aditivo, igual que Componente/ComponenteInstaller: perfecto para un tenant
 * nuevo (aditivo = exclusivo cuando no hay nada previo instalado). NO limpia
 * un tenant que ya tiene otros Componentes activos — para eso hace falta el
 * paso explícito de desactivación (capability_states, Etapa 4.5), no algo
 * que aplicar un Perfil resuelva solo.
 */
final class Perfil
{
    /**
     * @param string[] $componentes claves de Componente en config/platform/componentes.php
     * @param string $nombreSistema título mostrado en el sistema (login, pestaña del navegador) para
     *        un tenant provisionado con este Perfil — ver TenantsCrear::provisionarTenant().
     * @param string[] $caracteristicas funcionalidades reales que incluye — mostradas en el
     *        selector público de demo (/demo) y pensadas para reusarse en el checkout de
     *        arioli.dev. Solo lo que ya existe de verdad, no aspiracional.
     * @param ?class-string $demoSeeder seeder que siembra el Escenario Demo de este perfil
     *        (Etapa 6.2), si tiene uno. Etapa 7.1: plegado acá desde
     *        TenantsCrear::ESCENARIOS_DEMO — era una tercera lista separada,
     *        redundante con esta misma información (ver docs/CATALOGO_COMPONENTES.md 7.9).
     */
    public function __construct(
        public readonly string $key,
        public readonly string $nombre,
        public readonly string $descripcion = '',
        public readonly array $componentes = [],
        public readonly string $nombreSistema = 'Sistema de Salud',
        public readonly array $caracteristicas = [],
        public readonly ?string $demoSeeder = null,
    ) {
    }
}
