<?php

namespace App\Console\Commands;

use App\Models\AppVersion;
use App\Models\License;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProvisionTallerProDirectly extends Command
{
    protected $signature = 'provision:tallerpro-direct {tenant_id} {admin_name} {admin_email} {admin_password}';
    protected $description = 'Provision TallerPro tenant with COMPLETE database structure';

    public function handle(): int
    {
        $tenantId      = $this->argument('tenant_id');
        $adminName     = $this->argument('admin_name');
        $adminEmail    = $this->argument('admin_email');
        $adminPassword = $this->argument('admin_password');

        $this->info("Provisionando tenant TallerPro: {$tenantId}");

        try {
            $result = $this->provision($tenantId, $adminName, $adminEmail, $adminPassword);

            if ($result['success']) {
                $this->info("TallerPro provisionado exitosamente!");
                $this->info("Base de datos: {$result['database']}");
                $this->info("Usuario admin: {$adminEmail}");
                return 0;
            }

            $this->error("Error: {$result['error']}");
            return 1;

        } catch (\Exception $e) {
            $this->error("Error inesperado: " . $e->getMessage());
            return 1;
        }
    }

    private function provision(string $tenantId, string $adminName, string $adminEmail, string $adminPassword): array
    {
        $dbName = 'tallerpro_' . $tenantId;

        // Resolve latest registered version BEFORE switching databases
        $latestVersion = AppVersion::latestFor('tallerpro')?->version ?? '1.0.0';

        try {
            $this->info("Creando base de datos: {$dbName}");
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            DB::statement("USE `{$dbName}`");

            // ==================== USUARIOS Y AUTENTICACIÓN ====================

            DB::statement("
                CREATE TABLE users (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) NOT NULL UNIQUE,
                    email_verified_at TIMESTAMP NULL DEFAULT NULL,
                    password VARCHAR(255) NOT NULL,
                    role VARCHAR(20) NOT NULL DEFAULT 'viewer',
                    is_active TINYINT(1) NOT NULL DEFAULT 1,
                    remember_token VARCHAR(100) NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                CREATE TABLE password_reset_tokens (
                    email VARCHAR(255) NOT NULL PRIMARY KEY,
                    token VARCHAR(255) NOT NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                CREATE TABLE sessions (
                    id VARCHAR(255) NOT NULL PRIMARY KEY,
                    user_id BIGINT UNSIGNED NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent TEXT NULL,
                    payload LONGTEXT NOT NULL,
                    last_activity INT NOT NULL,
                    INDEX sessions_user_id_index (user_id),
                    INDEX sessions_last_activity_index (last_activity)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== CLIENTES ====================

            DB::statement("
                CREATE TABLE clients (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(150) NOT NULL,
                    business_name VARCHAR(255) NULL,
                    cuit VARCHAR(20) NULL,
                    condicion_iva VARCHAR(50) NULL,
                    phone VARCHAR(30) NULL,
                    whatsapp VARCHAR(30) NULL,
                    email VARCHAR(150) NULL,
                    address TEXT NULL,
                    city VARCHAR(100) NULL,
                    state VARCHAR(100) NULL,
                    notes TEXT NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    deleted_at TIMESTAMP NULL DEFAULT NULL,
                    INDEX idx_name (name)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== EQUIPOS ====================

            DB::statement("
                CREATE TABLE equipment (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    client_id BIGINT UNSIGNED NOT NULL,
                    name VARCHAR(150) NOT NULL,
                    brand VARCHAR(100) NULL,
                    model VARCHAR(100) NULL,
                    serial_number VARCHAR(100) NULL,
                    description TEXT NULL,
                    notes TEXT NULL,
                    last_maintenance_at DATE NULL,
                    next_maintenance_at DATE NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    deleted_at TIMESTAMP NULL DEFAULT NULL,
                    INDEX idx_serial (serial_number),
                    INDEX idx_client (client_id),
                    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== ÓRDENES DE TRABAJO ====================

            DB::statement("
                CREATE TABLE work_orders (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    order_number VARCHAR(20) NOT NULL UNIQUE,
                    client_id BIGINT UNSIGNED NOT NULL,
                    equipment_id BIGINT UNSIGNED NULL,
                    created_by BIGINT UNSIGNED NULL,
                    assigned_to BIGINT UNSIGNED NULL,
                    received_at DATETIME NOT NULL,
                    estimated_delivery_at DATE NULL,
                    delivered_at DATETIME NULL,
                    priority VARCHAR(20) NOT NULL DEFAULT 'medium',
                    status VARCHAR(30) NOT NULL DEFAULT 'pending',
                    reported_issue TEXT NOT NULL,
                    diagnosis TEXT NULL,
                    work_performed TEXT NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    deleted_at TIMESTAMP NULL DEFAULT NULL,
                    INDEX idx_status_priority (status, priority),
                    FOREIGN KEY (client_id) REFERENCES clients(id),
                    FOREIGN KEY (equipment_id) REFERENCES equipment(id),
                    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
                    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                CREATE TABLE work_order_status_logs (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    work_order_id BIGINT UNSIGNED NOT NULL,
                    status VARCHAR(30) NOT NULL,
                    changed_by BIGINT UNSIGNED NULL,
                    note TEXT NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE,
                    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                CREATE TABLE internal_notes (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    work_order_id BIGINT UNSIGNED NOT NULL,
                    user_id BIGINT UNSIGNED NOT NULL,
                    comment TEXT NOT NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== PRESUPUESTOS ====================

            DB::statement("
                CREATE TABLE estimates (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    estimate_number VARCHAR(20) NOT NULL UNIQUE,
                    client_id BIGINT UNSIGNED NOT NULL,
                    work_order_id BIGINT UNSIGNED NULL,
                    status VARCHAR(20) NOT NULL DEFAULT 'draft',
                    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
                    discount DECIMAL(12,2) NOT NULL DEFAULT 0,
                    total DECIMAL(12,2) NOT NULL DEFAULT 0,
                    valid_until DATE NULL,
                    sent_at DATETIME NULL,
                    approved_at DATETIME NULL,
                    rejected_at DATETIME NULL,
                    notes TEXT NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    deleted_at TIMESTAMP NULL DEFAULT NULL,
                    FOREIGN KEY (client_id) REFERENCES clients(id),
                    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                CREATE TABLE estimate_items (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    estimate_id BIGINT UNSIGNED NOT NULL,
                    type VARCHAR(20) NOT NULL DEFAULT 'labor',
                    description TEXT NOT NULL,
                    quantity DECIMAL(10,2) NOT NULL DEFAULT 1,
                    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
                    subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    FOREIGN KEY (estimate_id) REFERENCES estimates(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== COBROS ====================

            DB::statement("
                CREATE TABLE payments (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    work_order_id BIGINT UNSIGNED NOT NULL,
                    client_id BIGINT UNSIGNED NULL,
                    type VARCHAR(20) NOT NULL,
                    method VARCHAR(20) NOT NULL,
                    amount DECIMAL(12,2) NOT NULL,
                    paid_at DATETIME NOT NULL,
                    registered_by BIGINT UNSIGNED NULL,
                    notes TEXT NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    FOREIGN KEY (work_order_id) REFERENCES work_orders(id) ON DELETE CASCADE,
                    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
                    FOREIGN KEY (registered_by) REFERENCES users(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== MANTENIMIENTOS ====================

            DB::statement("
                CREATE TABLE maintenance_schedules (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    equipment_id BIGINT UNSIGNED NOT NULL,
                    last_maintenance_at DATE NULL,
                    frequency VARCHAR(20) NOT NULL DEFAULT 'six_months',
                    custom_frequency_days SMALLINT UNSIGNED NULL,
                    next_maintenance_at DATE NOT NULL,
                    notes TEXT NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    INDEX idx_next (next_maintenance_at),
                    FOREIGN KEY (equipment_id) REFERENCES equipment(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                CREATE TABLE reminders (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(150) NOT NULL,
                    description TEXT NULL,
                    type VARCHAR(20) NOT NULL DEFAULT 'manual',
                    status VARCHAR(20) NOT NULL DEFAULT 'pending',
                    due_at DATETIME NOT NULL,
                    assigned_to BIGINT UNSIGNED NULL,
                    remindable_type VARCHAR(255) NULL,
                    remindable_id BIGINT UNSIGNED NULL,
                    completed_at DATETIME NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    INDEX idx_status_due (status, due_at),
                    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== CONFIGURACIÓN DEL SISTEMA ====================

            DB::statement("
                CREATE TABLE system_settings (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    app_name VARCHAR(255) NULL,
                    app_logo VARCHAR(255) NULL,
                    favicon VARCHAR(255) NULL,
                    budget_company_name VARCHAR(255) NULL,
                    budget_signature VARCHAR(255) NULL,
                    budget_footer TEXT NULL,
                    setup_completed TINYINT(1) NOT NULL DEFAULT 0,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                INSERT INTO system_settings (id, app_name, created_at, updated_at)
                VALUES (1, 'TallerPro', NOW(), NOW())
            ");

            // ==================== MEDIA (Spatie Media Library) ====================

            DB::statement("
                CREATE TABLE media (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    model_type VARCHAR(255) NOT NULL,
                    model_id BIGINT UNSIGNED NOT NULL,
                    uuid CHAR(36) NULL UNIQUE,
                    collection_name VARCHAR(255) NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    file_name VARCHAR(255) NOT NULL,
                    mime_type VARCHAR(255) NULL,
                    disk VARCHAR(255) NOT NULL,
                    conversions_disk VARCHAR(255) NULL,
                    size BIGINT UNSIGNED NOT NULL,
                    manipulations JSON NOT NULL,
                    custom_properties JSON NOT NULL,
                    generated_conversions JSON NOT NULL,
                    responsive_images JSON NOT NULL,
                    order_column INT UNSIGNED NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    INDEX idx_model (model_type, model_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== SISTEMA DE COLAS Y CACHE ====================

            DB::statement("
                CREATE TABLE jobs (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    queue VARCHAR(255) NOT NULL,
                    payload LONGTEXT NOT NULL,
                    attempts TINYINT UNSIGNED NOT NULL,
                    reserved_at INT UNSIGNED NULL,
                    available_at INT UNSIGNED NOT NULL,
                    created_at INT UNSIGNED NOT NULL,
                    INDEX jobs_queue_index (queue)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                CREATE TABLE job_batches (
                    id VARCHAR(255) NOT NULL PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    total_jobs INT NOT NULL,
                    pending_jobs INT NOT NULL,
                    failed_jobs INT NOT NULL,
                    failed_job_ids LONGTEXT NOT NULL,
                    options MEDIUMTEXT NULL,
                    cancelled_at INT NULL,
                    created_at INT NOT NULL,
                    finished_at INT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                CREATE TABLE failed_jobs (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    uuid VARCHAR(255) UNIQUE NOT NULL,
                    connection TEXT NOT NULL,
                    queue TEXT NOT NULL,
                    payload LONGTEXT NOT NULL,
                    exception LONGTEXT NOT NULL,
                    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                CREATE TABLE cache (
                    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
                    `value` MEDIUMTEXT NOT NULL,
                    expiration INT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                CREATE TABLE cache_locks (
                    `key` VARCHAR(255) NOT NULL PRIMARY KEY,
                    owner VARCHAR(255) NOT NULL,
                    expiration INT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== METADATA DE VERSIÓN ====================

            DB::statement("
                CREATE TABLE app_meta (
                    `key` VARCHAR(100) NOT NULL PRIMARY KEY,
                    value TEXT NOT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                INSERT INTO app_meta (`key`, value, updated_at) VALUES ('version', ?, NOW())
            ", [$latestVersion]);

            // ==================== USUARIO ADMINISTRADOR ====================

            $this->info("Creando usuario administrador...");
            DB::statement("USE `{$dbName}`");

            $exists = DB::select("SELECT COUNT(*) as count FROM users WHERE email = ?", [$adminEmail])[0]->count > 0;

            if (!$exists) {
                DB::statement("
                    INSERT INTO users (name, email, password, role, is_active, created_at, updated_at)
                    VALUES (?, ?, ?, 'admin', 1, NOW(), NOW())
                ", [$adminName, $adminEmail, Hash::make($adminPassword)]);
            }

            // Volver a la BD central
            DB::statement("USE " . config('database.connections.mysql.database'));

            // Crear o actualizar License record en saas_central
            // sellable(): nunca el plan "Demo" (price=0 siempre gana un orderBy('price') a secas)
            $plan = \App\Models\Plan::sellable()
                ->whereHas('product', fn($q) => $q->where('slug', 'tallerpro'))
                ->orderBy('price')->first();

            if ($plan) {
                $existing = License::where('tenant_id', $tenantId)
                    ->whereHas('plan.product', fn($q) => $q->where('slug', 'tallerpro'))
                    ->first();

                if ($existing) {
                    DB::table('licenses')->where('id', $existing->id)
                        ->update(['installed_version' => $latestVersion]);
                } else {
                    License::create([
                        'tenant_id'         => $tenantId,
                        'plan_id'           => $plan->id,
                        'active'            => true,
                        'installed_version' => $latestVersion,
                        'starts_at'         => now()->toDateString(),
                        'expires_at'        => now()->addYear()->toDateString(),
                    ]);
                }
            }

            return [
                'success'  => true,
                'database' => $dbName,
            ];

        } catch (\Throwable $e) {
            DB::statement("USE " . config('database.connections.mysql.database'));
            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
}
