<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoConsentimiento extends Model
{
    protected $table = 'tipos_consentimiento';

    protected $fillable = [
        'nombre',
        'descripcion',
        'contenido_pagina1',
        'contenido_pagina2',
        'contenido_paginas',
        'requiere_firma_profesional',
        'activo',
    ];

    protected $casts = [
        'contenido_paginas'          => 'array',
        'requiere_firma_profesional' => 'boolean',
        'activo'                     => 'boolean',
    ];

    public function consentimientos()
    {
        return $this->hasMany(Consentimiento::class, 'tipo_id');
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
