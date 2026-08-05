<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa 6.6.5: la pieza que el modelo v1 no tenía en absoluto — evolución
 * histórica real, una fila por cambio de estado (de pieza o de
 * superficie), no snapshots completos duplicados. `entidad_tipo` +
 * `entidad_id` en vez de dos FK nullable: son mutuamente excluyentes por
 * diseño (un cambio es de una pieza entera o de una superficie, nunca
 * ambas a la vez) — this evita dos columnas donde una siempre está NULL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_odontologico', function (Blueprint $table) {
            $table->id();
            $table->string('entidad_tipo', 20); // pieza|superficie
            $table->unsignedBigInteger('entidad_id');
            $table->string('estado_anterior', 30)->nullable();
            $table->string('estado_nuevo', 30);
            $table->foreignId('profesional_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('motivo')->nullable();
            $table->timestamps();

            $table->index(['entidad_tipo', 'entidad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_odontologico');
    }
};
