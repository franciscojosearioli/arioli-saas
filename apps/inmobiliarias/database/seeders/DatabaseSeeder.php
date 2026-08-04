<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seeder de tenant — corre por tenant, no en el landlord (users/roles
     * son per-tenant, §02/§07 del Artifact).
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);
    }
}
