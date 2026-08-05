<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    // Declare is_demo and demo_reset_at as real columns (not JSON data)
    public static function getCustomColumns(): array
    {
        return ['id', 'is_demo', 'is_custom', 'demo_reset_at'];
    }

    protected $casts = [
        'is_demo'       => 'boolean',
        'is_custom'     => 'boolean',
        'demo_reset_at' => 'datetime',
    ];

    public function licenses()
    {
        return $this->hasMany(\App\Models\License::class, 'tenant_id', 'id');
    }

    // Relación con tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'tenant_id', 'id');
    }

    // Relación con usuarios del tenant
    public function users()
    {
        return $this->hasMany(\App\Models\User::class, 'tenant_id', 'id');
    }
}