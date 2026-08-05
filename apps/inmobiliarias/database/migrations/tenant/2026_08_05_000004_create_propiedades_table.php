<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Catálogo — §04: tipo + atributos comunes indexables como columnas,
// atributos flexibles por tipo en JSON (evita una tabla de 40 columnas
// nulas). `estado` acá es un campo simple — pasa a derivarse de Operación
// recién en Fase 2, cuando ese agregado exista.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propiedades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('desarrollo_id')->nullable()
                ->constrained('desarrollos')->nullOnDelete();
            $table->foreignId('propietario_id')->nullable()
                ->constrained('clientes')->nullOnDelete();

            $table->enum('tipo', [
                'loteo', 'casa', 'departamento', 'local', 'oficina', 'galpon', 'campo', 'cochera',
            ]);
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('estado', ['disponible', 'reservado', 'vendido', 'alquilado'])
                ->default('disponible');

            $table->decimal('precio', 14, 2)->nullable();
            $table->enum('moneda', ['ARS', 'USD'])->default('ARS');

            $table->decimal('superficie_cubierta', 10, 2)->nullable();
            $table->decimal('superficie_total', 10, 2)->nullable();
            $table->unsignedTinyInteger('ambientes')->nullable();
            $table->unsignedTinyInteger('dormitorios')->nullable();
            $table->unsignedTinyInteger('banos')->nullable();
            $table->unsignedTinyInteger('cocheras')->nullable();

            // Propios de loteos/emprendimientos con manzanas.
            $table->string('manzana')->nullable();
            $table->string('numero_lote')->nullable();

            $table->string('direccion')->nullable();
            $table->string('provincia')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('barrio')->nullable();
            // Point o polygon según tipo — mismo patrón que loteos (§04).
            $table->geometry('ubicacion')->nullable();

            $table->json('servicios')->nullable();
            $table->json('caracteristicas_destacadas')->nullable();
            // Atributos flexibles por tipo (ej. aptitud/hectáreas de un
            // campo) que no ameritan columna propia todavía.
            $table->json('atributos')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['desarrollo_id', 'manzana', 'numero_lote']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propiedades');
    }
};
