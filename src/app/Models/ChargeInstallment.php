<?php

namespace App\Models;

use App\Enums\ChargeInstallmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Una cuota del plan de pago sugerido de un Charge (ver Charge::generateInstallments()).
 * Se marca pagada seleccionándola al registrar un ChargePayment — es un
 * checklist informativo aparte del saldo real, que se sigue calculando
 * siempre desde ChargePayment (ver Charge::balance()).
 */
class ChargeInstallment extends Model
{
    protected $fillable = [
        'charge_id',
        'number',
        'amount',
        'status',
        'paid_at',
        'charge_payment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => ChargeInstallmentStatus::class,
        'paid_at' => 'date',
    ];

    public function charge(): BelongsTo
    {
        return $this->belongsTo(Charge::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(ChargePayment::class, 'charge_payment_id');
    }
}
