<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class Desarrollo extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'desarrollo_id',
        'constructora_id',
        'slug',
        'nombre',
        'tipo',
        'descripcion',
        'provincia',
        'ciudad',
        'barrio',
        'plano_maestro',
    ];

    /**
     * §08: mismo criterio idempotente que FichaPropiedad::publicar() —
     * (tenant_id, desarrollo_id) identifica una fila única, así que
     * sincronizar dos veces el mismo desarrollo actualiza en vez de
     * duplicar.
     */
    public static function sincronizar(string $tenantId, int $desarrolloId, array $datos): self
    {
        $desarrollo = static::firstOrNew(['tenant_id' => $tenantId, 'desarrollo_id' => $desarrolloId]);
        $desarrollo->fill($datos);

        if (! $desarrollo->slug) {
            $desarrollo->slug = Str::slug($datos['nombre']).'-'.Str::slug($tenantId).'-'.$desarrolloId;
        }

        $desarrollo->save();

        return $desarrollo;
    }

    public function fichas(): HasMany
    {
        return $this->hasMany(FichaPropiedad::class);
    }

    // 'ubicacion' (GEOMETRY, polígono general) queda fuera del fillable a
    // propósito — mismo motivo que el Desarrollo del lado tenant: nunca
    // por asignación directa, siempre vía query builder.
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

    // Guard de driver: ST_AsGeoJSON es de MySQL — en sqlite (tests) esto
    // debe devolver null en vez de romper cualquier test que renderice la
    // vista de mapa sin geometría real cargada.
    public function ubicacionComoGeoJson(): ?array
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return null;
        }

        $geojson = static::whereKey($this->id)->value(DB::raw('ST_AsGeoJSON(ubicacion)'));

        return $geojson ? json_decode($geojson, true) : null;
    }
}
