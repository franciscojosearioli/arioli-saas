<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PacientePadresTutor extends Model
{    
    use SoftDeletes, HasFactory;

    public $table = 'pacientes_padres_tutor';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'paciente_id',
        'padre_nombre',
        'padre_telefono',
        'padre_responsable',
        'madre_nombre',
        'madre_telefono',
        'madre_responsable',
        'tutor_nombre',
        'tutor_telefono',
        'tutor_responsable',
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
