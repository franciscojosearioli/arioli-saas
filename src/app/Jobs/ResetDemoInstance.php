<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ResetDemoInstance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 2;
    public int $backoff = 60;

    public function __construct(public readonly string $product) {}

    public function handle(): void
    {
        Log::info('ResetDemoInstance started', ['product' => $this->product]);

        $exitCode = Artisan::call('demo:reset', ['product' => $this->product]);

        if ($exitCode !== 0) {
            Log::error('ResetDemoInstance failed', ['product' => $this->product, 'exit_code' => $exitCode]);
            $this->fail("demo:reset {$this->product} failed with exit code {$exitCode}");
            return;
        }

        Log::info('ResetDemoInstance completed', ['product' => $this->product]);
    }
}
