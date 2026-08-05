<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Cuenta técnica real en un panel de hosting (HestiaCP hoy, cualquier otro
 * mañana) — separada de `Hosting` (la capa comercial) a propósito, para no
 * atar el servicio que el cliente compró a un proveedor de panel específico.
 * Se crea solo cuando el provisioning real termina con éxito, nunca antes.
 */
class HostingAccount extends Model
{
    protected $fillable = [
        'hosting_id',
        'provider',
        'remote_username',
        'panel_url',
        'status',
        'last_sync_at',
        'credential_claimed_at',
    ];

    protected $casts = [
        'last_sync_at'           => 'datetime',
        'credential_claimed_at'  => 'datetime',
    ];

    public function hosting(): BelongsTo
    {
        return $this->belongsTo(Hosting::class);
    }

    public function credentials(): MorphMany
    {
        return $this->morphMany(Credential::class, 'credentialable')->latest();
    }
}
