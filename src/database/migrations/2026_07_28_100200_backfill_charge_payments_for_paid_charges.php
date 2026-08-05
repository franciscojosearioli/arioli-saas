<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Consistencia histórica: todo Charge que ya estaba 'paid' antes de que
     * existiera charge_payments (pagado de una sola vez vía Mercado Pago o
     * "marcar pagado") no tiene registro de pago propio. Sin esto,
     * amountPaid()/balance() lo verían como 100% pendiente. Se inserta un
     * único ChargePayment por el monto total, fechado en paid_at (o
     * updated_at si paid_at quedó vacío por algún dato viejo).
     */
    public function up(): void
    {
        $paidCharges = DB::table('charges')
            ->where('status', 'paid')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('charge_payments')
                    ->whereColumn('charge_payments.charge_id', 'charges.id');
            })
            ->get(['id', 'amount', 'payment_method', 'paid_at', 'updated_at']);

        $now = now();

        foreach ($paidCharges as $charge) {
            DB::table('charge_payments')->insert([
                'charge_id'      => $charge->id,
                'amount'         => $charge->amount,
                'payment_method' => $charge->payment_method,
                'paid_at'        => $charge->paid_at ?? $charge->updated_at,
                'notes'          => 'Backfill automático — pago único registrado antes de que existiera el historial de pagos parciales.',
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);
        }
    }

    public function down(): void
    {
        // No revertimos el backfill: borrar estos registros haría que
        // charges 'paid' históricos vuelvan a verse como pendientes.
        // Si hay que deshacer esta migración, se hace a mano.
    }
};
