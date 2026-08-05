<?php

namespace App\Models;

use App\Enums\SignerRole;
use App\Enums\SignerStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ContractSigner extends Model
{
    use Auditable;

    protected $fillable = [
        'contract_id',
        'role',
        'name',
        'email',
        'order',
        'status',
        'signing_token',
        'signing_token_expires_at',
        'signed_at',
    ];

    protected $casts = [
        'role'                     => SignerRole::class,
        'status'                   => SignerStatus::class,
        'signing_token_expires_at' => 'datetime',
        'signed_at'                => 'datetime',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function signature(): HasOne
    {
        return $this->hasOne(ContractSignature::class);
    }

    public function isTokenValid(): bool
    {
        return $this->status === SignerStatus::Pending
            && $this->signing_token !== null
            && $this->signing_token_expires_at !== null
            && $this->signing_token_expires_at->isFuture();
    }
}
