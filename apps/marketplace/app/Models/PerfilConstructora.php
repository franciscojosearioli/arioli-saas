<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PerfilConstructora extends Model
{
    use HasFactory;

    protected $table = 'perfiles_constructora';

    protected $fillable = [
        'tenant_id',
        'constructora_id',
        'slug',
        'nombre',
        'descripcion',
        'logo_url',
    ];

    public static function sincronizar(string $tenantId, int $constructoraId, array $datos): self
    {
        $perfil = static::firstOrNew(['tenant_id' => $tenantId, 'constructora_id' => $constructoraId]);
        $perfil->fill($datos);

        if (! $perfil->slug) {
            $perfil->slug = Str::slug($datos['nombre']).'-'.Str::slug($tenantId).'-'.$constructoraId;
        }

        $perfil->save();

        return $perfil;
    }
}
