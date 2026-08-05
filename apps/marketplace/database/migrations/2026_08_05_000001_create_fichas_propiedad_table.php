<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §08: base DENORMALIZADA — nunca replica cuotas, pagos ni contratos,
// solo lo publicable. `tenant_id` + `propiedad_id` identifican de dónde
// vino la ficha (para que el ChannelAdapter del tenant pueda actualizar/
// despublicar), pero esta tabla no tiene FK real a ninguna base de
// tenant — es la única forma en que el marketplace "conoce" a un tenant.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fichas_propiedad', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('propiedad_id');
            $table->string('slug')->unique();

            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 14, 2)->nullable();
            $table->string('moneda', 3)->default('ARS');
            $table->string('tipo_operacion')->nullable();
            $table->string('tipo_propiedad');
            $table->string('estado');

            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('provincia')->nullable();
            $table->string('barrio')->nullable();

            $table->decimal('superficie_cubierta', 10, 2)->nullable();
            $table->decimal('superficie_total', 10, 2)->nullable();
            $table->unsignedTinyInteger('ambientes')->nullable();
            $table->unsignedTinyInteger('dormitorios')->nullable();
            $table->unsignedTinyInteger('banos')->nullable();
            $table->unsignedTinyInteger('cocheras')->nullable();

            $table->json('servicios')->nullable();
            $table->json('caracteristicas_destacadas')->nullable();
            $table->string('nombre_desarrollo')->nullable();
            $table->json('galeria')->nullable();

            $table->boolean('destacada')->default(false);
            $table->timestamp('publicada_en')->nullable();

            $table->timestamps();

            $table->unique(['tenant_id', 'propiedad_id']);
            $table->index(['provincia', 'ciudad', 'tipo_operacion', 'tipo_propiedad']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichas_propiedad');
    }
};
