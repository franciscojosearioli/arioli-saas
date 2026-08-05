<?php

namespace App\Console\Commands;

use App\Models\AppVersion;
use App\Models\License;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProvisionLoteosDirectly extends Command
{
    protected $signature = 'provision:loteos-direct {tenant_id} {admin_name} {admin_email} {admin_password}';
    protected $description = 'Provision Loteos tenant with COMPLETE database structure - all tables, foreign keys, constraints';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant_id');
        $adminName = $this->argument('admin_name');
        $adminEmail = $this->argument('admin_email');
        $adminPassword = $this->argument('admin_password');

        $this->info("🚀 Provisionando tenant Loteos: {$tenantId}");

        try {
            $result = $this->provisionLoteosDirect($tenantId, $adminName, $adminEmail, $adminPassword);

            if ($result['success']) {
                $this->info("✅ Loteos provisionado exitosamente!");
                $this->info("📋 Base de datos: {$result['database']}");
                $this->info("👤 Usuario admin: {$adminEmail}");
                $this->info("🔧 Setup wizard listo");
                return 0;
            } else {
                $this->error("❌ Error: {$result['error']}");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error inesperado: " . $e->getMessage());
            return 1;
        }
    }

    private function provisionLoteosDirect(string $tenantId, string $adminName, string $adminEmail, string $adminPassword): array
    {
        $dbName = 'loteos_' . $tenantId;

        // Resolve latest registered version BEFORE switching databases
        $latestVersion = AppVersion::latestFor('loteos')?->version ?? '1.0.0';

        try {
            // 1. Crear la base de datos
            $this->info("🗄️  Creando base de datos: {$dbName}");

            DB::statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // 2. Crear estructura de tablas COMPLETA y ROBUSTA
            $this->info("📋 Creando estructura completa de tablas...");

            DB::statement("USE `{$dbName}`");

            // ==================== TABLAS CORE DEL SISTEMA ====================

            // Tabla users (estructura COMPLETA con todos los campos)
            DB::statement("
                CREATE TABLE users (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    email_verified_at TIMESTAMP NULL DEFAULT NULL,
                    password VARCHAR(255) NOT NULL,
                    remember_token VARCHAR(100) NULL,
                    role ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
                    acceso_permitido TINYINT(1) NOT NULL DEFAULT 1,
                    dni VARCHAR(255) NULL,
                    telefono VARCHAR(255) NULL,
                    direccion VARCHAR(255) NULL,
                    provincia VARCHAR(255) NULL,
                    localidad VARCHAR(255) NULL,
                    pais VARCHAR(255) NULL,
                    interes_lotes JSON NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    deleted_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Tabla configuraciones (estructura COMPLETA con wizard + WhatsApp)
            DB::statement("
                CREATE TABLE configuraciones (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    nombre_aplicacion VARCHAR(255) NULL,
                    logo_principal VARCHAR(255) NULL,
                    logo_admin VARCHAR(255) NULL,
                    galeria JSON NULL,
                    descripcion LONGTEXT NULL,
                    whatsapp_numero VARCHAR(255) NULL,
                    envio_credenciales TINYINT(1) DEFAULT 0,
                    envio_avisos_pagos TINYINT(1) DEFAULT 0,
                    firma_imagen VARCHAR(255) NULL,
                    map_lat DECIMAL(10, 7) DEFAULT NULL,
                    map_lng DECIMAL(10, 7) DEFAULT NULL,
                    map_zoom TINYINT DEFAULT 14,
                    setup_completed TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== SISTEMA DE AUTENTICACIÓN ====================

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

            // ==================== NEGOCIO PRINCIPAL - LOTES ====================

            // Tabla lotes (COMPLETA con geometry, foreign keys)
            DB::statement("
                CREATE TABLE lotes (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    numero_lote VARCHAR(255) NOT NULL COMMENT 'Identificador único del lote',
                    estado ENUM('disponible', 'vendido', 'reservado') DEFAULT 'disponible' COMMENT 'Estado actual del lote',
                    user_id BIGINT UNSIGNED NULL COMMENT 'Cliente asignado al lote',
                    precio DECIMAL(50, 2) NOT NULL COMMENT 'Precio total del lote',
                    coordenadas GEOMETRY NULL COMMENT 'Coordenadas geográficas del lote (polígono)',
                    manzana VARCHAR(255) NULL,
                    dimensiones VARCHAR(255) NULL,
                    superficie DECIMAL(10, 2) NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    UNIQUE KEY lotes_numero_lote_manzana_unique (numero_lote, manzana),
                    INDEX lotes_user_id_index (user_id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Tabla cuotas (COMPLETA con foreign keys y constraints)
            DB::statement("
                CREATE TABLE cuotas (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    lote_id BIGINT UNSIGNED NOT NULL COMMENT 'Lote asociado a la cuota',
                    user_id BIGINT UNSIGNED NOT NULL COMMENT 'Cliente que debe pagar la cuota',
                    numero_cuota INT NOT NULL COMMENT 'Número de la cuota (ej. 1, 2, 3...)',
                    monto DECIMAL(50, 2) NOT NULL COMMENT 'Monto de la cuota',
                    fecha_vencimiento DATE NOT NULL COMMENT 'Fecha de vencimiento de la cuota',
                    estado ENUM('pendiente', 'pagada', 'vencida') DEFAULT 'pendiente' COMMENT 'Estado de la cuota',
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    INDEX cuotas_lote_id_index (lote_id),
                    INDEX cuotas_user_id_index (user_id),
                    FOREIGN KEY (lote_id) REFERENCES lotes(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Tabla pagos (COMPLETA con foreign keys)
            DB::statement("
                CREATE TABLE pagos (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    cuota_id BIGINT UNSIGNED NOT NULL COMMENT 'Cuota asociada al pago',
                    monto_pagado DECIMAL(50, 2) NOT NULL COMMENT 'Monto pagado',
                    fecha_pago DATE NOT NULL COMMENT 'Fecha en que se realizó el pago',
                    metodo_pago VARCHAR(255) NULL COMMENT 'Método de pago (ej. efectivo, transferencia)',
                    notas TEXT NULL COMMENT 'Notas adicionales sobre el pago',
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    INDEX pagos_cuota_id_index (cuota_id),
                    FOREIGN KEY (cuota_id) REFERENCES cuotas(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== SISTEMA DE NOTIFICACIONES ====================

            // Tabla notificaciones (COMPLETA con campos de envío)
            DB::statement("
                CREATE TABLE notificaciones (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id BIGINT UNSIGNED NOT NULL COMMENT 'Usuario destinatario de la notificación',
                    titulo VARCHAR(255) NOT NULL COMMENT 'Título de la notificación',
                    mensaje TEXT NOT NULL COMMENT 'Contenido de la notificación',
                    enviada_por_email TINYINT(1) DEFAULT 0,
                    enviada_por_sms TINYINT(1) DEFAULT 0,
                    enviada_por_whatsapp TINYINT(1) DEFAULT 0,
                    leida TINYINT(1) DEFAULT 0 COMMENT 'Indica si la notificación fue leída',
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    INDEX notificaciones_user_id_index (user_id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Tabla admin_notificaciones (notificaciones internas)
            DB::statement("
                CREATE TABLE admin_notificaciones (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    tipo VARCHAR(255) DEFAULT 'general' COMMENT 'pago_registrado, cliente_creado, cuota_vencida, general',
                    titulo VARCHAR(255) NOT NULL,
                    mensaje TEXT NOT NULL,
                    leida TINYINT(1) DEFAULT 0,
                    referencia_id BIGINT UNSIGNED NULL,
                    referencia_tipo VARCHAR(255) NULL,
                    url VARCHAR(255) NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== INTEGRACIÓN WHATSAPP ====================

            DB::statement("
                CREATE TABLE whats_app_interactions (
                    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    user_id BIGINT UNSIGNED NULL,
                    telefono VARCHAR(255) NOT NULL COMMENT 'Número del cliente en formato E.164',
                    mensaje_entrante TEXT NULL,
                    mensaje_saliente TEXT NULL,
                    fecha_mensaje TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    estado_conversacion VARCHAR(255) NULL,
                    created_at TIMESTAMP NULL DEFAULT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL,
                    INDEX whats_app_interactions_user_id_index (user_id),
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // ==================== SISTEMA DE COLAS Y CACHE ====================

            // Sistema de Jobs (Laravel Queue)
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

            // Sistema de Cache
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

            // 3. Crear usuario administrador
            $this->info("👤 Creando usuario administrador...");

            DB::statement("USE `{$dbName}`");

            $userExists = DB::select("SELECT COUNT(*) as count FROM users WHERE email = ?", [$adminEmail])[0]->count > 0;

            if (!$userExists) {
                DB::statement("
                    INSERT INTO users (name, email, password, role, acceso_permitido, created_at, updated_at)
                    VALUES (?, ?, ?, 'admin', 1, NOW(), NOW())
                ", [$adminName, $adminEmail, Hash::make($adminPassword)]);
            }

            // 4. Insertar configuración inicial (setup wizard activado)
            $this->info("⚙️  Configurando sistema inicial...");

            $configExists = DB::select("SELECT COUNT(*) as count FROM configuraciones")[0]->count > 0;

            if (!$configExists) {
                DB::statement("
                    INSERT INTO configuraciones (
                        nombre_aplicacion,
                        setup_completed,
                        map_lat,
                        map_lng,
                        map_zoom,
                        envio_credenciales,
                        envio_avisos_pagos,
                        created_at,
                        updated_at
                    ) VALUES (
                        'Sistema Loteos',
                        0,
                        -28.8808297,
                        -62.268254,
                        14,
                        0,
                        0,
                        NOW(),
                        NOW()
                    )
                ");
            }

            // ==================== METADATA DE VERSIÓN ====================

            DB::statement("
                CREATE TABLE IF NOT EXISTS app_meta (
                    `key` VARCHAR(100) NOT NULL PRIMARY KEY,
                    value TEXT NOT NULL,
                    updated_at TIMESTAMP NULL DEFAULT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            DB::statement("
                INSERT INTO app_meta (`key`, value, updated_at) VALUES ('version', ?, NOW())
                ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()
            ", [$latestVersion]);

            // Volver a la BD central
            DB::statement("USE " . config('database.connections.mysql.database'));

            // Crear o actualizar License record en saas_central
            // sellable(): nunca el plan "Demo" (price=0 siempre gana un orderBy('price') a secas)
            $plan = \App\Models\Plan::sellable()
                ->whereHas('product', fn($q) => $q->where('slug', 'loteos'))
                ->orderBy('price')->first();

            if ($plan) {
                $existing = License::where('tenant_id', $tenantId)
                    ->whereHas('plan.product', fn($q) => $q->where('slug', 'loteos'))
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
                'success'   => true,
                'tenant_id' => $tenantId,
                'database'  => $dbName,
            ];

        } catch (\Throwable $e) {
            // Volver a la BD central en caso de error
            DB::statement("USE " . config('database.connections.mysql.database'));

            return [
                'success' => false,
                'error'   => $e->getMessage(),
            ];
        }
    }
}