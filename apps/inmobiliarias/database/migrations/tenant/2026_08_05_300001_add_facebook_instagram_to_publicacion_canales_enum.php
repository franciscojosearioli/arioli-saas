<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Fase 4 (§09/§14): "Facebook (página)" e "Instagram (cuenta
// profesional)" — los primeros dos canales externos reales además de
// sitio_web, ahora que existe el adapter y CuentaConectada detrás.
// Schema::change() (no DB::statement crudo) para que corra igual en
// sqlite (tests) que en MySQL — mismo criterio que las migraciones de
// enum de la Rev. 1.3.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publicacion_canales', function (Blueprint $table) {
            $table->enum('canal', ['sitio_web', 'facebook', 'instagram'])->change();
        });
    }

    public function down(): void
    {
        Schema::table('publicacion_canales', function (Blueprint $table) {
            $table->enum('canal', ['sitio_web'])->change();
        });
    }
};
