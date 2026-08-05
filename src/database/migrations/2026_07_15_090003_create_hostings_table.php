<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hostings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('plan')->nullable();
            $table->string('type')->nullable(); // shared|vps|cloud|dedicated|docker
            $table->string('status')->default('activo'); // activo|suspendido|cancelado|migrando
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
        Schema::dropIfExists('hostings');
    }
};
