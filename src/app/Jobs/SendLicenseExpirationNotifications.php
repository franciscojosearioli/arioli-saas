<?php

namespace App\Jobs;

use App\Models\License;
use App\Mail\LicenseExpirationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendLicenseExpirationNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $daysToNotify = [30, 15, 7];

        foreach ($daysToNotify as $days) {
            $licenses = License::with(['plan.product'])
                ->where('active', true)
                ->whereDate('expires_at', now()->addDays($days)->toDateString())
                ->get();

            foreach ($licenses as $license) {
                $user = \App\Models\User::where('tenant_id', $license->tenant_id)
                    ->whereNotNull('tenant_id')
                    ->first();

                if (!$user) {
                    Log::warning('No user found for tenant', ['tenant_id' => $license->tenant_id]);
                    continue;
                }

                try {
                    Mail::to($user->email)
                        ->send(new LicenseExpirationMail($license, $user, $days));
                        
                    \App\Support\NotificationHelper::licenseExpiring($license, $days);

                    Log::info('Expiration notification sent', [
                        'tenant_id' => $license->tenant_id,
                        'email'     => $user->email,
                        'days'      => $days,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Expiration notification error', [
                        'tenant_id' => $license->tenant_id,
                        'message'   => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}