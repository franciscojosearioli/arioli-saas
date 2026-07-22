<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_sistema', function (Blueprint $table) {
            $table->id();

            // Identidad del sistema
            $table->string('nombre_sistema')->default('Historias Clínicas');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();

            // Información institucional
            $table->string('nombre_institucion')->nullable();
            $table->string('tipo_institucion')->nullable();
            $table->text('descripcion')->nullable();

            // Datos de contacto
            $table->string('direccion')->nullable();
            $table->string('localidad')->nullable();
            $table->string('provincia')->nullable()->default('Buenos Aires');
            $table->string('pais')->default('Argentina');
            $table->string('telefono')->nullable();
            $table->string('email_contacto')->nullable();
            $table->string('website')->nullable();
            $table->string('cuit')->nullable();

            // PDFs
            $table->text('pie_pdf')->nullable();

            $table->timestamps();
        });

        // Registro inicial
        DB::table('configuracion_sistema')->insert([
            'nombre_sistema'    => 'Historias Clínicas',
            'nombre_institucion'=> '',
            'provincia'         => 'Buenos Aires',
            'pais'              => 'Argentina',
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_sistema');
    }
};
