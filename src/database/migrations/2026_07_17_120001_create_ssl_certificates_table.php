<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ssl_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('Cloudflare');
            $table->string('status')->default('activo'); // activo|vencido|pendiente
            $table->date('expires_at')->nullable();
            $table->boolean('auto_renew')->default(true);
            $table->string('renewal_payer')->default('arioli'); // cliente|arioli|tercero
            $table->decimal('provider_cost', 12, 2)->nullable()->default(0);
            $table->decimal('management_fee', 12, 2)->nullable();
            $table->timestamps();
            $table->index(['client_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ssl_certificates');
    }
};
