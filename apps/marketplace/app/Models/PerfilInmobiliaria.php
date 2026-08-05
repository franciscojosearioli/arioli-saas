<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PerfilInmobiliaria extends Model
{
    use HasFactory;

    protected $table = 'perfiles_inmobiliaria';

    protected $fillable = [
        'tenant_id',
        'slug',
        'nombre_comercial',
        'descripcion',
        'logo_url',
    ];

    public static function sincronizar(string $tenantId, array $datos): self
    {
        $perfil = static::firstOrNew(['tenant_id' => $tenantId]);
        $perfil->fill($datos);

        if (! $perfil->slug) {
            $perfil->slug = Str::slug($datos['nombre_comercial']).'-'.Str::slug($tenantId);
        }

        $perfil->save();

        return $perfil;
    }

    public function fichas()
    {
        return FichaPropiedad::where('tenant_id', $this->tenant_id);
    }
}
