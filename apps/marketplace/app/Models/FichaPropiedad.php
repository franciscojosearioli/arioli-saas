<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FichaPropiedad extends Model
{
    use HasFactory;

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

        $ficha->fill($datos);

        if (! $ficha->slug) {
            $ficha->slug = static::slugUnico($datos['titulo'], $tenantId, $propiedadId);
        }

        if (! $ficha->publicada_en) {
            $ficha->publicada_en = now();
        }

        $ficha->save();

        return $ficha;
    }

    private static function slugUnico(string $titulo, string $tenantId, int $propiedadId): string
    {
        return Str::slug($titulo).'-'.Str::slug($tenantId).'-'.$propiedadId;
    }
}
