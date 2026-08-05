<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §09: "Sitio web de la inmobiliaria — autenticación: API key propia del
// tenant". Sin URL/API key configuradas, SitioWebAdapter simplemente no
// tiene a dónde publicar — mismo criterio de "no inventar un valor" que
// comision_porcentaje nullable.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->string('sitio_web_url')->nullable()->after('comision_porcentaje');
            $table->string('sitio_web_api_key')->nullable()->after('sitio_web_url');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion', function (Blueprint $table) {
            $table->dropColumn(['sitio_web_url', 'sitio_web_api_key']);
        });
    }
};
