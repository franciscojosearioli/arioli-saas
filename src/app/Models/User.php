<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ClienteResetPasswordNotification;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'client_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Relación con tenant
    public function tenant()
    {
        return $this->belongsTo(\Stancl\Tenancy\Database\Models\Tenant::class, 'tenant_id', 'id');
    }

    // Relación con Client (portal de clientes de Hosting/Dominio, sin licencia SaaS)
    public function client()
    {
        return $this->belongsTo(\App\Models\Client::class);
    }

    // Scope: usuarios del admin central
    public function scopeAdminUsers($query)
    {
        return $query->whereNull('tenant_id');
    }

    // Scope: usuarios de un tenant específico
    public function scopeForTenant($query, string $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function sendPasswordResetNotification($token): void
    {
        if ($this->tenant_id) {
            $this->notify(new \App\Notifications\ClienteResetPasswordNotification($token));
        } else {
            $this->notify(new \App\Notifications\AdminResetPasswordNotification($token));
        }
    }

    // Relaciones con tickets
    public function tickets()
    {
        return $this->hasMany(\App\Models\Ticket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'assigned_to');
    }
}