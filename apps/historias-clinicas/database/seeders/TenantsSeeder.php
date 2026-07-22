<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Bootstrap único: puebla la tabla `tenants` leyendo information_schema una
 * sola vez. Se corre una única vez, contra la DB maestra (historias_default,
 * ver docs/ARQUITECTURA_MODULAR.md sección 6). A partir de acá,
 * information_schema deja de consultarse en runtime — la fuente de verdad
 * pasa a ser esta tabla.
 */
class TenantsSeeder extends Seeder
{
    public function run(): void
    {
        $schemas = DB::select(
            "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME LIKE 'historias\\_%'"
        );

        foreach ($schemas as $schema) {
            $database = $schema->SCHEMA_NAME;
            $tenantKey = preg_replace('/^historias_/', '', $database);

            DB::table('tenants')->updateOrInsert(
                ['tenant_key' => $tenantKey],
                [
                    'database' => $database,
                    'status' => 'activo',
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
