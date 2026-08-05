<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Etapa 6.5 (ver docs/ARQUITECTURA_MODULAR.md) — Gate G-02: transforma al
 * usuario sembrado id=1 (admin@admin.com) en el administrador real del
 * cliente (nombre/email reales, contraseña aleatoria e inutilizable hasta
 * el reclamo) y elimina a los usuarios id=2/3 (user@user.com,
 * secretaria@sistema.com) — cuentas de ejemplo sin valor para un cliente
 * real, cuya contraseña conocida quedaría como agujero de seguridad si se
 * dejaran (aunque no sean "el admin"). `RoleUserTableSeeder` asigna roles
 * por ID, no por email — por eso alcanza con actualizar el id=1 en vez de
 * crear un usuario nuevo y reasignarle el rol.
 *
 * Nunca se usa para demos: ahí los 3 usuarios sembrados se mantienen tal
 * cual, es exactamente lo que se necesita mostrar.
 *
 * Usa mysql_tenant_admin (Gate G-01) porque es parte del ciclo de vida de
 * provisioning de un tenant, igual que tenants:crear.
 */
class TenantsAsegurarAdministrador extends Command
{
    private const CONEXION = 'mysql_tenant_admin';

    protected $signature = 'tenants:asegurar-administrador
        {tenant_key : Clave interna del tenant}
        {nombre : Nombre real del administrador}
        {email : Email real del administrador}';

    protected $description = 'Transforma el usuario sembrado id=1 en el administrador real y elimina los usuarios de ejemplo (Etapa 6.5)';

    public function handle(): int
    {
        $tenantKey = $this->argument('tenant_key');
        $nombre = $this->argument('nombre');
        $email = $this->argument('email');

        $tenant = Tenant::where('tenant_key', $tenantKey)->first();

        if (! $tenant) {
            $this->error("No existe un tenant con la clave '{$tenantKey}'.");
            return self::FAILURE;
        }

        $originalDatabase = Config::get('database.connections.' . self::CONEXION . '.database');

        Config::set('database.connections.' . self::CONEXION . '.database', $tenant->database);
        DB::purge(self::CONEXION);
        DB::reconnect(self::CONEXION);

        try {
            $admin = User::on(self::CONEXION)->find(1);

            if (! $admin) {
                $this->error("El tenant '{$tenantKey}' no tiene un usuario id=1 — no se puede asegurar.");
                return self::FAILURE;
            }

            $admin->update([
                'name' => $nombre,
                'email' => $email,
                'password' => Hash::make(Str::random(40)),
            ]);

            // forceDelete, no delete: User usa SoftDeletes — un delete()
            // normal solo marca deleted_at y deja el hash de password
            // sentado en la tabla, exactamente la cuenta "fantasma" que
            // se decidió evitar (ver docs/ARQUITECTURA_MODULAR.md, 6.5).
            User::on(self::CONEXION)->whereIn('id', [2, 3])->forceDelete();

            $this->info("Tenant '{$tenantKey}': administrador asegurado ({$email}), usuarios de ejemplo eliminados.");
        } finally {
            Config::set('database.connections.' . self::CONEXION . '.database', $originalDatabase);
            DB::purge(self::CONEXION);
            DB::reconnect(self::CONEXION);
        }

        return self::SUCCESS;
    }
}
