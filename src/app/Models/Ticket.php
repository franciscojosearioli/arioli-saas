<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Ticket extends Model
{
    protected $fillable = [
        'tenant_id',
        'client_id',
        'user_id',
        'title',
        'description',
        'status',
        'priority',
        'category',
        'admin_notes',
        'resolved_at',
        'assigned_to',
        'related_type',
        'related_id',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    /**
     * Relación con el tenant (cliente)
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(\Stancl\Tenancy\Database\Models\Tenant::class, 'tenant_id', 'id');
    }

    /**
     * Relación con el cliente (portal de Hosting/Dominio, sin licencia)
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relación con el usuario que creó el ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Activo puntual del cliente al que refiere el ticket (Hosting,
     * ClientDomain, ClientService...) — opcional, para que soporte sepa de
     * qué está hablando sin tener que preguntarlo.
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relación con el administrador asignado
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scopes
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['abierto', 'en_progreso', 'esperando_cliente']);
    }

    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resuelto', 'cerrado']);
    }

    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope genérico por dueño del portal — un User siempre tiene tenant_id
     * XOR client_id, nunca ambos (ver User::client()/tenant()).
     */
    public function scopeForPortalUser($query, User $user)
    {
        return $user->client_id
            ? $query->where('client_id', $user->client_id)
            : $query->where('tenant_id', $user->tenant_id);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Accessors
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'abierto' => 'badge-red',
            'en_progreso' => 'badge-blue',
            'esperando_cliente' => 'badge-yellow',
            'resuelto' => 'badge-green',
            'cerrado' => 'badge-gray',
            default => 'badge-gray',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'baja' => 'badge-green',
            'media' => 'badge-blue',
            'alta' => 'badge-yellow',
            'critica' => 'badge-red',
            default => 'badge-blue',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'abierto' => 'Abierto',
            'en_progreso' => 'En Progreso',
            'esperando_cliente' => 'Esperando Cliente',
            'resuelto' => 'Resuelto',
            'cerrado' => 'Cerrado',
            default => 'Desconocido',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            'baja' => 'Baja',
            'media' => 'Media',
            'alta' => 'Alta',
            'critica' => 'Crítica',
            default => 'Media',
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match($this->category) {
            'tecnico' => 'Técnico',
            'facturacion' => 'Facturación',
            'configuracion' => 'Configuración',
            'otro' => 'Otro',
            default => 'Técnico',
        };
    }
}