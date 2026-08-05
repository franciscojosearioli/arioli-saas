<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa 6.6.5: separa "qué tiene la pieza ahora" (estado, en
 * piezas_odontologicas/superficies_odontologicas) de "qué se le hizo o se
 * le va a hacer" (esto). `superficie` nullable a propósito — algunos
 * tratamientos aplican a una cara (obturación) y otros a la pieza entera
 * (extracción, corona).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tratamientos_odontologicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('odontograma_id')->constrained('odontogramas')->cascadeOnDelete();
            $table->unsignedTinyInteger('numero_fdi');
            $table->string('superficie', 20)->nullable();
            $table->string('tipo_tratamiento', 40); // obturacion|extraccion|endodoncia|corona|sellado|limpieza|implante|otro
            $table->string('estado_tratamiento', 20)->default('pendiente'); // pendiente|realizado|cancelado
            $table->date('fecha_planificada')->nullable();
            $table->date('fecha_realizada')->nullable();
            $table->foreignId('profesional_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('material', 60)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['paciente_id', 'numero_fdi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tratamientos_odontologicos');
    }
};
