<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('capability_key', 150)->nullable()->after('title');
        });

        // Backfill: permisos de módulos que ya declaran su capability en
        // Fase 0. Todo lo demás (users, roles, permissions, audit_log,
        // paciente, informe, medicacion, etc.) queda capability_key = NULL
        // -> siempre evaluable, comportamiento de Core, sin cambios.
        DB::table('permissions')
            ->whereIn('title', [
                'especialidad_access',
                'especialidad_create',
                'especialidad_edit',
                'especialidad_delete',
            ])
            ->update(['capability_key' => 'especialidades']);
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('capability_key');
        });
    }
};
