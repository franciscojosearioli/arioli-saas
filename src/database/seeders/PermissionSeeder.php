<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Tenants
            'tenants.view',
            'tenants.create',
            'tenants.edit',
            'tenants.delete',

            // Licenses
            'licenses.view',
            'licenses.create',
            'licenses.edit',
            'licenses.delete',

            // Plans
            'plans.view',
            'plans.create',
            'plans.edit',
            'plans.delete',

            // Users
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}