<?php

namespace App\Models;

use App\Contracts\AssetInterface;
use App\Enums\DomainStatus;
use App\Traits\HasCostSplit;
use App\Traits\HasRenewalStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Dominio administrado de un Client (activo del CRM). No confundir con
 * \Stancl\Tenancy\Database\Models\Domain, que resuelve el ruteo de subdominios
 * de los tenants SaaS — son conceptos de negocio completamente distintos.
 */
class ClientDomain extends Model implements AssetInterface
{
    use HasCostSplit, HasRenewalStatus;

    protected $table = 'client_domains';

    protected $fillable = [
        'client_id',
        'domain_name',
        'is_primary',
        'status',
        'registrar',
        'dns_provider',
        'account_holder',
        'account_email',
        'registered_at',
        'expires_at',
        'auto_renew',
        'renewal_payer',
        'provider_cost',
        'management_fee',
    ];

    protected $casts = [
        'status'          => DomainStatus::class,
        'is_primary'      => 'boolean',
        'registered_at'   => 'date',
        'expires_at'      => 'date',
        'auto_renew'      => 'boolean',
        'provider_cost'   => 'decimal:2',
        'management_fee'  => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'domain_id');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->latest();
    }

    public function credentials(): MorphMany
    {
        return $this->morphMany(Credential::class, 'credentialable')->latest();
    }

    public function charges(): MorphMany
    {
        return $this->morphMany(Charge::class, 'chargeable')->latest();
    }

    public function expiresAt(): ?Carbon
    {
        return $this->expires_at;
    }

    public function isAutoRenew(): bool
    {
        return $this->auto_renew;
    }

    public function renewalPayer(): string
    {
        return $this->renewal_payer;
    }

    public function label(): string
    {
        return "Dominio: {$this->domain_name}";
    }
}
