<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// CRM — §04: "misma persona puede ser comprador en una Operación y
// propietario/locador en otra — rol contextual por Operación, no tablas
// rígidas separadas". Una sola tabla; "propietario" es un Cliente
// referenciado como dueño de una Propiedad, no un tipo distinto.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            // Portal propietario/inquilino (§07): solo si tiene user asociado.
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->enum('tipo_persona', ['fisica', 'juridica'])->default('fisica');
            $table->string('nombre');
            $table->string('documento')->nullable();
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('direccion')->nullable();
            $table->string('provincia')->nullable();
            $table->string('ciudad')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
