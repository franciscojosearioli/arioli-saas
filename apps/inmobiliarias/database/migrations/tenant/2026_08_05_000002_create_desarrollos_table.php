<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Catálogo — §04: agrupa lotes/unidades bajo un proyecto. El `tipo` define
// qué plantilla usa el marketplace (§08); todos comparten el mismo agregado.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desarrollos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('constructora_id')->nullable()
                ->constrained('constructoras')->nullOnDelete();
            $table->string('nombre');
            $table->enum('tipo', ['loteo', 'barrio_cerrado', 'edificio', 'emprendimiento']);
            $table->text('descripcion')->nullable();
            $table->string('provincia')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('barrio')->nullable();
            // Polígono general del desarrollo — mismo patrón que loteos
            // (ST_* de MySQL en producción; sin lectura/escritura por SQL
            // crudo todavía, así que corre igual en sqlite para tests).
            $table->geometry('ubicacion')->nullable();
            $table->string('plano_maestro')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desarrollos');
    }
};
