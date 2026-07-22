<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemoInstance extends Model
{
    public $table = 'demo_instances';

    protected $fillable = [
        'tenant_key',
        'perfil_key',
        'status',
        'expires_at',
        'activada_at',
        'expirada_at',
        'eliminada_at',
        'error_message',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'activada_at' => 'datetime',
        'expirada_at' => 'datetime',
        'eliminada_at' => 'datetime',
    ];
}
