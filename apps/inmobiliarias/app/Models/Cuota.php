<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cuota extends Model
{
    use HasFactory;

    protected $fillable = [
        'operacion_id',
        'numero',
        'fecha_vencimiento',
        'monto',
        'moneda',
        'estado',
    ];

    protected $attributes = [
        'moneda' => 'ARS',
        'estado' => 'pendiente',
    ];

    protected function casts(): array
    {
        return [
            'fecha_vencimiento' => 'date',
            'monto' => 'decimal:2',
        ];
    }

    public function operacion(): BelongsTo
    {
        return $this->belongsTo(Operacion::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function montoPagado(): string
    {
        return (string) $this->pagos()->sum('monto');
    }

    /**
     * §06: registrar un pago total o parcial. Recalcula el estado de la
     * Cuota según lo efectivamente pagado — nunca se marca 'pagada' a mano.
     */
    public function registrarPago(array $datos): Pago
    {
        $pago = $this->pagos()->create($datos);

        $pagado = (float) $this->montoPagado();
        $this->estado = match (true) {
            $pagado >= (float) $this->monto => 'pagada',
            $pagado > 0 => 'parcial',
            default => 'pendiente',
        };
        $this->save();

        return $pago;
    }
}
