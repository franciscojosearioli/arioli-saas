<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa 6.6.5: estado por cara — la brecha real del modelo v1 (un molar
 * con caries oclusal pero sano en el resto no se podía representar, era
 * una sola columna `estado` por diente entero). Qué caras aplican a cada
 * pieza vive en config/platform/piezas_dentales_catalogo.php, no acá.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('superficies_odontologicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pieza_odontologica_id')->constrained('piezas_odontologicas')->cascadeOnDelete();
            $table->string('superficie', 20); // oclusal|incisal|vestibular|palatina_lingual|mesial|distal
            $table->string('estado', 30)->default('sana');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['pieza_odontologica_id', 'superficie'], 'superficies_odo_pieza_superficie_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('superficies_odontologicas');
    }
};
