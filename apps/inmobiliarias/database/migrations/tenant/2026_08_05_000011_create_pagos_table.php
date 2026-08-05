<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §04/§06: registrar un pago (total o parcial) contra una Cuota. Varios
// Pago pueden cerrar una misma Cuota.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cuota_id')->constrained('cuotas')->cascadeOnDelete();
            $table->foreignId('registrado_por_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->decimal('monto', 14, 2);
            $table->date('fecha');
            $table->enum('medio_pago', ['efectivo', 'transferencia', 'cheque', 'tarjeta', 'otro']);
            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
