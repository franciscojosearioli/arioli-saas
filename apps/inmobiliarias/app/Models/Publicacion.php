<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Publicacion extends Model
{
    use HasFactory, SoftDeletes;

    // Sin esto, Eloquent pluraliza "Publicacion" en inglés -> "publicacions".
    protected $table = 'publicaciones';

    protected $fillable = [
        'propiedad_id',
        'destacada',
        'destacada_hasta',
    ];

    protected function casts(): array
    {
        return [
            'destacada' => 'boolean',
            'destacada_hasta' => 'date',
        ];
    }

    public function propiedad(): BelongsTo
    {
        return $this->belongsTo(Propiedad::class);
    }

    public function canales(): HasMany
    {
        return $this->hasMany(PublicacionCanal::class);
    }

    /**
     * §09: activa un canal para esta Publicación — si ya existe, lo deja
     * como estaba (activar de nuevo un canal ya publicado no lo duplica).
     */
    public function activarCanal(string $canal): PublicacionCanal
    {
        return $this->canales()->firstOrCreate(['canal' => $canal]);
    }
}
