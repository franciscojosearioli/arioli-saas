<?php

namespace App\Platform\Contracts\Services;

use App\Models\Tenant;

/**
 * Etapa 6.5 (ver docs/ARQUITECTURA_MODULAR.md): caso de uso "entregar un
 * sistema operativo a un cliente real" — deliberadamente NO comparte
 * clase con ProvisionDemoService aunque reutilice el mismo motor
 * (tenants:crear). Son dos casos de uso de negocio distintos (mostrar el
 * producto vs. entregarlo), no una sola abstracción forzada.
 */
interface ProvisionClienteServiceContract
{
    /**
     * Provisiona un tenant real y deja al cliente a un clic de entrar:
     * base + migraciones + Perfil (tenants:crear), administrador real
     * asegurado (tenants:asegurar-administrador), y un email de
     * bienvenida con el link firmado para que el cliente defina su
     * propia contraseña. Nunca lanza excepción por una falla de
     * provisioning — el `Tenant` devuelto refleja su `status` real
     * ('activo' o 'error'), a criterio del llamador.
     */
    public function provisionar(string $tenantKey, string $perfilKey, string $adminNombre, string $adminEmail): Tenant;
}
