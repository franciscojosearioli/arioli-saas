<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §08: "vista especial de Desarrollo: mapa interactivo con todas las
// unidades/lotes coloreadas por estado — mismo polígono general +
// colección de unidades hijas" que loteos, más "Desarrollos a su cargo"
// en el perfil de Constructora. Antes de esta migración solo Propiedad se
// sincronizaba (con el nombre del desarrollo como texto suelto) — esta es
// la entidad real que ambas vistas necesitaban. Mismo patrón denormalizado
// que fichas_propiedad: tenant_id+desarrollo_id es el único vínculo con el
// tenant de origen, sin FK real a su base.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desarrollos', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('desarrollo_id');
            $table->unsignedBigInteger('constructora_id')->nullable();
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->enum('tipo', ['loteo', 'barrio_cerrado', 'edificio', 'emprendimiento']);
            $table->text('descripcion')->nullable();
            $table->string('provincia')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('barrio')->nullable();
            // Polígono general — igual patrón que loteos, ver §04/§08.
            $table->geometry('ubicacion')->nullable();
            $table->string('plano_maestro')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'desarrollo_id']);
            $table->index(['tenant_id', 'constructora_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desarrollos');
    }
};
