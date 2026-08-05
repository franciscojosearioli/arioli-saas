<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'tipo_persona',
        'nombre',
        'documento',
        'email',
        'telefono',
        'direccion',
        'provincia',
        'ciudad',
        'notas',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Propiedades de las que este Cliente es propietario. "Propietario" no
     * es un tipo aparte (§04) — es el mismo Cliente en ese rol.
     */
    public function propiedades(): HasMany
    {
        return $this->hasMany(Propiedad::class, 'propietario_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function operaciones(): BelongsToMany
    {
        return $this->belongsToMany(Operacion::class, 'operacion_cliente')
            ->withPivot('rol')
            ->withTimestamps();
    }

    public function documentos(): MorphMany
    {
        return $this->morphMany(Documento::class, 'documentable');
    }
}
