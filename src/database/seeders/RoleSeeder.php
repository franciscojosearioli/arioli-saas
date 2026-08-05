<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Admin central: acceso total
        $adminCentral = Role::firstOrCreate(['name' => 'admin_central', 'guard_name' => 'web']);
        $adminCentral->syncPermissions(Permission::all());

        // Admin del tenant
        Role::firstOrCreate(['name' => 'tenant_admin', 'guard_name' => 'web']);

        // Usuario del tenant
        Role::firstOrCreate(['name' => 'tenant_user', 'guard_name' => 'web']);
    }
}