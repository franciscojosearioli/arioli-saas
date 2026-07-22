<?php

namespace App\Traits;

use App\Exceptions\StaleRecordException;

trait OptimisticLocking
{
    public function updateWithLock(array $attributes, int $currentVersion): void
    {
        $affected = $this->newQuery()
            ->where($this->getKeyName(), $this->getKey())
            ->where('lock_version', $currentVersion)
            ->update(array_merge($attributes, ['lock_version' => $currentVersion + 1]));

        if (!$affected) {
            throw new StaleRecordException(static::class, $this->getKey());
        }

        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
        $this->setAttribute('lock_version', $currentVersion + 1);
        $this->syncOriginal();
    }
}
