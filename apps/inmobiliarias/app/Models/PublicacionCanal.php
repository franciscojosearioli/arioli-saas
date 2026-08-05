<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicacionCanal extends Model
{
    use HasFactory;

    // Sin esto, Eloquent pluraliza solo la última palabra -> "publicacion_canals".
    protected $table = 'publicacion_canales';

    // Cuántos reintentos con backoff antes de pasar a 'error' (§09).
    public const MAX_INTENTOS = 5;

    protected $fillable = [
        'publicacion_id',
        'canal',
        'estado',
        'external_id',
        'contenido_override',
        'programada_para',
        'fecha_publicada',
    ];

    protected $attributes = [
        'estado' => 'borrador',
    ];

    protected function casts(): array
    {
        return [
            'contenido_override' => 'array',
            'programada_para' => 'datetime',
            'fecha_publicada' => 'datetime',
        ];
    }

    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class);
    }

    public function marcarPublicando(): void
    {
        $this->update(['estado' => 'publicando']);
    }

    /**
     * §09: cada llamada a un canal es idempotente — si ya existe
     * external_id, el adapter actualiza en vez de crear un duplicado. Acá
     * solo se persiste el resultado de esa llamada.
     *
     * intentos/ultimo_error son estado interno de reintentos, no datos
     * mass-asignables desde un request — forceFill() en vez de sumarlos
     * a $fillable.
     */
    public function marcarPublicada(string $externalId): void
    {
        $this->update([
            'estado' => 'publicada',
            'external_id' => $externalId,
            'fecha_publicada' => $this->fecha_publicada ?? now(),
        ]);
        $this->forceFill(['intentos' => 0, 'ultimo_error' => null])->save();
    }

    /**
     * §09: reintentos con backoff — el CALLER (el worker) decide cuándo
     * reintentar; acá solo se registra el intento fallido y, al agotar
     * MAX_INTENTOS, se pasa a 'error' con motivo visible.
     */
    public function marcarError(string $mensaje): void
    {
        $intentos = $this->intentos + 1;

        $this->forceFill([
            'estado' => $intentos >= self::MAX_INTENTOS ? 'error' : $this->estado,
            'intentos' => $intentos,
            'ultimo_error' => $mensaje,
        ])->save();
    }

    public function pausar(): void
    {
        $this->update(['estado' => 'pausada']);
    }

    public function despublicar(): void
    {
        $this->update(['estado' => 'despublicada']);
    }
}
