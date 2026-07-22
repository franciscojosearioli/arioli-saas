<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PacienteFichaAdmision extends Model
{    
    use SoftDeletes, HasFactory;

    public $table = 'pacientes_ficha_admision';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'fecha_ingreso',
        'fecha_egreso',
    ];

    protected $fillable = [
        'paciente_id',
        'fecha_ingreso',
        'modalidad',
        'fecha_egreso',
        'tipo_egreso',
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
