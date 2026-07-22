<?php

namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformeTipo extends Model
{
    use SoftDeletes, Auditable, HasFactory;

    public $table = 'informes_tipos';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'modulo_key',
        'categoria',
        'firma_requerida',
        'roles_firmantes',
        'visible_portal',
        'permiso_codigo',
        'activo',
        'orden',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'roles_firmantes' => 'array',
        'firma_requerida' => 'boolean',
        'visible_portal'  => 'boolean',
        'activo'          => 'boolean',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function plantillas()
    {
        return $this->hasMany(PlantillaDocumento::class, 'tipo_documento_id');
    }
}