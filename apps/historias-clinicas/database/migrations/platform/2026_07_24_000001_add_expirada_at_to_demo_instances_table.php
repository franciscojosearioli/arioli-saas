<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa 6.3.2: el período de gracia entre 'expirada' y la limpieza
 * necesita saber cuándo pasó a 'expirada' exactamente. Se agrega un
 * timestamp explícito en vez de apoyarse en `updated_at` — ese campo
 * puede cambiar por otros motivos mientras el registro sigue 'expirada'
 * (ej. anotar algo a mano) y correr el riesgo silencioso de reiniciar el
 * conteo del período de gracia.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_instances', function (Blueprint $table) {
            $table->timestamp('expirada_at')->nullable()->after('activada_at');
        });
    }

    public function down(): void
    {
        Schema::table('demo_instances', function (Blueprint $table) {
            $table->dropColumn('expirada_at');
        });
    }
};
