<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlantillaDocumentoVersion extends Model
{
    public $table = 'plantilla_documento_versiones';

    protected $fillable = [
        'plantilla_documento_id',
        'version',
        'contenido',
        'variables_disponibles',
        'vigente_desde',
        'creado_por',
    ];

    protected $casts = [
        'variables_disponibles' => 'array',
        'vigente_desde' => 'datetime',
    ];

    public function plantilla()
    {
        return $this->belongsTo(PlantillaDocumento::class, 'plantilla_documento_id');
    }

    public function creadoPor()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    /**
     * Sustitución simple {{clave}} -> valor. Sin lógica condicional a
     * propósito (decisión de diseño: no evaluar código arbitrario desde
     * contenido editable por admins). Ver docs/ARQUITECTURA_MODULAR.md
     * sección "Motor de documentos".
     */
    public function renderizar(array $variables): string
    {
        $contenido = $this->contenido;

        foreach ($variables as $clave => $valor) {
            $contenido = str_replace('{{' . $clave . '}}', (string) $valor, $contenido);
        }

        return $contenido;
    }
}
