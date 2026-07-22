<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Etapa 6.3.1 (ver docs/ARQUITECTURA_MODULAR.md): tabla maestra, vive solo
 * en la DB maestra junto a `tenants` — igual que esa migración, NO corre
 * vía `tenants:migrate` (eso itera `database/migrations` contra cada
 * tenant). Se aplica a mano contra la DB maestra:
 * `php artisan migrate --path=database/migrations/platform --force`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demo_instances', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_key', 100)->unique();
            $table->string('perfil_key', 100);
            $table->enum('status', [
                'pendiente',
                'provisionando',
                'activa',
                'expirada',
                'eliminando',
                'eliminada',
                'error',
            ])->default('pendiente');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('activada_at')->nullable();
            $table->timestamp('eliminada_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_instances');
    }
};
