<?php

namespace App\Jobs;

use App\Mail\AssetExpirationMail;
use App\Support\NotificationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Generaliza SendLicenseExpirationNotifications a cualquier "activo administrado"
 * (hoy: ClientDomain, Hosting) registrado en config('asset_types') — agregar un
 * activo nuevo (SSL, VPS...) no requiere tocar este Job, solo implementar
 * AssetInterface y sumar la clase al config.
 */
class SendAssetExpirationNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $daysToNotify = [30, 15, 7, 1];

        foreach ($daysToNotify as $days) {
            foreach (config('asset_types', []) as $assetClass) {
                $assets = $assetClass::whereDate('expires_at', now()->addDays($days)->toDateString())->get();

                foreach ($assets as $asset) {
                    $client = $asset->client;

                    if (! $client) {
                        continue;
                    }

                    $contact = $client->contacts()->where('is_primary', true)->first()
                        ?? $client->contacts()->first();

                    try {
                        if ($contact?->email) {
                            Mail::to($contact->email)->send(new AssetExpirationMail($asset, $contact, $days));
                        }

                        NotificationHelper::assetExpiring($asset, $days);

                        Log::info('Asset expiration notification sent', [
                            'asset_type' => $assetClass,
                            'asset_id'   => $asset->getKey(),
                            'client_id'  => $client->id,
                            'days'       => $days,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Asset expiration notification error', [
                            'asset_type' => $assetClass,
                            'asset_id'   => $asset->getKey(),
                            'message'    => $e->getMessage(),
                        ]);
                    }
                }
            }
        }
    }
}
