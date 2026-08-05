<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa 6.4: quién pidió la demo — permite reenviar la URL si cierra la
 * pestaña e identificar la solicitud. Nullable porque una DemoInstance
 * creada por CLI (demo:crear, uso interno/pruebas) no tiene solicitante.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('demo_instances', function (Blueprint $table) {
            $table->string('solicitante_nombre', 150)->nullable()->after('perfil_key');
            $table->string('solicitante_email', 190)->nullable()->after('solicitante_nombre');
        });
    }

    public function down(): void
    {
        Schema::table('demo_instances', function (Blueprint $table) {
            $table->dropColumn(['solicitante_nombre', 'solicitante_email']);
        });
    }
};
