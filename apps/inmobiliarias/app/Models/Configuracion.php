<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    protected $table = 'configuracion';

    protected $fillable = [
        'comision_porcentaje',
    ];

    protected function casts(): array
    {
        return [
            'comision_porcentaje' => 'decimal:2',
        ];
    }

    public static function actual(): self
    {
        return static::query()->firstOrCreate([]);
    }
}
