<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credentials', function (Blueprint $table) {
            $table->id();
            $table->morphs('credentialable'); // Client, ClientDomain, Hosting, Project
            $table->string('type'); // wordpress|cpanel|cloudflare|mercadopago|google|redes_sociales|hosting|dominio|ftp|smtp|otro
            $table->string('label')->nullable();
            $table->string('username')->nullable();
            $table->text('secret'); // cifrado (Crypt::encryptString)
            $table->string('url')->nullable();
            $table->string('owner')->default('cliente'); // cliente|arioli|proveedor_externo
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credentials');
    }
};
