<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class ContractSignature extends Model
{
    public $timestamps = false;

    protected $fillable = ['contract_signer_id', 'ip_address', 'user_agent', 'content_hash', 'signed_at'];

    protected $casts = ['signed_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(fn (self $s) => $s->signed_at ??= now());
    }

    public function update(array $attributes = [], array $options = []): bool
    {
        throw new RuntimeException('ContractSignature es de solo lectura — es evidencia legal.');
    }

    public function delete(): bool
    {
        throw new RuntimeException('ContractSignature es de solo lectura — es evidencia legal.');
    }

    public function signer(): BelongsTo
    {
        return $this->belongsTo(ContractSigner::class, 'contract_signer_id');
    }
}
