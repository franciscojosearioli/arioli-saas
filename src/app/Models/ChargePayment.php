<?php

namespace App\Models;

use App\Enums\ChargePaymentMethod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Un pago (parcial o total) aplicado contra un Charge. Ver Charge::registerPayment()
 * y Charge::markAsPaid() — este es el único lugar donde se registra plata
 * efectivamente cobrada, sin importar si el Charge se salda de una vez o de a partes.
 */
class ChargePayment extends Model
{
    protected $fillable = [
        'charge_id',
        'amount',
        'payment_method',
        'paid_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount'         => 'decimal:2',
        'payment_method' => ChargePaymentMethod::class,
        'paid_at'        => 'date',
    ];

    public function charge(): BelongsTo
    {
        return $this->belongsTo(Charge::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
