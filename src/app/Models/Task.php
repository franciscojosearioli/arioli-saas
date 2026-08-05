<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Tarea/recordatorio accionable para un cliente — a diferencia de ClientEvent
 * (log inmutable de lo que ya pasó), esto es mutable: se puede marcar como
 * completada. Alimenta el panel "Hoy" del Workspace del Cliente.
 */
class Task extends Model
{
    protected $fillable = [
        'client_id',
        'title',
        'description',
        'due_date',
        'assigned_to',
        'status',
        'source',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completada';
    }
}
