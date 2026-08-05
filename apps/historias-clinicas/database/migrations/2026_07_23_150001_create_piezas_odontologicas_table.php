<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa 6.6.5 (ver docs/ARQUITECTURA_MODULAR.md): reemplaza a
 * `piezas_dentales` — deliberadamente no es una edición de esa tabla, es
 * un modelo nuevo. `estado_general` es para condiciones de la pieza
 * ENTERA (ausente, extraída) — el estado por cara vive en
 * `superficies_odontologicas`, no acá.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('piezas_odontologicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('odontograma_id')->constrained('odontogramas')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero_fdi');
            $table->string('estado_general', 30)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['odontograma_id', 'numero_fdi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('piezas_odontologicas');
    }
};
