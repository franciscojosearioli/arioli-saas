<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §09: un PublicacionCanal por canal activado. `canal` solo tiene los dos
// canales reales de Fase 3 (marketplace propio + sitio web) — Facebook e
// Instagram (Fase 4) se suman con su propia migración cuando ese
// ChannelAdapter exista, mismo criterio que 'permuta' en Operacion.tipo:
// no dejar un valor seleccionable sin soporte real detrás.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publicacion_canales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publicacion_id')->constrained('publicaciones')->cascadeOnDelete();

            $table->enum('canal', ['marketplace', 'sitio_web']);
            $table->enum('estado', [
                'borrador', 'programada', 'publicando', 'publicada', 'pausada', 'despublicada', 'error',
            ])->default('borrador');

            // Id que devuelve la plataforma del canal — permite actualizar
            // en vez de duplicar en la siguiente sincronización (§09).
            $table->string('external_id')->nullable();
            $table->json('contenido_override')->nullable();
            $table->dateTime('programada_para')->nullable();
            $table->dateTime('fecha_publicada')->nullable();
            $table->unsignedTinyInteger('intentos')->default(0);
            $table->text('ultimo_error')->nullable();

            $table->timestamps();

            $table->unique(['publicacion_id', 'canal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicacion_canales');
    }
};
