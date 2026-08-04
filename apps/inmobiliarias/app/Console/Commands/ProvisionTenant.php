<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ProvisionTenant extends Command
{
    protected $signature = 'tenant:provision
        {tenant_id : Slug del tenant — define el nombre de la base (prefijo inmobiliarias_) y el subdominio por defecto}
        {admin_name}
        {admin_email}
        {admin_password}
        {--domain= : Dominio completo del tenant; por defecto {tenant_id}.inmobiliarias.arioli.dev}';

    protected $description = 'Provisiona un tenant nuevo: base de datos, dominio, roles base y usuario admin';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant_id');

        if (Tenant::find($tenantId)) {
            $this->error("El tenant '{$tenantId}' ya existe.");

            return self::FAILURE;
        }

        $domain = $this->option('domain') ?: "{$tenantId}.inmobiliarias.arioli.dev";

        $this->info("Creando tenant '{$tenantId}'…");

        // Tenant::create() dispara TenantCreated → CreateDatabase → MigrateDatabase
        // (síncrono, ver TenancyServiceProvider) — la base ya existe y está
        // migrada cuando esta línea termina.
        $tenant = Tenant::create(['id' => $tenantId]);
        $tenant->domains()->create(['domain' => $domain]);

        $this->info("Base de datos creada y migrada: inmobiliarias_{$tenantId}");

        tenancy()->initialize($tenant);

        try {
            (new RolesAndPermissionsSeeder)->run();

            $admin = User::create([
                'name' => $this->argument('admin_name'),
                'email' => $this->argument('admin_email'),
                'password' => Hash::make($this->argument('admin_password')),
                'email_verified_at' => now(),
            ]);
            $admin->assignRole('admin');

            $this->info("Usuario admin creado: {$admin->email}");
        } finally {
            tenancy()->end();
        }

        $this->info("Tenant '{$tenantId}' listo en https://{$domain}");

        return self::SUCCESS;
    }
}
