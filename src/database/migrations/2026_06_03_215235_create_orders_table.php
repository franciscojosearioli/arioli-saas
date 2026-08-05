<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique(); // ID público para MercadoPago
            $table->foreignId('plan_id')->constrained('plans');
            $table->string('tenant_id')->nullable(); // Se llena después del pago
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_company'); // Nombre del negocio/subdominio
            $table->decimal('amount', 12, 2);
            $table->string('currency')->default('ARS');
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->string('mp_preference_id')->nullable(); // ID de preferencia MP
            $table->string('mp_payment_id')->nullable(); // ID de pago MP
            $table->string('mp_status')->nullable(); // Estado de MP
            $table->json('mp_data')->nullable(); // Datos completos de MP
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('uuid');
            $table->index('status');
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};