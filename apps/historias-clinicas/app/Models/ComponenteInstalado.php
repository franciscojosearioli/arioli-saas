<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComponenteInstalado extends Model
{
    public $table = 'componentes_instalados';

    protected $fillable = [
        'componente_key',
        'instalado_en',
        'instalado_por',
    ];

    protected $casts = [
        'instalado_en' => 'datetime',
    ];
}
