<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §04: Cuota cuelga de Operación, no de Propiedad — soporta pago parcial
// (igual que hoy en loteos), acá vía múltiples Pago por Cuota.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operacion_id')->constrained('operaciones')->cascadeOnDelete();

            $table->unsignedInteger('numero');
            $table->date('fecha_vencimiento');
            $table->decimal('monto', 14, 2);
            $table->enum('moneda', ['ARS', 'USD'])->default('ARS');
            $table->enum('estado', ['pendiente', 'parcial', 'pagada', 'vencida'])->default('pendiente');

            $table->timestamps();

            $table->unique(['operacion_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas');
    }
};
