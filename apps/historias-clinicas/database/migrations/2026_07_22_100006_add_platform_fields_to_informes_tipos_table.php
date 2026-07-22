<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('informes_tipos', function (Blueprint $table) {
            $table->string('modulo_key', 100)->default('historia_clinica')->after('name');
            $table->string('categoria', 100)->nullable()->after('modulo_key');
            $table->boolean('firma_requerida')->default(true)->after('categoria');
            $table->json('roles_firmantes')->nullable()->after('firma_requerida');
            $table->boolean('visible_portal')->default(false)->after('roles_firmantes');
            $table->string('permiso_codigo', 150)->nullable()->after('visible_portal');
            $table->boolean('activo')->default(true)->after('permiso_codigo');
            $table->integer('orden')->nullable()->after('activo');
        });

        // Backfill: los 5 tipos existentes se linkean a su permiso de acceso
        // ya existente (informe_psicologico_access, etc.) — no se duplica
        // ningún permiso.
        $mapa = [
            'Informe Psicológico'             => 'informe_psicologico_access',
            'Informe Psiquiátrico'            => 'informe_psiquiatrico_access',
            'Informe Clínico General'         => 'informe_clinico_access',
            'Informe de Operador Terapéutico' => 'informe_operador_access',
            'Informe Judicial'                => 'informe_judicial_access',
        ];

        foreach ($mapa as $nombre => $permisoCodigo) {
            DB::table('informes_tipos')
                ->where('name', $nombre)
                ->update([
                    'modulo_key' => 'historia_clinica',
                    'permiso_codigo' => $permisoCodigo,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('informes_tipos', function (Blueprint $table) {
            $table->dropColumn([
                'modulo_key',
                'categoria',
                'firma_requerida',
                'roles_firmantes',
                'visible_portal',
                'permiso_codigo',
                'activo',
                'orden',
            ]);
        });
    }
};
