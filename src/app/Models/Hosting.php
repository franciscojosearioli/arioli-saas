<?php

namespace App\Models;

use App\Contracts\AssetInterface;
use App\Enums\HostingStatus;
use App\Traits\HasCostSplit;
use App\Traits\HasRenewalStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Hosting extends Model implements AssetInterface
{
    use HasCostSplit, HasRenewalStatus;

    protected $fillable = [
        'client_id',
        'domain_id',
        'hosting_plan_id',
        'provider',
        'plan',
        'type',
        'status',
        'account_holder',
        'account_email',
        'registered_at',
        'expires_at',
        'provisioned_at',
        'auto_renew',
        'renewal_payer',
        'provider_cost',
        'management_fee',
    ];

    protected $casts = [
        'status'          => HostingStatus::class,
        'registered_at'   => 'date',
        'expires_at'      => 'date',
        'provisioned_at'  => 'datetime',
        'auto_renew'      => 'boolean',
        'provider_cost'   => 'decimal:2',
        'management_fee'  => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function hostingPlan(): BelongsTo
    {
        return $this->belongsTo(HostingPlan::class);
    }

    /**
     * Vínculo directo con el dominio de esta cuenta — deliberadamente no pasa
     * por un Project (ver Admin\HostingOrderController::store()): un Project
     * es una entidad de gestión que el admin crea a mano, y no debe ser
     * requisito para que retrySsl()/setupMail()/ProvisionHostingAccount
     * encuentren el dominio de la cuenta.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(ClientDomain::class, 'domain_id');
    }

    public function account(): HasOne
    {
        return $this->hasOne(HostingAccount::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'hosting_id');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->latest();
    }

    public function credentials(): MorphMany
    {
        return $this->morphMany(Credential::class, 'credentialable')->latest();
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
        return "Hosting: {$this->provider}" . ($this->plan ? " — {$this->plan}" : '');
    }
}
