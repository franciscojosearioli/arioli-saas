<?php

namespace App\Platform\Services;

use App\Models\Tenant;
use App\Models\User;
use App\Platform\Contracts\Services\CompleteTenantProvisioningContract;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class CompleteTenantProvisioning implements CompleteTenantProvisioningContract
{
    /**
     * `tenants` vive en la DB maestra, pero este caso de uso corre dentro
     * de un request ya conectado al tenant (vía IdentifyTenant) — se usa
     * la conexión mysql_tenant_admin (Gate G-01) para llegar a la
     * maestra sin depender de que nadie haya reapuntado `mysql`. Al
     * inicio de un request normal, mysql_tenant_admin ya apunta a la
     * maestra por configuración (nadie la tocó todavía en este proceso).
     */
    private const CONEXION_MASTER = 'mysql_tenant_admin';

    public function completar(string $slug, string $password): ?User
    {
        $tenant = Tenant::on(self::CONEXION_MASTER)->where('slug', $slug)->first();

        if (! $tenant) {
            throw new RuntimeException("No existe un tenant con slug '{$slug}'.");
        }

        if ($tenant->credencial_claimed_at) {
            return null;
        }

        $admin = User::find(1);

        if (! $admin) {
            throw new RuntimeException("El tenant '{$slug}' no tiene un usuario id=1.");
        }

        $admin->update(['password' => Hash::make($password)]);

        $tenant->update(['credencial_claimed_at' => now()]);

        return $admin;
    }
}
