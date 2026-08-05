<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use RuntimeException;

class ContractTemplateVersion extends Model
{
    public $timestamps = false;

    protected $fillable = ['contract_template_id', 'version', 'content', 'created_by', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(fn (self $v) => $v->created_at ??= now());
    }

    public function update(array $attributes = [], array $options = []): bool
    {
        throw new RuntimeException('ContractTemplateVersion es de solo lectura.');
    }

    public function delete(): bool
    {
        throw new RuntimeException('ContractTemplateVersion es de solo lectura.');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ContractTemplate::class, 'contract_template_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
