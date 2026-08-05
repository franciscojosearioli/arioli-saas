<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Desarrollo extends Model
{
    use HasFactory;

    // 'ubicacion' (GEOMETRY, el polígono general del desarrollo — §08)
    // queda fuera del fillable a propósito: se escribe vía
    // guardarUbicacion(), nunca por asignación directa (lección de loteos).
    protected $fillable = [
        'constructora_id',
        'nombre',
        'tipo',
        'descripcion',
        'provincia',
        'ciudad',
        'barrio',
        'plano_maestro',
    ];

    public function constructora(): BelongsTo
    {
        return $this->belongsTo(Constructora::class);
    }

    public function propiedades(): HasMany
    {
        return $this->hasMany(Propiedad::class);
    }

    // Mismo mecanismo que Propiedad::guardarUbicacion() — ver ese método
    // para el porqué de la revalidación acá.
    public function guardarUbicacion(?string $wkt): void
    {
        if ($wkt === null || $wkt === '') {
            static::whereKey($this->id)->update(['ubicacion' => null]);

            return;
        }

        if (! preg_match('/^POLYGON\(\([-\d.,\s]+\)\)$/i', $wkt)) {
            throw new InvalidArgumentException('WKT de ubicación inválido — el polígono general solo admite POLYGON.');
        }

        static::whereKey($this->id)->update(['ubicacion' => DB::raw("ST_GeomFromText('{$wkt}')")]);
    }

    // Ver el comentario equivalente en Propiedad::ubicacionComoWkt().
    public function ubicacionComoWkt(): ?string
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return null;
        }

        return static::whereKey($this->id)->value(DB::raw('ST_AsText(ubicacion)'));
    }

    public function ubicacionComoGeoJson(): ?array
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return null;
        }

        $geojson = static::whereKey($this->id)->value(DB::raw('ST_AsGeoJSON(ubicacion)'));

        return $geojson ? json_decode($geojson, true) : null;
    }
}
