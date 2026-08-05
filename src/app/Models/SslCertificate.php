<?php

namespace App\Models;

use App\Contracts\AssetInterface;
use App\Enums\SslCertificateStatus;
use App\Traits\HasCostSplit;
use App\Traits\HasRenewalStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SslCertificate extends Model implements AssetInterface
{
    use HasCostSplit, HasRenewalStatus;

    protected $fillable = [
        'client_id',
        'provider',
        'status',
        'expires_at',
        'auto_renew',
        'renewal_payer',
        'provider_cost',
        'management_fee',
    ];

    protected $casts = [
        'status'          => SslCertificateStatus::class,
        'expires_at'      => 'date',
        'auto_renew'      => 'boolean',
        'provider_cost'   => 'decimal:2',
        'management_fee'  => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
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
        return "SSL: {$this->provider}";
    }
}
