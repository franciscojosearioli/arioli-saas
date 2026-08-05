<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Propiedad extends Model
{
    use HasFactory, SoftDeletes;

    // Sin esto, Eloquent pluraliza "Propiedad" en inglés -> "propiedads".
    protected $table = 'propiedades';

    // Espeja los defaults de la migración: sin esto, un create() que no
    // manda 'estado'/'moneda' deja esos atributos en null en el modelo en
    // memoria hasta el próximo fresh() — el default de MySQL/sqlite nunca
    // se lee de vuelta automáticamente.
    protected $attributes = [
        'estado' => 'disponible',
        'moneda' => 'ARS',
    ];

    // 'ubicacion' (GEOMETRY) queda fuera a propósito — ver Desarrollo.php.
    protected $fillable = [
        'desarrollo_id',
        'propietario_id',
        'tipo',
        'titulo',
        'descripcion',
        'estado',
        'precio',
        'moneda',
        'superficie_cubierta',
        'superficie_total',
        'ambientes',
        'dormitorios',
        'banos',
        'cocheras',
        'manzana',
        'numero_lote',
        'direccion',
        'provincia',
        'ciudad',
        'barrio',
        'servicios',
        'caracteristicas_destacadas',
        'atributos',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'superficie_cubierta' => 'decimal:2',
            'superficie_total' => 'decimal:2',
            'servicios' => 'array',
            'caracteristicas_destacadas' => 'array',
            'atributos' => 'array',
        ];
    }

    public function desarrollo(): BelongsTo
    {
        return $this->belongsTo(Desarrollo::class);
    }

    public function propietario(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'propietario_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function operaciones(): HasMany
    {
        return $this->hasMany(Operacion::class);
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentable');
    }

    public function publicacion(): HasOne
    {
        return $this->hasOne(Publicacion::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoPropiedad::class)->orderBy('orden');
    }

    /**
     * §04: ubicación como GEOMETRY (point o polygon) — nunca por
     * asignación directa del atributo (lección de loteos, ver
     * Desarrollo::class), siempre vía query builder + ST_GeomFromText.
     * $wkt ya viene validado (regex) por quien llama esto — igual se
     * revalida acá porque una raw query nunca debe confiar en el llamador.
     */
    public function guardarUbicacion(?string $wkt): void
    {
        if ($wkt === null || $wkt === '') {
            static::whereKey($this->id)->update(['ubicacion' => null]);

            return;
        }

        if (! preg_match('/^(POINT\([-\d.\s]+\)|POLYGON\(\([-\d.,\s]+\)\))$/i', $wkt)) {
            throw new \InvalidArgumentException('WKT de ubicación inválido.');
        }

        static::whereKey($this->id)->update(['ubicacion' => DB::raw("ST_GeomFromText('{$wkt}')")]);
    }

    // Las funciones ST_* son de MySQL — en sqlite (tests) esto correría
    // siempre, incluso para desarrollos sin ubicación, y rompería
    // cualquier test que dispare el observer/sync sin tocar el mapa.
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

    /**
     * Extraído del PropiedadObserver para que también lo pueda llamar el
     * update de ubicación (query builder crudo, nunca dispara el evento
     * `updated` de Eloquent) — sin esto, cambiar solo la geometría nunca
     * llega al marketplace.
     */
    public function dispararSincronizacion(string $evento = 'PropiedadActualizada'): void
    {
        if (! $this->publicacion()->exists()) {
            return;
        }

        OutboxEvent::create([
            'aggregate_type' => static::class,
            'aggregate_id' => $this->id,
            'evento' => $evento,
            'payload' => ['propiedad_id' => $this->id],
        ]);
    }
}
