<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CRM — §04: origen, interés, estado, agente asignado.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agente_id')->nullable()
                ->constrained('users')->nullOnDelete();
            // Se completa al convertir el lead (§05: LeadConvertido).
            $table->foreignId('cliente_id')->nullable()
                ->constrained('clientes')->nullOnDelete();
            $table->foreignId('propiedad_id')->nullable()
                ->constrained('propiedades')->nullOnDelete();

            $table->string('nombre');
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();

            $table->enum('origen', ['marketplace', 'formulario', 'whatsapp', 'referido', 'otro'])
                ->default('otro');
            $table->enum('estado', ['nuevo', 'contactado', 'calificado', 'convertido', 'perdido'])
                ->default('nuevo');
            // tipo/zona/presupuesto — se completa según lo que el lead
            // muestre interés, sin ameritar columnas propias todavía.
            $table->json('interes')->nullable();
            $table->text('notas')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
