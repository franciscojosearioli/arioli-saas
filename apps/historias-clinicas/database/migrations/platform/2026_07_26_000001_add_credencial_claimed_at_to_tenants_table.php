<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa 6.5 (ver docs/ARQUITECTURA_MODULAR.md): guarda del link firmado
 * de reclamo de credenciales de un cliente real — un solo campo alcanza,
 * no hace falta una tabla de tokens (el link en sí no se persiste en
 * ningún lado; Laravel firma/valida la URL con la APP_KEY). `null` =
 * todavía no reclamado; con fecha = ya se usó, el link no vuelve a
 * funcionar aunque la firma siga siendo válida.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('credencial_claimed_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('credencial_claimed_at');
        });
    }
};
