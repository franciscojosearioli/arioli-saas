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
 *
 * Etapa 6.6.5: cambia de cardinalidad — antes una fila por VISITA (con
 * `crear()` clonando 32 piezas en 'sana' cada vez), ahora **una fila por
 * paciente**, registro vivo del estado bucal actual. La evolución
 * histórica real vive en HistorialOdontologico (un cambio = una fila),
 * no en comparar snapshots completos duplicados — ver el análisis de
 * dominio en docs/ARQUITECTURA_MODULAR.md antes de este cambio.
 * `numerosFdiAdulto()` se retira: reemplazado por el catálogo estático
 * (config/platform/piezas_dentales_catalogo.php), que además de los
 * números sabe nombre anatómico, tipo de diente y qué superficies aplican.
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
        return $this->hasMany(PiezaOdontologica::class);
    }

    public function tratamientos()
    {
        return $this->hasMany(TratamientoOdontologico::class);
    }
}
