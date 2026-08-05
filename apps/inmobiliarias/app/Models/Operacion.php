<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Operacion extends Model
{
    use HasFactory, SoftDeletes;

    // Sin esto, Eloquent pluraliza "Operacion" en inglés -> "operacions".
    protected $table = 'operaciones';

    // Estados de Propiedad (§04) que corresponden a cada tipo de
    // Operación cerrada — una venta deja la propiedad 'vendida', un
    // alquiler la deja 'alquilada'. 'reserva' no cambia el estado acá:
    // una reserva cerrada normalmente deriva en una venta/alquiler nueva.
    private const ESTADO_PROPIEDAD_AL_CERRAR = [
        'venta' => 'vendido',
        'alquiler' => 'alquilado',
    ];

    protected $fillable = [
        'propiedad_id',
        'agente_id',
        'tipo',
        'estado',
        'fecha_inicio',
        'fecha_cierre',
        'monto',
        'moneda',
        'indice_actualizacion',
        'notas',
    ];

    protected $attributes = [
        'estado' => 'abierta',
        'moneda' => 'ARS',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_cierre' => 'date',
            'monto' => 'decimal:2',
        ];
    }

    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }

    public function agente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agente_id');
    }

    public function partes(): BelongsToMany
    {
        return $this->belongsToMany(Cliente::class, 'operacion_cliente')
            ->withPivot('rol')
            ->withTimestamps();
    }

    public function cuotas(): HasMany
    {
        return $this->hasMany(Cuota::class);
    }

    public function contratos(): HasMany
    {
        return $this->hasMany(Contrato::class);
    }

    public function comision(): HasOne
    {
        return $this->hasOne(Comision::class);
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentable');
    }

    /**
     * §06: asigna una parte con su rol. Un cliente no puede tener dos
     * roles en la misma operación (unique en la tabla pivote).
     */
    public function asignarParte(Cliente $cliente, string $rol): void
    {
        $this->partes()->attach($cliente->id, ['rol' => $rol]);
    }

    /**
     * §06: genera el plan de cuotas — dispara PlanDeCuotasGenerado en el
     * dominio (sin listener todavía: no hay módulo de notificaciones
     * antes de Fase 5 que necesite reaccionar a esto).
     */
    public function generarPlanDeCuotas(int $cantidadCuotas, string $fechaPrimerVencimiento, string $montoPorCuota): void
    {
        $fecha = Carbon::parse($fechaPrimerVencimiento);

        for ($numero = 1; $numero <= $cantidadCuotas; $numero++) {
            $this->cuotas()->create([
                'numero' => $numero,
                'fecha_vencimiento' => $fecha->copy()->addMonthsNoOverflow($numero - 1),
                'monto' => $montoPorCuota,
                'moneda' => $this->moneda,
            ]);
        }
    }

    /**
     * §05: OperacionCerrada + ComisionGenerada. Actualiza el estado de la
     * Propiedad y genera la Comisión del agente asignado según el
     * porcentaje fijo del tenant (§17 Rev. 1.2) — si Configuracion no lo
     * tiene definido todavía, no inventa un número: no genera comisión.
     */
    public function cerrar(): void
    {
        $this->update([
            'estado' => 'cerrada',
            'fecha_cierre' => now()->toDateString(),
        ]);

        $estadoPropiedad = self::ESTADO_PROPIEDAD_AL_CERRAR[$this->tipo] ?? null;
        if ($estadoPropiedad) {
            $this->propiedad->update(['estado' => $estadoPropiedad]);
        }

        $this->generarComision();
    }

    public function cancelar(): void
    {
        $this->update(['estado' => 'cancelada']);
    }

    private function generarComision(): void
    {
        if (! $this->agente_id || ! $this->monto) {
            return;
        }

        $porcentaje = Configuracion::actual()->comision_porcentaje;
        if ($porcentaje === null) {
            return;
        }

        $this->comision()->create([
            'agente_id' => $this->agente_id,
            'porcentaje' => $porcentaje,
            'monto' => round((float) $this->monto * (float) $porcentaje / 100, 2),
            'moneda' => $this->moneda,
        ]);
    }
}
