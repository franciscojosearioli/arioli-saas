<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa 6.6.5: retira el modelo v1 (estado por pieza entera, sin
 * superficies, sin historial, sin tratamientos) — reemplazado por
 * piezas_odontologicas + superficies_odontologicas. Decisión explícita de
 * Francisco: descartar los datos existentes (todos demo/test, sin cliente
 * real) en vez de migrarlos — confirmado antes de este paso que ningún
 * tenant real dependía de esta tabla (ver docs/ARQUITECTURA_MODULAR.md).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('piezas_dentales');
    }

    public function down(): void
    {
        Schema::create('piezas_dentales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odontograma_id')->constrained('odontogramas')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero');
            $table->string('estado', 30)->default('sana');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['odontograma_id', 'numero']);
        });
    }
};
