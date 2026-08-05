<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    public $table = 'tenants';

    protected $fillable = [
        'tenant_key',
        'slug',
        'database',
        'status',
        'credencial_claimed_at',
        'version',
        'last_migration_at',
        'last_migration_status',
    ];

    protected $casts = [
        'last_migration_at' => 'datetime',
        'credencial_claimed_at' => 'datetime',
    ];
}
