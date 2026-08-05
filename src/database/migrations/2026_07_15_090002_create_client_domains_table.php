<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Nombre de tabla "client_domains" (no "domains"): la tabla "domains" ya existe y
     * pertenece a Stancl/Tenancy (ruteo de subdominios de tenants) — es un concepto
     * completamente distinto al dominio administrado de un cliente del CRM.
     */
    public function up(): void
    {
        Schema::create('client_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('domain_name');
            $table->boolean('is_primary')->default(false);
            $table->string('status')->default('activo'); // activo|suspendido|transferencia|pendiente|expirado
            $table->string('registrar')->nullable();
            $table->string('dns_provider')->nullable();
            $table->string('account_holder')->nullable();
            $table->string('account_email')->nullable();
            $table->date('registered_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->string('renewal_payer')->default('cliente'); // cliente|arioli|tercero
            $table->decimal('renewal_cost', 12, 2)->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_domains');
    }
};
