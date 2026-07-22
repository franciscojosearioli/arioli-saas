<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PacienteDatosAdicionales extends Model
{    
    use SoftDeletes, HasFactory;

    public $table = 'pacientes_datos_adicionales';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'paciente_id',
        'abuso_sexual',
        'sobredosis',
        'antecedentes_legales',
        'analfabeto',
        'padres_separados',
        'privado_libertad',
        'tiempo_privado_libertad',
        'enfermedad_cronica',
        'enfermedad_cronica_detalles',
        'alergia',
        'alergia_detalles',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

}
