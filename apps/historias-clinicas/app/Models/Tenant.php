<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    public $table = 'tenants';

    protected $fillable = [
        'tenant_key',
        'database',
        'status',
        'version',
        'last_migration_at',
        'last_migration_status',
    ];

    protected $casts = [
        'last_migration_at' => 'datetime',
    ];
}
