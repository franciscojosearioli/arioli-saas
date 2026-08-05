<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArqueoCaja extends Model
{
    use HasFactory;

    // Sin esto, Eloquent pluraliza solo la última palabra -> "arqueo_cajas".
    protected $table = 'arqueos_caja';

    protected $fillable = [
        'cerrado_por_id',
        'fecha',
        'monto_esperado',
        'monto_contado',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'monto_esperado' => 'decimal:2',
            'monto_contado' => 'decimal:2',
        ];
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por_id');
    }

    public function diferencia(): string
    {
        return bcsub((string) $this->monto_contado, (string) $this->monto_esperado, 2);
    }

    /**
     * §06: "cerrar caja del día" — lo esperado es lo efectivamente
     * cobrado en efectivo ese día, no todos los medios de pago (una
     * transferencia no forma parte del arqueo físico de caja).
     */
    public static function calcularEsperado(string $fecha): string
    {
        $total = Pago::query()
            ->where('medio_pago', 'efectivo')
            ->whereDate('fecha', $fecha)
            ->sum('monto');

        return number_format((float) $total, 2, '.', '');
    }
}
