<?php

namespace App\Models;

use App\Enums\ContractType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class ContractTemplate extends Model
{
    use Auditable;

    protected $fillable = ['name', 'type', 'content', 'version', 'active'];

    protected $casts = [
        'type'    => ContractType::class,
        'version' => 'integer',
        'active'  => 'boolean',
    ];

    protected static function booted(): void
    {
        // Cada edición del contenido queda como una versión inmutable antes de sobreescribir.
        static::updating(function (self $template) {
            if ($template->isDirty('content')) {
                $template->versions()->create([
                    'version'    => $template->getOriginal('version'),
                    'content'    => $template->getOriginal('content'),
                    'created_by' => Auth::id(),
                ]);

                $template->version = (int) $template->getOriginal('version') + 1;
            }
        });
    }

    public function versions(): HasMany
    {
        return $this->hasMany(ContractTemplateVersion::class)->latest('version');
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
