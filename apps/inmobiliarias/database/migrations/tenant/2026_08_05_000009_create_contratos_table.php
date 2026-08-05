<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §04: 1 contrato principal por Operación, con historial de renovaciones
// para alquileres. `renueva_contrato_id` encadena una renovación al
// contrato que extiende, en vez de una tabla de historial aparte.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operacion_id')->constrained('operaciones')->cascadeOnDelete();
            $table->foreignId('renueva_contrato_id')->nullable()
                ->constrained('contratos')->nullOnDelete();

            $table->enum('estado', ['borrador', 'firmado', 'vencido', 'renovado'])->default('borrador');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->text('clausulas')->nullable();
            $table->text('notas')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
