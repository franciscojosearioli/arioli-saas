<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Soporte para el mantenimiento mensual con backup automático de HestiaCP:
 * pedir confirmación el día 1, correr v-backup-user recién cuando el cliente
 * confirma, y cobrar solo después de que el backup termina bien — todo esto
 * vive en el mismo ClientService en vez de una tabla nueva, porque es un
 * único registro persistente cuyo ciclo se relee contra "es este mes" (mismo
 * criterio que ya usa GenerateMonthlyServiceCharges), no necesita historial
 * de todos los meses.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_services', function (Blueprint $table) {
            $table->boolean('auto_maintenance_hestia')->default(false)->after('project_id');
            $table->timestamp('maintenance_requested_at')->nullable()->after('auto_maintenance_hestia');
            $table->timestamp('maintenance_confirmed_at')->nullable()->after('maintenance_requested_at');
            $table->string('last_backup_status')->nullable()->after('maintenance_confirmed_at');
            $table->timestamp('last_backup_at')->nullable()->after('last_backup_status');
            $table->string('last_backup_path')->nullable()->after('last_backup_at');
        });
    }

    public function down(): void
    {
        Schema::table('client_services', function (Blueprint $table) {
            $table->dropColumn([
                'auto_maintenance_hestia',
                'maintenance_requested_at',
                'maintenance_confirmed_at',
                'last_backup_status',
                'last_backup_at',
                'last_backup_path',
            ]);
        });
    }
};
