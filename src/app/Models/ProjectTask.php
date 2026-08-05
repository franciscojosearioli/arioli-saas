<?php

namespace App\Models;

use App\Enums\ProjectTaskStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTask extends Model
{
    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'position',
    ];

    protected $casts = [
        'status' => ProjectTaskStatus::class,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
