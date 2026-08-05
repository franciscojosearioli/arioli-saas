<?php

namespace App\Models;

use App\Enums\BillingCycle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HostingPlan extends Model
{
    protected $fillable = [
        'name',
        'marketing_description',
        'specs_json',
        'price',
        'currency',
        'billing_cycle',
        'active',
        'hestia_package',
    ];

    protected $casts = [
        'specs_json'    => 'array',
        'price'         => 'decimal:2',
        'billing_cycle' => BillingCycle::class,
        'active'        => 'boolean',
    ];

    public function hostings(): HasMany
    {
        return $this->hasMany(Hosting::class);
    }

    public function scopeSellable(Builder $query): Builder
    {
        return $query->where('active', true);
    }
}
