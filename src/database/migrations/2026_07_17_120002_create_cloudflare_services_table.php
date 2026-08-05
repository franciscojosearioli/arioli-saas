<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cloudflare_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('Cloudflare');
            $table->string('managed_by')->default('Arioli.dev');
            $table->string('status')->default('activo'); // activo|suspendido
            $table->date('expires_at')->nullable(); // null en plan gratuito
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
        Schema::dropIfExists('cloudflare_services');
    }
};
