<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class OutboxEvent extends Model
{
    // Solo existe 'created_at' (con default de DB) — no hay 'updated_at'
    // en la migración, es un log de solo-inserción salvo por los campos
    // de estado de procesamiento (procesado_en/intentos/ultimo_error).
    public $timestamps = false;

    protected $fillable = [
        'aggregate_type',
        'aggregate_id',
        'evento',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'procesado_en' => 'datetime',
        ];
    }

    public function scopePendientes(Builder $query): Builder
    {
        return $query->whereNull('procesado_en');
    }

    /**
     * procesado_en/intentos/ultimo_error son estado interno de
     * procesamiento, nunca datos que deban ser mass-asignables desde un
     * request — por eso forceFill() en vez de sumarlos a $fillable.
     */
    public function marcarProcesado(): void
    {
        $this->forceFill(['procesado_en' => now()])->save();
    }

    public function marcarError(string $mensaje): void
    {
        $this->forceFill([
            'intentos' => $this->intentos + 1,
            'ultimo_error' => $mensaje,
        ])->save();
    }
}
