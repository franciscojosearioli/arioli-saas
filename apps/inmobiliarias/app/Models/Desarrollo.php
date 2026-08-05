<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Desarrollo extends Model
{
    use HasFactory;

    // 'ubicacion' (GEOMETRY) queda fuera a propósito: no hay todavía UI que
    // la escriba, y asignarla como atributo normal en un modelo existente
    // rompe (lección de loteos — ver [[project_arioli_loteos_app]] en
    // memoria). Cuando exista ese caso de uso, escribirla vía query builder
    // + DB::raw("ST_GeomFromText(...)"), nunca por asignación directa.
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
}
