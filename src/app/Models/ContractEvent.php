<?php

namespace App\Models;

use App\Enums\ContractEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class ContractEvent extends Model
{
    public $timestamps = false;

    protected $fillable = ['contract_id', 'contract_signer_id', 'event', 'user_id', 'metadata', 'created_at'];

    protected $casts = [
        'event'      => ContractEventType::class,
        'metadata'   => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(fn (self $e) => $e->created_at ??= now());
    }

    public function update(array $attributes = [], array $options = []): bool
    {
        throw new RuntimeException('ContractEvent es de solo lectura — es la línea de tiempo del contrato.');
    }

    public function delete(): bool
    {
        throw new RuntimeException('ContractEvent es de solo lectura — es la línea de tiempo del contrato.');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(ContractSigner::class, 'contract_signer_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function log(
        Contract $contract,
        ContractEventType $event,
        ?ContractSigner $signer = null,
        array $metadata = [],
    ): self {
        return static::create([
            'contract_id'        => $contract->id,
            'contract_signer_id' => $signer?->id,
            'event'              => $event,
            'user_id'            => Auth::id(),
            'metadata'           => $metadata ?: null,
        ]);
    }
}
