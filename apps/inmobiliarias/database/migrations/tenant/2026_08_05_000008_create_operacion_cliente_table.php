<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §04: partes de una Operación vía tabla pivote con rol — comprador,
// vendedor, locador, locatario, garante. Puede haber varios garantes (o
// varios compradores/cónyuges) por operación; un mismo cliente no puede
// tener dos roles a la vez en la misma operación.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operacion_cliente', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operacion_id')->constrained('operaciones')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->enum('rol', ['comprador', 'vendedor', 'locador', 'locatario', 'garante']);
            $table->timestamps();

            $table->unique(['operacion_id', 'cliente_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operacion_cliente');
    }
};
