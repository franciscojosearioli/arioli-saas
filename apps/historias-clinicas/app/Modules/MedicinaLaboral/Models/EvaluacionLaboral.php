<?php

namespace App\Modules\MedicinaLaboral\Models;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Segundo Componente real (Etapa 5) — mismo criterio que Odontograma:
 * depende de Paciente en un solo sentido, Paciente.php no se toca.
 */
class EvaluacionLaboral extends Model
{
    use SoftDeletes;

    public $table = 'evaluaciones_laborales';

    protected $fillable = [
        'paciente_id',
        'profesional_id',
        'tipo',
        'fecha',
        'estado',
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

    public static function tiposLabels(): array
    {
        return [
            'preocupacional' => 'Preocupacional',
            'periodico' => 'Periódico',
            'egreso' => 'De egreso',
        ];
    }

    public static function estadosLabels(): array
    {
        return [
            'apto' => 'Apto',
            'no_apto' => 'No apto',
            'apto_con_restricciones' => 'Apto con restricciones',
        ];
    }
}
