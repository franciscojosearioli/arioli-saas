<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function (Model $model) {
            self::audit('created', $model);
        });

        static::updated(function (Model $model) {
            self::audit('updated', $model, $model->getChanges());
        });

        static::deleted(function (Model $model) {
            self::audit('deleted', $model);
        });
    }

    protected static function audit($event, $model, $changes = [])
    {
        $description = self::generateDescription($event, $model);

        $auditLog = AuditLog::create([
            'description'  => $description,
            'subject_id'   => $model->id ?? null,
            'subject_type' => sprintf('%s#%s', get_class($model), $model->id) ?? null,
            'user_id'      => auth()->id() ?? null,
            'properties'   => $changes ?: $model->toArray(),
            'host'         => request()->ip() ?? null,
        ]);

        // Adjuntar el log de auditoría al usuario autenticado
        if ($auditLog && auth()->check()) {
            $user = auth()->user();
            $user->auditLogs()->attach($auditLog->id, ['read' => false]);
        }
    }

    protected static function generateDescription($event, $model)
    {
        $user = auth()->user() ? auth()->user()->name : 'Admin';
        $modelName = class_basename($model);
        $action = '';

        switch ($event) {
            case 'created':
                $action = "creó un $modelName";
                break;
            case 'updated':
                $action = "actualizó un $modelName";
                break;
            case 'deleted':
                $action = "eliminó un $modelName";
                break;
        }

        return sprintf('%s %s', $user, $action);
    }
}