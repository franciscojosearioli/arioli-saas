<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\OptimisticLocking;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Paciente extends Model
{
    use SoftDeletes, Auditable, HasFactory, OptimisticLocking;

    public $table = 'pacientes';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'fecha_nac',
    ];

    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'telefono',
        'email',
        'fecha_nac',
        'sexo',
        'edad',
        'estado_civil',
        'obra_social',
        'n_afiliado',
        'provincia',
        'localidad',
        'calle',
        'calle_numero',
        'calle_piso',
        'calle_dpto',
        'familiar_hijos',
        'familiar_hermanos',
        'historial_tratamiento',
        'lock_version',
        'created_at',
        'updated_at',
        'deleted_at',
        'consentimiento_firmado',
        'consentimiento_firmado_at',
        'consentimiento_firma_imagen',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function informes()
    {
        return $this->hasMany(Informe::class);
    }

    public function medicacion()
    {
        return $this->hasMany(Medicacion::class);
    }

    public function ficha_admision(): HasOne
    {
        return $this->hasOne(PacienteFichaAdmision::class);
    }
    
    public function reingreso()
    {
        return $this->hasMany(PacienteReingreso::class, 'paciente_id');
    }
    
    public function padres_tutores(): HasOne
    {
        return $this->hasOne(PacientePadresTutor::class);
    }

    public function conyugue(): HasOne
    {
        return $this->hasOne(PacienteConyugue::class);
    }

    public function hijos()
    {
        return $this->hasMany(PacienteHijo::class);
    }

    public function hermanos()
    {
        return $this->hasMany(PacienteHermano::class);
    }

    public function educacion(): HasOne
    {
        return $this->hasOne(PacienteEducacion::class);
    }

    public function laboral(): HasOne
    {
        return $this->hasOne(PacienteLaboral::class);
    }

    public function historial_tratamientos()
    {
        return $this->hasMany(PacienteHistorialTratamientos::class);
    }

    public function problematica(): HasOne
    {
        return $this->hasOne(PacienteProblematica::class);
    }

    public function datos_adicionales(): HasOne
    {
        return $this->hasOne(PacienteDatosAdicionales::class);
    }
    
    public function actualizarStatus()
        {
            // Obtener el último reingreso ordenado por fecha_reingreso descendente
            $ultimoReingreso = $this->reingreso()->orderByDesc('fecha_reingreso')->first();
        
            if ($ultimoReingreso) {
                // Si hay un reingreso, el status depende de su fecha_egreso_reingreso
                $this->status = $ultimoReingreso->fecha_egreso ? 0 : 1;
            } else {
                // Si no hay reingresos, el status depende de la última ficha de admisión
                $ultimaFicha = $this->ficha_admision()->orderByDesc('fecha_ingreso')->first();
                $this->status = ($ultimaFicha && !$ultimaFicha->fecha_egreso) ? 1 : 0;
            }
        
            $this->save();
        }
}