<?php

namespace App\Modules\Odontologia\Models;

use Illuminate\Database\Eloquent\Model;

class PiezaDental extends Model
{
    public $table = 'piezas_dentales';

    protected $fillable = [
        'odontograma_id',
        'numero',
        'estado',
        'observaciones',
    ];

    public function odontograma()
    {
        return $this->belongsTo(Odontograma::class);
    }

    public static function estadosLabels(): array
    {
        return [
            'sana' => 'Sana',
            'cariada' => 'Cariada',
            'obturada' => 'Obturada',
            'ausente' => 'Ausente',
            'extraida' => 'Extraída',
            'corona' => 'Corona',
            'implante' => 'Implante',
        ];
    }
}
