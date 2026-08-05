<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// §08: "perfil de constructora — una página por Constructora, con los
// Desarrollos a su cargo". Una Constructora vive dentro de un tenant
// (§04), así que tenant_id+constructora_id es lo que la identifica acá.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfiles_constructora', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->unsignedBigInteger('constructora_id');
            $table->string('slug')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('logo_url')->nullable();
            $table->timestamps();

            $table->unique(['tenant_id', 'constructora_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfiles_constructora');
    }
};
