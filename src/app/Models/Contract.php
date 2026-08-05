<?php

namespace App\Models;

use App\Enums\ContractStatus;
use App\Enums\ContractType;
use App\Enums\SignerStatus;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contract extends Model
{
    use Auditable;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'contractable_type',
        'contractable_id',
        'contract_template_id',
        'title',
        'type',
        'content',
        'status',
        'requires_signature',
    ];

    protected $casts = [
        'type'               => ContractType::class,
        'status'             => ContractStatus::class,
        'requires_signature' => 'boolean',
    ];

    public function contractable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Un contrato con `client_id` seteado y `contractable` nulo es un
     * "Contrato General" del cliente (ej. términos y condiciones generales).
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'contract_template_id');
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id', 'id');
    }

    public function signers(): HasMany
    {
        return $this->hasMany(ContractSigner::class)->orderBy('order');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ContractEvent::class)->latest('created_at');
    }

    /**
     * Recalcula el estado agregado del contrato en base al estado de sus
     * firmantes. Se llama después de cada acción de un firmante (firmar,
     * rechazar) — nunca se infiere en el momento de leer.
     */
    public function recalculateStatus(): void
    {
        $signers = $this->signers;

        if ($signers->isEmpty()) {
            return;
        }

        if ($signers->contains(fn (ContractSigner $s) => $s->status === SignerStatus::Rejected)) {
            $this->update(['status' => ContractStatus::Rejected]);

            return;
        }

        if ($signers->every(fn (ContractSigner $s) => $s->status === SignerStatus::Signed)) {
            $this->update(['status' => ContractStatus::Signed]);
        }
    }
}
