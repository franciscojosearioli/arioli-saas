<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cuotas reales del plan de pago de un Charge (ej. 5 cuotas de $200).
     * Cada una se puede marcar pagada seleccionándola al registrar un pago
     * (ver ChargeController::storePayment) — independiente de que el cliente
     * también pueda pagar montos libres que no coincidan con ninguna cuota.
     */
    public function up(): void
    {
        Schema::create('charge_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('charge_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('number');
            $table->decimal('amount', 12, 2);
            $table->string('status')->default('pending');
            $table->date('paid_at')->nullable();
            $table->foreignId('charge_payment_id')->nullable()->constrained('charge_payments')->nullOnDelete();
            $table->timestamps();

            $table->unique(['charge_id', 'number']);
            $table->index('charge_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charge_installments');
    }
};
