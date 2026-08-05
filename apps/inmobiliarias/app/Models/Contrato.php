<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contrato extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'operacion_id',
        'renueva_contrato_id',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'clausulas',
        'notas',
    ];

    protected $attributes = [
        'estado' => 'borrador',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
        ];
    }

    public function operacion(): BelongsTo
    {
        return $this->belongsTo(Operacion::class);
    }

    public function renuevaA(): BelongsTo
    {
        return $this->belongsTo(Contrato::class, 'renueva_contrato_id');
    }

    public function renovaciones(): HasMany
    {
        return $this->hasMany(Contrato::class, 'renueva_contrato_id');
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentable');
    }

    /**
     * §05: ContratoRenovado — "se extiende un alquiler". Crea el contrato
     * siguiente encadenado a este y marca este como renovado, en vez de
     * mutar las fechas del mismo registro y perder el historial.
     */
    public function renovar(array $datos): self
    {
        $renovacion = static::create([
            ...$datos,
            'operacion_id' => $this->operacion_id,
            'renueva_contrato_id' => $this->id,
        ]);

        $this->update(['estado' => 'renovado']);

        return $renovacion;
    }
}
