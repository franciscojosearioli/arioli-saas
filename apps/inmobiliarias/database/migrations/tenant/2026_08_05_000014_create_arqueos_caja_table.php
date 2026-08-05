<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §06/§17 Rev. 1.2: "cerrar caja del día" — solo arqueo diario, sin
// conciliación bancaria todavía. `monto_esperado` se calcula a partir de
// los Pago del día (ver ArqueoCaja::calcularEsperado); `monto_contado` es
// lo que el administrativo cuenta a mano al cerrar.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arqueos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cerrado_por_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->date('fecha')->unique();
            $table->decimal('monto_esperado', 14, 2);
            $table->decimal('monto_contado', 14, 2);
            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arqueos_caja');
    }
};
