<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlantillaDocumento extends Model
{
    use SoftDeletes;

    public $table = 'plantillas_documento';

    protected $fillable = [
        'tipo_documento_id',
        'nombre',
        'codigo',
        'activo',
        'orden',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function tipoDocumento()
    {
        return $this->belongsTo(InformeTipo::class, 'tipo_documento_id');
    }

    public function versiones()
    {
        return $this->hasMany(PlantillaDocumentoVersion::class, 'plantilla_documento_id');
    }

    public function versionVigente(): ?PlantillaDocumentoVersion
    {
        return $this->versiones()
            ->where('vigente_desde', '<=', now())
            ->orderByDesc('version')
            ->first();
    }
}
