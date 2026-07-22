<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ConfiguracionSistema extends Model
{
    protected $table = 'configuracion_sistema';

    protected $fillable = [
        'nombre_sistema',
        'logo',
        'favicon',
        'nombre_institucion',
        'tipo_institucion',
        'descripcion',
        'direccion',
        'localidad',
        'provincia',
        'pais',
        'telefono',
        'email_contacto',
        'website',
        'cuit',
        'pie_pdf',
        'setup_completed',
    ];

    protected $casts = [
        'setup_completed' => 'boolean',
    ];

    /**
     * Devuelve la instancia única. Crea el registro por defecto si no existe.
     */
    public static function instancia(): self
    {
        return static::firstOrCreate([], [
            'nombre_sistema'     => 'Historias Clínicas',
            'nombre_institucion' => '',
            'provincia'          => 'Buenos Aires',
            'pais'               => 'Argentina',
        ]);
    }

    // ── Accessors de URL ──────────────────────────────────────────────────────

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return Storage::disk('public')->url($this->logo);
        }
        return null;
    }

    public function getLogoLoginUrlAttribute(): ?string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return Storage::disk('public')->url($this->logo);
        }
        return null;
    }

    public function getFaviconUrlAttribute(): ?string
    {
        if ($this->favicon && Storage::disk('public')->exists($this->favicon)) {
            return Storage::disk('public')->url($this->favicon);
        }
        return null;
    }
}
