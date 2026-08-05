<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $fillable = [
        'tenant_id',
        'client_id',
        'plan_id',
        'starts_at',
        'expires_at',
        'active',
        'license_type',
        'installed_version',
        'custom_domain',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
        'active'     => 'boolean',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function tenant()
    {
        return $this->belongsTo(\App\Models\Tenant::class, 'tenant_id', 'id');
    }

    /**
     * La License pertenece conceptualmente al Client (no el Tenant): si el día
     * de mañana se reprovisiona a otro Tenant, el dueño (client_id) no cambia.
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function domain()
    {
        return $this->hasOne(\Stancl\Tenancy\Database\Models\Domain::class, 'license_id');
    }

    public function isDemo(): bool
    {
        return $this->license_type === 'demo';
    }

    public function scopeNotDemo($query)
    {
        return $query->where(fn($q) => $q->where('license_type', '!=', 'demo')->orWhereNull('license_type'));
    }

    public function isValid(): bool
    {
        if (!$this->active) return false;
        if ($this->starts_at > now()) return false;
        // Demo licenses never expire
        if ($this->isDemo() || $this->expires_at === null) return true;
        return $this->expires_at >= now();
    }

    public function isExpired(): bool
    {
        // Demo licenses never expire
        if ($this->isDemo() || $this->expires_at === null) return false;
        return $this->expires_at < now();
    }

    public function daysRemaining(): ?int
    {
        if ($this->isDemo() || $this->expires_at === null) return null;
        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }

    public function renew(int $planId, int $months): self
    {
        // Si la licencia no venció, extender desde la fecha de vencimiento actual
        // Si ya venció, extender desde hoy
        $startFrom = $this->isExpired() ? now() : $this->expires_at;

        $this->update([
            'plan_id'    => $planId,
            'active'     => true,
            'starts_at'  => now(),
            'expires_at' => $startFrom->copy()->addMonths($months),
        ]);

        return $this;
    }

    // Crear nueva licencia para un tenant y producto.
    // El dominio usa public_domain (no slug) — ej: acme.servis.arioli.dev
    public static function createForTenant(
        string $tenantId,
        int $planId,
        int $months,
        Product $product
    ): self {
        $domain = $tenantId . '.' . $product->public_domain . '.' . config('app.tenant_domain');

        $license = self::create([
            'tenant_id'  => $tenantId,
            'plan_id'    => $planId,
            'active'     => true,
            'starts_at'  => now(),
            'expires_at' => now()->addMonths($months),
        ]);

        \Stancl\Tenancy\Database\Models\Domain::create([
            'domain'     => $domain,
            'tenant_id'  => $tenantId,
            'license_id' => $license->id,
        ]);

        return $license;
    }

    // Renovar licencia: extiende la fecha sin cambiar el dominio
    public static function renewForTenant(
        string $tenantId,
        int $planId,
        int $months
    ): self {
        // Desactivar licencias anteriores del mismo plan/producto
        $plan = Plan::find($planId);
        $productId = $plan->product_id;

        // Buscar licencias activas del mismo producto
        $existingLicense = self::where('tenant_id', $tenantId)
            ->where('active', true)
            ->whereHas('plan', fn($q) => $q->where('product_id', $productId))
            ->first();

        if ($existingLicense) {
            // Renovar la existente
            return $existingLicense->renew($planId, $months);
        }

        // Si no existe, crear nueva (no debería pasar en renovación)
        return self::create([
            'tenant_id'  => $tenantId,
            'plan_id'    => $planId,
            'active'     => true,
            'starts_at'  => now(),
            'expires_at' => now()->addMonths($months),
        ]);
    }
}