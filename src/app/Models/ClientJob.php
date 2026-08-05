<?php

namespace App\Models;

use App\Enums\JobStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * "Trabajo puntual" facturable a un cliente. Tabla `client_jobs` (no `jobs`):
 * `jobs` ya es la cola de trabajos en background de Laravel.
 */
class ClientJob extends Model
{
    protected $table = 'client_jobs';

    protected $fillable = [
        'client_id',
        'project_id',
        'owner_user_id',
        'title',
        'description',
        'hours',
        'amount',
        'status',
        'requested_at',
        'completed_at',
    ];

    protected $casts = [
        'status'       => JobStatus::class,
        'hours'        => 'decimal:2',
        'amount'       => 'decimal:2',
        'requested_at' => 'date',
        'completed_at' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function charges(): MorphMany
    {
        return $this->morphMany(Charge::class, 'chargeable');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->latest();
    }

    public function timeEntries(): MorphMany
    {
        return $this->morphMany(TimeEntry::class, 'trackable');
    }
}
