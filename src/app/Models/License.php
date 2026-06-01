<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'tenant_id',
        'plan_id',
        'starts_at',
        'expires_at',
        'active',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'expires_at' => 'date',
        'active' => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}