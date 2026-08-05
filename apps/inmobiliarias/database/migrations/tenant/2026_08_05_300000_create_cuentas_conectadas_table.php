<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Fase 4 (§09, §04): "Cada tenant conecta sus propias cuentas oficiales
// desde Configuración... El agregado CuentaConectada guarda access/
// refresh token (cifrados), los scopes efectivamente otorgados y la
// fecha de expiración; nunca las credenciales de login del tenant, solo
// el token que la plataforma emite." access_token va cifrado con el cast
// 'encrypted' de Laravel (AES-256 con APP_KEY), no un campo aparte.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuentas_conectadas', function (Blueprint $table) {
            $table->id();
            $table->enum('canal', ['facebook', 'instagram']);

            // Page id (Facebook) / IG Business Account id (Instagram) —
            // lo que la Graph API espera al publicar.
            $table->string('external_account_id');
            $table->string('external_account_name')->nullable();

            $table->text('access_token');
            $table->json('scopes')->nullable();
            $table->timestamp('token_expira_en')->nullable();

            $table->enum('estado', ['activa', 'requiere_reconexion'])->default('activa');
            $table->text('ultimo_error')->nullable();

            $table->timestamps();

            // Un tenant conecta una sola cuenta por canal — mismo criterio
            // que "su página de Facebook, su cuenta de Instagram" (§09,
            // singular). Reconectar reemplaza, no duplica.
            $table->unique('canal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuentas_conectadas');
    }
};
