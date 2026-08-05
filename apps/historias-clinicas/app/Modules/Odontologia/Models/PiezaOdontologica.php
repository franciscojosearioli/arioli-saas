<?php

namespace App\Modules\Odontologia\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Etapa 6.6.5 (ver docs/ARQUITECTURA_MODULAR.md): reemplaza a PiezaDental.
 * `estado_general` es solo para condiciones de la pieza ENTERA (ausente,
 * extraída) — el estado normal de trabajo (sana/cariada/obturada/...) vive
 * por superficie, en `superficies()`. El nombre anatómico, tipo de diente
 * y qué superficies aplican NO viven acá — vienen del catálogo estático
 * (config/platform/piezas_dentales_catalogo.php), consultado por
 * `numero_fdi`, no duplicados en esta tabla.
 */
class PiezaOdontologica extends Model
{
    public $table = 'piezas_odontologicas';

    protected $fillable = [
        'odontograma_id',
        'numero_fdi',
        'estado_general',
        'observaciones',
    ];

    public function odontograma()
    {
        return $this->belongsTo(Odontograma::class);
    }

    public function superficies()
    {
        return $this->hasMany(SuperficieOdontologica::class);
    }

    public function catalogo(): array
    {
        return config('piezas_dentales_catalogo.piezas.' . $this->numero_fdi, [
            'nombre' => 'Pieza ' . $this->numero_fdi,
            'tipo' => 'desconocido',
            'denticion' => 'permanente',
            'cuadrante' => (int) floor($this->numero_fdi / 10),
            'superficies' => ['oclusal', 'vestibular', 'palatina_lingual', 'mesial', 'distal'],
        ]);
    }

    public static function estadosGeneralesLabels(): array
    {
        return [
            'ausente' => 'Ausente',
            'extraida' => 'Extraída',
        ];
    }
}
