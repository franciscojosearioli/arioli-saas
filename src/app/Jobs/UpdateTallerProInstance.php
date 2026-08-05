<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Alias mantenido para jobs ya encolados. Toda lógica nueva usa UpdateAppInstance.
 */
class UpdateTallerProInstance implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly string $tenantId,
        public readonly string $targetVersion,
    ) {}

    public function handle(): void
    {
        UpdateAppInstance::dispatch($this->tenantId, 'tallerpro', $this->targetVersion);
    }
}
