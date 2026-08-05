<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectRepository extends Model
{
    protected $fillable = [
        'project_id',
        'provider',
        'url',
        'branch',
        'is_main',
        'is_private',
    ];

    protected $casts = [
        'is_main'    => 'boolean',
        'is_private' => 'boolean',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
