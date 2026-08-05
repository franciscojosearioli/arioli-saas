<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

/**
 * Trait opt-in: cualquier modelo que lo use queda auditado automáticamente
 * (created/updated/deleted) en la tabla audit_logs, sin tocar su lógica propia.
 */
trait Auditable
{
    protected static function bootAuditable(): void
    {
        static::created(fn ($model) => $model->writeAuditLog('created', [], $model->getAttributes()));

        static::updated(function ($model) {
            $changes = $model->getChanges();
            unset($changes['updated_at']);

            if (empty($changes)) {
                return;
            }

            $old = collect($model->getOriginal())->only(array_keys($changes))->toArray();
            $model->writeAuditLog('updated', $old, $changes);
        });

        static::deleted(fn ($model) => $model->writeAuditLog('deleted', $model->getAttributes(), []));
    }

    protected function writeAuditLog(string $action, array $oldValues, array $newValues): void
    {
        $user = Auth::user();

        AuditLog::create([
            'user_id'        => $user?->id,
            'user_email'     => $user?->email,
            'action'         => $action,
            'auditable_type' => static::class,
            'auditable_id'   => $this->getKey(),
            'old_values'     => $this->redactSensitive($oldValues),
            'new_values'     => $this->redactSensitive($newValues),
            'ip_address'     => request()?->ip(),
            'user_agent'     => request()?->userAgent(),
        ]);
    }

    /**
     * Nunca persistir valores encriptados/sensibles en texto plano en el log.
     */
    protected function redactSensitive(array $values): array
    {
        if (($values['is_encrypted'] ?? false) && array_key_exists('value', $values)) {
            $values['value'] = '••••••••';
        }

        return $values;
    }
}
