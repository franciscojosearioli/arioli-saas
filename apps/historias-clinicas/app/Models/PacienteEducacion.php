<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PacienteEducacion extends Model
{    
    use SoftDeletes, HasFactory;

    public $table = 'pacientes_educacion';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'paciente_id',
        'primaria_completa',
        'primaria_incompleta',
        'primaria_ultimo_anio',
        'primaria_expulsado',
        'primaria_interrumpido',
        'primaria_cambios',
        'secundaria_completa',
        'secundaria_incompleta',
        'secundaria_ultimo_anio',
        'secundaria_expulsado',
        'secundaria_interrumpido',
        'secundaria_cambios',
        'terciaria_completa',
        'terciaria_incompleta',
        'terciaria_ultimo_anio',
        'terciaria_expulsado',
        'terciaria_interrumpido',
        'terciaria_cambios',
        'facultad_completa',
        'facultad_incompleta',
        'facultad_ultimo_anio',
        'facultad_expulsado',
        'facultad_interrumpido',
        'facultad_cambios',
        'observaciones',
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
