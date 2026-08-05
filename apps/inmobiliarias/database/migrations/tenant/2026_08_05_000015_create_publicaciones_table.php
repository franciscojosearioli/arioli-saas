<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §04/§08/§09: la decisión del tenant de publicar una Propiedad. Una
// Publicación por Propiedad — el contenido base es el mismo across
// canales, lo que varía por canal vive en PublicacionCanal.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propiedad_id')->unique()
                ->constrained('propiedades')->cascadeOnDelete();

            $table->boolean('destacada')->default(false);
            $table->date('destacada_hasta')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publicaciones');
    }
};
