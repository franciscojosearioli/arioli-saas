<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §04: Comisión cuelga de Operación, referencia al agente — dinero que la
// inmobiliaria le paga al agente, en paralelo a la Cuota que paga el
// cliente. §17 Rev. 1.2: porcentaje fijo por tenant (Configuracion), no
// un motor de reglas por operación/agente — `porcentaje` acá es una
// copia del valor de Configuracion al momento de generarse, para que un
// cambio posterior del parámetro no reescriba comisiones ya generadas.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comisiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operacion_id')->unique()
                ->constrained('operaciones')->cascadeOnDelete();
            $table->foreignId('agente_id')->constrained('users')->cascadeOnDelete();

            $table->decimal('porcentaje', 5, 2);
            $table->decimal('monto', 14, 2);
            $table->enum('moneda', ['ARS', 'USD'])->default('ARS');
            $table->enum('estado', ['pendiente', 'liquidada'])->default('pendiente');
            $table->date('fecha_liquidacion')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comisiones');
    }
};
