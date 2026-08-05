<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProvisionHistoriasInstance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public readonly string $tenantId,
        public readonly string $slug,
        public readonly string $publicDomain,
        public readonly string $adminName,
        public readonly string $adminEmail,
        public readonly string $adminPassword,
    ) {}

    public function handle(): void
    {
        Log::info("Starting {$this->slug} provisioning via direct command", [
            'tenant_id'     => $this->tenantId,
            'slug'          => $this->slug,
            'public_domain' => $this->publicDomain,
        ]);

        try {
            $exitCode = Artisan::call("provision:{$this->slug}-direct", [
                'tenant_id'      => $this->tenantId,
                'admin_name'     => $this->adminName,
                'admin_email'    => $this->adminEmail,
                'admin_password' => $this->adminPassword,
            ]);

            $output = Artisan::output();

            if ($exitCode === 0) {
                Log::info("{$this->slug} instance provisioned successfully", [
                    'tenant_id' => $this->tenantId,
                    'database'  => "historias_{$this->tenantId}",
                    'output'    => $output,
                ]);
            } else {
                Log::error("{$this->slug} provisioning failed", [
                    'tenant_id' => $this->tenantId,
                    'exit_code' => $exitCode,
                    'output'    => $output,
                ]);
                $this->fail("{$this->slug} provisioning failed with exit code: {$exitCode}");
            }

        } catch (\Exception $e) {
            Log::error("{$this->slug} provisioning exception", [
                'tenant_id' => $this->tenantId,
                'error'     => $e->getMessage(),
            ]);
            $this->fail("{$this->slug} provisioning failed: {$e->getMessage()}");
        }

        try {
            Mail::to($this->adminEmail)
                ->send(new \App\Mail\HistoriasClinicasWelcomeMail(
                    $this->adminName,
                    $this->adminEmail,
                    $this->tenantId,
                    $this->adminPassword,
                    $this->publicDomain,
                ));
        } catch (\Throwable $e) {
            Log::error('HistoriasClinicasWelcomeMail failed', [
                'tenant_id' => $this->tenantId,
                'error'     => $e->getMessage(),
            ]);
        }
    }
}
