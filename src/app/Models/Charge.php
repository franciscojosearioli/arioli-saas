<?php

namespace App\Models;

use App\Enums\ChargeInstallmentStatus;
use App\Enums\ChargePaymentMethod;
use App\Enums\ChargeStatus;
use App\Enums\Currency;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * Cobro contra cualquier entidad `chargeable` (ClientService, ClientJob, ClientDomain,
 * Hosting, License...). Tabla `charges` (antes `service_charges`): ya no es solo un
 * cobro de servicio.
 */
class Charge extends Model
{
    protected $fillable = [
        'client_id',
        'chargeable_type',
        'chargeable_id',
        'concept',
        'amount',
        'amount_with_fee',
        'currency',
        'status',
        'due_date',
        'payment_method',
        'mp_preference_id',
        'mp_payment_id',
        'payment_url',
        'paid_at',
        'invoice_id',
        'bundled_into_charge_id',
        'installments_count',
        'installment_amount',
    ];

    protected $casts = [
        'status'              => ChargeStatus::class,
        'currency'            => Currency::class,
        'payment_method'      => ChargePaymentMethod::class,
        'amount'              => 'decimal:2',
        'amount_with_fee'     => 'decimal:2',
        'installment_amount'  => 'decimal:2',
        'due_date'            => 'date',
        'paid_at'             => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function chargeable(): MorphTo
    {
        return $this->morphTo();
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function timeEntries(): MorphMany
    {
        return $this->morphMany(TimeEntry::class, 'trackable');
    }

    /**
     * Cuando este Charge es una "orden de pago" que agrupa a otros (ver
     * PaymentOrderController), acá quedan los originales que la componen.
     */
    public function bundledItems(): HasMany
    {
        return $this->hasMany(self::class, 'bundled_into_charge_id');
    }

    public function bundledInto(): BelongsTo
    {
        return $this->belongsTo(self::class, 'bundled_into_charge_id');
    }

    /**
     * Historial de pagos (parciales o totales) aplicados a este cobro. Es
     * la única fuente de verdad para saber cuánto se cobró realmente —
     * markAsPaid() también inserta acá, ver más abajo.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(ChargePayment::class)->orderByDesc('paid_at')->orderByDesc('id');
    }

    /**
     * Cuánto se cobró hasta ahora de este Charge. Usa la colección ya
     * cargada si `payments` viene con eager load (evita N+1 en listados).
     */
    public function amountPaid(): float
    {
        $payments = $this->relationLoaded('payments') ? $this->payments : $this->payments()->get();

        return (float) $payments->sum('amount');
    }

    /**
     * Saldo pendiente de este cobro puntual (nunca negativo — un pago que
     * se pasa de monto no genera saldo a favor acá, ver validación en
     * ChargeController::storePayment()).
     */
    public function balance(): float
    {
        return round(max(0, (float) $this->amount - $this->amountPaid()), 2);
    }

    public function isFullyPaid(): bool
    {
        return $this->balance() <= 0.005;
    }

    public function hasInstallmentPlan(): bool
    {
        return (bool) $this->installments_count && (bool) $this->installment_amount;
    }

    /**
     * Las cuotas reales del plan de pago, en orden. Ver generateInstallments().
     */
    public function installments(): HasMany
    {
        return $this->hasMany(ChargeInstallment::class)->orderBy('number');
    }

    public function installmentsPaidCount(): int
    {
        $installments = $this->relationLoaded('installments') ? $this->installments : $this->installments()->get();

        return $installments->where('status', ChargeInstallmentStatus::Paid)->count();
    }

    public function installmentsRemainingCount(): int
    {
        $installments = $this->relationLoaded('installments') ? $this->installments : $this->installments()->get();

        return $installments->where('status', ChargeInstallmentStatus::Pending)->count();
    }

    /**
     * Genera las cuotas reales (1..N) a partir del plan sugerido
     * (installments_count/installment_amount) — se llama una sola vez, al
     * crear el cobro (ChargeController::store()). La última cuota absorbe
     * el redondeo para que la suma cierre exacto con el monto total.
     */
    public function generateInstallments(): void
    {
        if (! $this->hasInstallmentPlan() || $this->installments()->exists()) {
            return;
        }

        $count = $this->installments_count;
        $assigned = 0.0;

        for ($number = 1; $number <= $count; $number++) {
            $amount = $number === $count
                ? round((float) $this->amount - $assigned, 2)
                : round((float) $this->installment_amount, 2);

            $assigned += $amount;

            $this->installments()->create([
                'number' => $number,
                'amount' => $amount,
            ]);
        }
    }

    /**
     * Marca como pagadas las cuotas seleccionadas y las vincula al
     * ChargePayment que las saldó — es un checklist informativo aparte del
     * saldo real (ver storePayment(): el monto del pago no tiene por qué
     * coincidir con la suma de las cuotas elegidas).
     */
    public function markInstallmentsPaid(array $installmentIds, ChargePayment $payment): void
    {
        if (empty($installmentIds)) {
            return;
        }

        $this->installments()
            ->whereIn('id', $installmentIds)
            ->where('status', ChargeInstallmentStatus::Pending)
            ->get()
            ->each(fn (ChargeInstallment $installment) => $installment->update([
                'status'             => ChargeInstallmentStatus::Paid,
                'paid_at'            => $payment->paid_at,
                'charge_payment_id'  => $payment->id,
            ]));
    }

    /**
     * Reabre las cuotas que estaban vinculadas a este pago — se usa al
     * borrar un ChargePayment (destroyPayment()) para que el checklist de
     * cuotas quede consistente con el saldo recalculado.
     */
    public function releaseInstallmentsForPayment(ChargePayment $payment): void
    {
        $this->installments()
            ->where('charge_payment_id', $payment->id)
            ->update([
                'status'            => ChargeInstallmentStatus::Pending,
                'paid_at'           => null,
                'charge_payment_id' => null,
            ]);
    }

    /**
     * Registra un pago parcial o total contra este cobro — el camino para
     * cobros informales (efectivo/transferencia) que se van saldando de a
     * partes ("me dan 200, después 300…", ver propuesta del cliente). Si el
     * pago completa el saldo, el Charge pasa a Paid automáticamente.
     */
    public function registerPayment(
        float $amount,
        ?ChargePaymentMethod $method = null,
        ?string $notes = null,
        ?int $userId = null,
        Carbon|string|null $paidAt = null,
    ): ChargePayment {
        $payment = $this->payments()->create([
            'amount'         => $amount,
            'payment_method' => $method?->value,
            'paid_at'        => $paidAt ?? now()->toDateString(),
            'notes'          => $notes,
            'created_by'     => $userId,
        ]);

        $this->unsetRelation('payments');

        if ($this->isFullyPaid() && $this->status !== ChargeStatus::Paid) {
            $this->update([
                'status'         => ChargeStatus::Paid,
                'paid_at'        => now(),
                'payment_method' => $method?->value ?? $this->payment_method?->value,
            ]);
        }

        return $payment;
    }

    /**
     * Si este Charge agrupa a otros (bundledItems), marcarlo pagado marca
     * también en cascada a cada uno de los originales — así conservan su
     * propio estado/paid_at para el historial aunque se hayan cobrado juntos.
     */
    public function markAsPaid(?string $paymentMethod = null): void
    {
        $resolvedMethod = $paymentMethod ?? $this->payment_method?->value;

        $this->update([
            'status'         => ChargeStatus::Paid,
            'paid_at'        => now(),
            'payment_method' => $resolvedMethod,
        ]);

        // Completa el saldo restante con un ChargePayment (no solo cuando no
        // hay pagos previos: si ya tenía pagos parciales registrados, esto
        // "marcar pagado" fuerza el cierre cubriendo lo que faltaba, para que
        // amountPaid()/balance() queden consistentes con el status Paid).
        $remaining = $this->balance();
        if ($remaining > 0) {
            $payment = $this->payments()->create([
                'amount'         => $remaining,
                'payment_method' => $resolvedMethod,
                'paid_at'        => now()->toDateString(),
            ]);
            $this->unsetRelation('payments');

            $this->markInstallmentsPaid(
                $this->installments()->where('status', ChargeInstallmentStatus::Pending)->pluck('id')->all(),
                $payment,
            );
        }

        foreach ($this->bundledItems as $item) {
            if ($item->status !== ChargeStatus::Paid) {
                $item->markAsPaid($resolvedMethod);
            }
        }
    }
}
