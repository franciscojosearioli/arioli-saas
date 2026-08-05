<?php

namespace App\Models;

use App\Enums\BillingCycle;
use App\Enums\ClientServiceStatus;
use App\Enums\ServiceType;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ClientService extends Model
{
    use Auditable;

    protected $fillable = [
        'client_id',
        'project_id',
        'owner_user_id',
        'service_type',
        'billing_cycle',
        'amount',
        'provider_cost',
        'management_fee',
        'starts_at',
        'ends_at',
        'auto_renew',
        'status',
        'contract_id',
        'auto_maintenance_hestia',
        'maintenance_requested_at',
        'maintenance_confirmed_at',
        'last_backup_status',
        'last_backup_at',
        'last_backup_path',
    ];

    protected $casts = [
        'service_type'  => ServiceType::class,
        'billing_cycle' => BillingCycle::class,
        'status'          => ClientServiceStatus::class,
        'amount'          => 'decimal:2',
        'provider_cost'   => 'decimal:2',
        'management_fee'  => 'decimal:2',
        'starts_at'     => 'date',
        'ends_at'       => 'date',
        'auto_renew'    => 'boolean',
        'auto_maintenance_hestia'  => 'boolean',
        'maintenance_requested_at' => 'datetime',
        'maintenance_confirmed_at' => 'datetime',
        'last_backup_at'           => 'datetime',
    ];

    /**
     * true si ya se pidió confirmación de mantenimiento este mes calendario
     * — mismo criterio que GenerateMonthlyServiceCharges usa para no cobrar
     * dos veces, así el ciclo se resetea solo sin limpiar nada a mano.
     */
    public function maintenanceRequestedThisMonth(): bool
    {
        return $this->maintenance_requested_at?->isSameMonth(now()) ?? false;
    }

    public function maintenanceConfirmedThisMonth(): bool
    {
        return $this->maintenance_confirmed_at?->isSameMonth(now()) ?? false;
    }

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

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function charges(): MorphMany
    {
        return $this->morphMany(Charge::class, 'chargeable');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable')->latest();
    }

    /**
     * Historial de cambios de `amount` — se lee de audit_logs (trait Auditable),
     * no de una tabla propia. Filtra los registros "updated" que tocaron `amount`.
     */
    public function priceHistory()
    {
        return AuditLog::where('auditable_type', static::class)
            ->where('auditable_id', $this->id)
            ->where('action', 'updated')
            ->whereRaw("JSON_CONTAINS_PATH(new_values, 'one', '$.amount')")
            ->latest()
            ->get();
    }
}
