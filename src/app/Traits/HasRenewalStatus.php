<?php

namespace App\Traits;

/**
 * Estado de vencimiento calculado puramente desde expires_at — independiente del
 * campo `status` operativo (activo/suspendido/etc.) que sigue cargando el admin a
 * mano. No pisa ni reemplaza ese campo, solo agrega una vista derivada para badges
 * y alertas de vencimiento.
 */
trait HasRenewalStatus
{
    public function renewalStatusLabel(): string
    {
        $expiresAt = $this->expiresAt();

        if (! $expiresAt) {
            return 'Sin vencimiento';
        }

        if ($expiresAt->isPast()) {
            return 'Vencido';
        }

        if (now()->diffInDays($expiresAt, false) <= 30) {
            return 'Próximo a vencer';
        }

        return 'Activo';
    }

    public function renewalStatusColor(): string
    {
        return match ($this->renewalStatusLabel()) {
            'Vencido' => 'red',
            'Próximo a vencer' => 'amber',
            'Activo' => 'green',
            default => 'gray',
        };
    }
}
