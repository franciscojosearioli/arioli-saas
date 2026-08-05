<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Constructora extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'logo',
        'email',
        'telefono',
    ];

    public function desarrollos(): HasMany
    {
        return $this->hasMany(Desarrollo::class);
    }
}
