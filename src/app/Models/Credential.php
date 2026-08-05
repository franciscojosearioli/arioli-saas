<?php

namespace App\Models;

use App\Enums\CredentialOwner;
use App\Enums\CredentialType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Crypt;

class Credential extends Model
{
    protected $fillable = [
        'credentialable_type',
        'credentialable_id',
        'type',
        'label',
        'username',
        'secret',
        'url',
        'owner',
        'last_verified_at',
    ];

    protected $casts = [
        'type'             => CredentialType::class,
        'owner'            => CredentialOwner::class,
        'last_verified_at' => 'datetime',
    ];

    /**
     * Igual patrón que Setting::setValueAttribute — pero acá siempre se cifra,
     * nunca es opcional.
     */
    public function setSecretAttribute(?string $value): void
    {
        $this->attributes['secret'] = $value !== null ? Crypt::encryptString($value) : null;
    }

    public function getSecretAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception) {
            return null;
        }
    }

    public function credentialable(): MorphTo
    {
        return $this->morphTo();
    }

    public function markVerified(): void
    {
        $this->update(['last_verified_at' => now()]);
    }
}
