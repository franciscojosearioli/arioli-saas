<?php

namespace App\Modules\Odontologia\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class HistorialOdontologico extends Model
{
    public $table = 'historial_odontologico';

    public $timestamps = true;
    const UPDATED_AT = null; // un registro de historial nunca se edita, solo se crea

    protected $fillable = [
        'entidad_tipo',
        'entidad_id',
        'estado_anterior',
        'estado_nuevo',
        'profesional_id',
        'motivo',
    ];

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    public static function registrar(string $entidadTipo, int $entidadId, ?string $estadoAnterior, string $estadoNuevo, ?int $profesionalId, ?string $motivo = null): self
    {
        return self::create([
            'entidad_tipo' => $entidadTipo,
            'entidad_id' => $entidadId,
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'profesional_id' => $profesionalId,
            'motivo' => $motivo,
        ]);
    }
}
