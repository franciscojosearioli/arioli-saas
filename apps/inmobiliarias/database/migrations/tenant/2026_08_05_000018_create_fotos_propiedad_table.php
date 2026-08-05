<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §03 (Catálogo incluye "media") / §09: la galería de una Propiedad es
// una colección ORDENADA, no una lista plana — cada imagen tiene `orden`
// y puede marcarse `es_principal` (la que usan los canales que solo
// admiten una foto de portada). Gap real de Fase 1 (Catálogo listaba
// media en su alcance, Propiedad no tenía dónde guardarla) cerrado acá
// porque Fase 3 la necesita de verdad para publicar contenido real, no
// una galería vacía.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotos_propiedad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propiedad_id')->constrained('propiedades')->cascadeOnDelete();

            $table->string('url');
            $table->unsignedInteger('orden')->default(0);
            $table->boolean('es_principal')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotos_propiedad');
    }
};
