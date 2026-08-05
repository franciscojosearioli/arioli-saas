<?php

namespace App\Modules\Odontologia\Models;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TratamientoOdontologico extends Model
{
    use SoftDeletes;

    public $table = 'tratamientos_odontologicos';

    protected $fillable = [
        'paciente_id',
        'odontograma_id',
        'numero_fdi',
        'superficie',
        'tipo_tratamiento',
        'estado_tratamiento',
        'fecha_planificada',
        'fecha_realizada',
        'profesional_id',
        'material',
        'observaciones',
    ];

    protected $casts = [
        'fecha_planificada' => 'date',
        'fecha_realizada' => 'date',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class);
    }

    public function odontograma()
    {
        return $this->belongsTo(Odontograma::class);
    }

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    public function marcarRealizado(?\DateTimeInterface $fecha = null): void
    {
        $this->update([
            'estado_tratamiento' => 'realizado',
            'fecha_realizada' => $fecha ?? now(),
        ]);
    }

    public static function tiposLabels(): array
    {
        return [
            'obturacion' => 'Obturación',
            'extraccion' => 'Extracción',
            'endodoncia' => 'Endodoncia',
            'corona' => 'Corona',
            'sellado' => 'Sellado',
            'limpieza' => 'Limpieza',
            'implante' => 'Implante',
            'otro' => 'Otro',
        ];
    }

    public static function estadosLabels(): array
    {
        return [
            'pendiente' => 'Pendiente',
            'realizado' => 'Realizado',
            'cancelado' => 'Cancelado',
        ];
    }
}
