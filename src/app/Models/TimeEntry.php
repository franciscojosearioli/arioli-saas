<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Entrada de horas trabajadas contra cualquier `trackable` (Charge o
 * ClientJob) — el monto del trackable se recalcula sumando `subtotal` cada
 * vez que se crea/borra una entrada (ver TimeEntryController).
 */
class TimeEntry extends Model
{
    protected $fillable = [
        'trackable_type',
        'trackable_id',
        'description',
        'worked_on',
        'hours',
        'rate_per_hour',
        'subtotal',
    ];

    protected $casts = [
        'worked_on'     => 'date',
        'hours'         => 'decimal:2',
        'rate_per_hour' => 'decimal:2',
        'subtotal'      => 'decimal:2',
    ];

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }
}
