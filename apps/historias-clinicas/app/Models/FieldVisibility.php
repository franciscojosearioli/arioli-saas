<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldVisibility extends Model
{
    public $table = 'field_visibility';

    protected $fillable = [
        'entidad',
        'campo',
        'tipo',
        'visible',
        'requerido',
        'origen',
    ];

    protected $casts = [
        'visible'   => 'boolean',
        'requerido' => 'boolean',
    ];
}
