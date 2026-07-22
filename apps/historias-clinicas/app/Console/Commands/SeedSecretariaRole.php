<?php

namespace App\Console\Commands;

use Database\Seeders\SecretariaRoleSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedSecretariaRole extends Command
{
    protected $signature = 'secretaria:seed {--db= : Base de datos específica (ej: historias_demo)}';
    protected $description = 'Crea el rol Secretaria con sus permisos en uno o todos los tenants';

    public function handle(): void
    {
        $targetDb = $this->option('db');

        if ($targetDb) {
            $this->seedForDatabase($targetDb);
        } else {
            // Buscar todas las DBs que parecen ser tenants del sistema
            $databases = DB::select("SHOW DATABASES LIKE 'historias%'");
            foreach ($databases as $row) {
                $dbName = current((array) $row);
                $this->seedForDatabase($dbName);
            }
        }
    }

    private function seedForDatabase(string $dbName): void
    {
        $this->line("Seeding <info>{$dbName}</info>...");

        try {
            DB::purge('mysql');
            config(['database.connections.mysql.database' => $dbName]);
            DB::reconnect('mysql');

            // Verificar que las tablas existen
            $tables = DB::select("SHOW TABLES LIKE 'permissions'");
            if (empty($tables)) {
                $this->warn("  → Saltando {$dbName}: sin tabla permissions");
                return;
            }

            (new SecretariaRoleSeeder())->run();
            $this->info("  ✓ Secretaria creada en {$dbName}");
        } catch (\Throwable $e) {
            $this->error("  ✗ Error en {$dbName}: " . $e->getMessage());
        }
    }
}
