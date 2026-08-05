<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §04: Operación decoupla Cuota/Pago de Propiedad — una misma propiedad
// atraviesa varias operaciones a lo largo de su vida (se alquila, se
// recupera, se vende), a diferencia de loteos donde Cuota.lote_id ataba
// todo a un único lote para siempre.
//
// `tipo` no incluye 'permuta' todavía: §14 la ubica en Fase 7 como
// feature avanzada con forma propia (intercambio de dos propiedades, no
// una venta con contraparte en dinero) — sumarla acá sin ese soporte real
// detrás dejaría un valor seleccionable que no hace nada.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('propiedad_id')->constrained('propiedades')->cascadeOnDelete();
            $table->foreignId('agente_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->enum('tipo', ['venta', 'alquiler', 'reserva']);
            $table->enum('estado', ['abierta', 'cerrada', 'cancelada'])->default('abierta');

            $table->date('fecha_inicio');
            $table->date('fecha_cierre')->nullable();

            // Venta/reserva: precio pactado. Alquiler: valor del canon.
            $table->decimal('monto', 14, 2)->nullable();
            $table->enum('moneda', ['ARS', 'USD'])->default('ARS');

            // Solo alquiler — §17 Rev. 1.2: carga manual del índice, sin
            // fuente externa automática todavía.
            $table->enum('indice_actualizacion', ['ICL', 'IPC'])->nullable();

            $table->text('notas')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operaciones');
    }
};
