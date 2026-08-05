<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §03: Configuración — parámetros del tenant. Fase 2 solo necesita el
// porcentaje de comisión fijo (§17, Rev. 1.2); branding y demás parámetros
// se suman cuando el módulo que los usa (Publicaciones/Marketplace) llega.
// Tabla de una sola fila por tenant, no key-value: no hay todavía más de
// un puñado de parámetros que amerite esa flexibilidad.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            // Null = todavía no configurado. Generar una Comisión sin esto
            // definido se salta la comisión en vez de inventar un valor.
            $table->decimal('comision_porcentaje', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion');
    }
};
