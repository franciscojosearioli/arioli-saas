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

    // No hay todavía un listado de "Desarrollos a su cargo" (§08) — eso
    // requiere sincronizar Desarrollo como su propia entidad al
    // marketplace (hoy solo Propiedad se sincroniza, con el nombre del
    // desarrollo como texto suelto en la ficha, no una relación real).
    // Mismo bloqueo que la vista de mapa: se resuelve cuando se sincronice
    // Desarrollo de verdad, no fabricando una relación que hoy no existe.
}
