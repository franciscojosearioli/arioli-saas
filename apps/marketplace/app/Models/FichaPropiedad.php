<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Laravel\Scout\Searchable;

class FichaPropiedad extends Model
{
    use HasFactory, Searchable;

    protected $table = 'fichas_propiedad';

    protected $fillable = [
        'tenant_id',
        'propiedad_id',
        'slug',
        'titulo',
        'descripcion',
        'precio',
        'moneda',
        'tipo_operacion',
        'tipo_propiedad',
        'estado',
        'direccion',
        'ciudad',
        'provincia',
        'barrio',
        'superficie_cubierta',
        'superficie_total',
        'ambientes',
        'dormitorios',
        'banos',
        'cocheras',
        'servicios',
        'caracteristicas_destacadas',
        'nombre_desarrollo',
        'galeria',
        'destacada',
        'publicada_en',
    ];

    protected function casts(): array
    {
        return [
            'precio' => 'decimal:2',
            'superficie_cubierta' => 'decimal:2',
            'superficie_total' => 'decimal:2',
            'servicios' => 'array',
            'caracteristicas_destacadas' => 'array',
            'galeria' => 'array',
            'destacada' => 'boolean',
            'publicada_en' => 'datetime',
        ];
    }

    /**
     * §08: publicar (o re-publicar) una ficha para un tenant+propiedad es
     * upsert — el mismo par nunca genera dos fichas, así el
     * ChannelAdapter puede llamar publish() de forma idempotente si un
     * primer intento se cayó después de guardar pero antes de responder.
     */
    public static function publicar(string $tenantId, int $propiedadId, array $datos): self
    {
        $ficha = static::firstOrNew(['tenant_id' => $tenantId, 'propiedad_id' => $propiedadId]);

        // 'desarrollo_id' llega como el id del lado tenant (la fuente),
        // no el id de la fila `desarrollos` de esta base — nunca por
        // fill() directo, se resuelve abajo. 'ubicacion_wkt' tampoco es
        // fillable, se escribe aparte vía guardarUbicacion().
        $fuenteDesarrolloId = $datos['desarrollo_id'] ?? null;
        $wkt = $datos['ubicacion_wkt'] ?? null;

        $ficha->fill($datos);

        if (! $ficha->slug) {
            $ficha->slug = static::slugUnico($datos['titulo'], $tenantId, $propiedadId);
        }

        if (! $ficha->publicada_en) {
            $ficha->publicada_en = now();
        }

        $ficha->desarrollo_id = $fuenteDesarrolloId
            ? Desarrollo::where('tenant_id', $tenantId)->where('desarrollo_id', $fuenteDesarrolloId)->value('id')
            : null;

        $ficha->save();

        $ficha->guardarUbicacion($wkt);

        return $ficha;
    }

    public function desarrollo(): BelongsTo
    {
        return $this->belongsTo(Desarrollo::class);
    }

    // 'ubicacion' (GEOMETRY, point o polygon — §04) queda fuera del
    // fillable a propósito, mismo motivo que en Desarrollo::class.
    public function guardarUbicacion(?string $wkt): void
    {
        if ($wkt === null || $wkt === '') {
            static::whereKey($this->id)->update(['ubicacion' => null]);

            return;
        }

        if (! preg_match('/^(POINT\([-\d.\s]+\)|POLYGON\(\([-\d.,\s]+\)\))$/i', $wkt)) {
            throw new InvalidArgumentException('WKT de ubicación inválido.');
        }

        static::whereKey($this->id)->update(['ubicacion' => DB::raw("ST_GeomFromText('{$wkt}')")]);
    }

    public function ubicacionComoGeoJson(): ?array
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return null;
        }

        $geojson = static::whereKey($this->id)->value(DB::raw('ST_AsGeoJSON(ubicacion)'));

        return $geojson ? json_decode($geojson, true) : null;
    }

    private static function slugUnico(string $titulo, string $tenantId, int $propiedadId): string
    {
        return Str::slug($titulo).'-'.Str::slug($tenantId).'-'.$propiedadId;
    }

    public function searchableAs(): string
    {
        return 'fichas_propiedad';
    }

    /**
     * §08: los filtros que pide el Artifact — provincia/ciudad/tipo/
     * precio/operación/ambientes/superficie/servicios — más el texto
     * libre (título/descripción/barrio/desarrollo) para la búsqueda.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'precio' => $this->precio ? (float) $this->precio : null,
            'moneda' => $this->moneda,
            'tipo_operacion' => $this->tipo_operacion,
            'tipo_propiedad' => $this->tipo_propiedad,
            'estado' => $this->estado,
            'ciudad' => $this->ciudad,
            'provincia' => $this->provincia,
            'barrio' => $this->barrio,
            'superficie_cubierta' => $this->superficie_cubierta ? (float) $this->superficie_cubierta : null,
            'superficie_total' => $this->superficie_total ? (float) $this->superficie_total : null,
            'ambientes' => $this->ambientes,
            'dormitorios' => $this->dormitorios,
            'servicios' => $this->servicios,
            'nombre_desarrollo' => $this->nombre_desarrollo,
            'destacada' => $this->destacada,
            'publicada_en' => $this->publicada_en?->timestamp,
        ];
    }
}
