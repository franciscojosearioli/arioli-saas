<?php

namespace App\Models;

use App\Traits\Auditable;
use App\Traits\OptimisticLocking;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Informe extends Model
{
    use SoftDeletes, Auditable, HasFactory, OptimisticLocking;

    public $table = 'informes';

    protected $fillable = [
        'paciente_id',
        'tipo_id',
        'plantilla_documento_version_id',
        'profesional_id',
        'agenda_id',
        'fecha',
        'diagnostico',
        'codigo_cie10',
        'redaccion',
        'document_files',
        'firmado',
        'firmado_por',
        'firmado_at',
        'lock_version',
    ];

    protected $casts = [
        'fecha'      => 'date',
        'firmado'    => 'boolean',
        'firmado_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function plantillaVersion()
    {
        return $this->belongsTo(PlantillaDocumentoVersion::class, 'plantilla_documento_version_id');
    }

    public function tipo()
    {
        return $this->belongsTo(InformeTipo::class, 'tipo_id');
    }

    public function profesional()
    {
        return $this->belongsTo(User::class, 'profesional_id');
    }

    public function agenda()
    {
        return $this->belongsTo(Agenda::class, 'agenda_id');
    }

    public function firmadoPor()
    {
        return $this->belongsTo(User::class, 'firmado_por');
    }

    public function recetas()
    {
        return $this->hasMany(Receta::class);
    }

    public function firmaAuditorias()
    {
        return $this->hasMany(FirmaAuditoria::class);
    }
}
