<?php

namespace App\Modules\Odontologia\Models;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Primera entidad real de dominio de un Componente. Depende de Paciente
 * (Core) — dependencia en un solo sentido, la esperada. Paciente.php NO se
 * tocó: no tiene ningún método odontogramas(). Ver docs/ARQUITECTURA_MODULAR.md
 * Etapa 4.3 para la fricción que esto reveló.
 */
class Odontograma extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'paciente_id',
        'profesional_id',
        'fecha',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    public function piezas()
    {
        return $this->hasMany(PiezaDental::class);
    }

    /** Notación FDI adulto: 11-18, 21-28, 31-38, 41-48 (32 piezas). */
    public static function numerosFdiAdulto(): array
    {
        $numeros = [];
        foreach ([1, 2, 3, 4] as $cuadrante) {
            foreach (range(1, 8) as $posicion) {
                $numeros[] = ($cuadrante * 10) + $posicion;
            }
        }
        return $numeros;
    }
}
