<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Integration extends Model
{
    protected $fillable = [
        'client_id',
        'project_id',
        'provider',
        'config',
        'is_encrypted',
        'active',
    ];

    protected $casts = [
        'config'       => 'array',
        'is_encrypted' => 'boolean',
        'active'       => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
