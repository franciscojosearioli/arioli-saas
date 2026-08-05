<?php

/**
 * Etapa 6.6.1 (ver docs/ARQUITECTURA_MODULAR.md): subdominios de
 * infraestructura de la plataforma — nunca disponibles como `tenant_key`
 * ni `slug` de un tenant real ni de una demo. Formaliza como regla de
 * plataforma lo que antes era una reserva implícita (ej. el propio
 * subdominio `demo`, tratado por error como "disponible para contratar").
 *
 * Punto único de verdad para historias-clinicas: `TenantsCrear`,
 * `Internal\ProvisionController` (checkout de cliente real) e
 * `IdentifyTenant` (mensaje mostrado cuando el subdominio no resuelve a
 * ningún tenant) consultan esta misma lista. La app central
 * (`/opt/arioli-saas/src`, no comparte código con este repo) mantiene su
 * propia copia en `CheckoutController::RESERVED_SLUGS` — debe mantenerse
 * en sincronía a mano con esta lista.
 */
return [
    'demo',
    'admin',
    'api',
    'app',
    'www',
    'mail',
    'ftp',
    'cliente',
    'test',
    'ejemplo',
    'staging',
    'panel',
    'soporte',
    'support',
];
