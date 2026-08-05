<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class UpdateAppInstance implements ShouldQueue
{
    use Queueable;

    public int $tries   = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly string $tenantId,
        public readonly string $product,
        public readonly string $targetVersion,
    ) {}

    public function handle(): void
    {
        $exitCode = Artisan::call("update:{$this->product}-direct", [
            'tenant_id' => $this->tenantId,
            'version'   => $this->targetVersion,
        ]);

        if ($exitCode !== 0) {
            $output = Artisan::output();
            Log::error("update:{$this->product}-direct failed", [
                'tenant_id' => $this->tenantId,
                'version'   => $this->targetVersion,
                'output'    => $output,
            ]);
            $this->fail("update:{$this->product}-direct exited with code {$exitCode}");
        }

        Log::info('App tenant updated', [
            'product'   => $this->product,
            'tenant_id' => $this->tenantId,
            'version'   => $this->targetVersion,
        ]);
    }
}
