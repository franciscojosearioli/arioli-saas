<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
