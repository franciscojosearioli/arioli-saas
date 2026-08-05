<?php

namespace App\Modules\Odontologia\Models;

use Illuminate\Database\Eloquent\Model;

class SuperficieOdontologica extends Model
{
    public $table = 'superficies_odontologicas';

    protected $fillable = [
        'pieza_odontologica_id',
        'superficie',
        'estado',
        'observaciones',
    ];

    public function pieza()
    {
        return $this->belongsTo(PiezaOdontologica::class, 'pieza_odontologica_id');
    }

    public static function estadosLabels(): array
    {
        return [
            'sana' => 'Sana',
            'cariada' => 'Cariada',
            'obturada' => 'Obturada',
            'corona' => 'Corona',
            'sellada' => 'Sellada',
            'fracturada' => 'Fracturada',
        ];
    }

    public static function superficiesLabels(): array
    {
        return [
            'oclusal' => 'Oclusal',
            'incisal' => 'Incisal',
            'vestibular' => 'Vestibular',
            'palatina_lingual' => 'Palatina/Lingual',
            'mesial' => 'Mesial',
            'distal' => 'Distal',
        ];
    }
}
