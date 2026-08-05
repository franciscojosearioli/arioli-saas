<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comision extends Model
{
    use HasFactory;

    // Sin esto, Eloquent pluraliza "Comision" en inglés -> "comisions".
    protected $table = 'comisiones';

    protected $fillable = [
        'operacion_id',
        'agente_id',
        'porcentaje',
        'monto',
        'moneda',
        'estado',
        'fecha_liquidacion',
    ];

    protected $attributes = [
        'moneda' => 'ARS',
        'estado' => 'pendiente',
    ];

    protected function casts(): array
    {
        return [
            'porcentaje' => 'decimal:2',
            'monto' => 'decimal:2',
            'fecha_liquidacion' => 'date',
        ];
    }

    public function operacion(): BelongsTo
    {
        return $this->belongsTo(Operacion::class);
    }

    public function agente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agente_id');
    }

    public function liquidar(): void
    {
        $this->update([
            'estado' => 'liquidada',
            'fecha_liquidacion' => now()->toDateString(),
        ]);
    }
}
