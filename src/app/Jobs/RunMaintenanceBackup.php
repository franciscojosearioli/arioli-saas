<?php

namespace App\Jobs;

use App\Enums\ClientEventType;
use App\Mail\MaintenanceBackupReadyMail;
use App\Models\ClientEvent;
use App\Models\ClientService;
use App\Services\Clients\MaintenanceChargeService;
use App\Services\Hosting\HestiaCliClient;
use App\Support\NotificationHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

/**
 * Se dispara cuando el cliente confirma el mantenimiento de este mes
 * (MaintenanceConfirmationController::confirm()). Corre el backup real en
 * HestiaCP, lo trae a Arioli, avisa por mail con el link de descarga y
 * recién ahí genera el cobro — en ese orden, nunca cobra si el backup falla.
 */
class RunMaintenanceBackup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 1500;
    public int $tries = 1;

    public function __construct(public readonly ClientService $service) {}

    public function handle(HestiaCliClient $hestia, MaintenanceChargeService $charges): void
    {
        $service = $this->service;
        $account = $service->project?->hosting?->account;

        if (! $account || $account->provider !== 'hestiacp') {
            $this->fail('El servicio no tiene un proyecto con hosting HestiaCP vinculado — no se puede hacer el backup.');

            return;
        }

        $username = $account->remote_username;

        $backup = $hestia->backupUser($username);

        if (! $backup['success']) {
            $this->markFailed($service, "No se pudo generar el backup en HestiaCP: {$backup['output']}");

            return;
        }

        $dir = storage_path('app/private/backups');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = "{$username}-" . now()->format('Y-m-d_His') . '.tar';
        $localPath = "{$dir}/{$filename}";

        $fetch = $hestia->fetchLatestBackupFile($username, $localPath);

        if (! $fetch['success']) {
            $this->markFailed($service, "El backup se generó en HestiaCP pero no se pudo traer a Arioli: {$fetch['output']}");

            return;
        }

        // Se borra la copia local anterior de este mismo servicio — solo se
        // conserva la del mes actual, no se acumulan backups viejos en disco.
        if ($service->last_backup_path) {
            $previousPath = "{$dir}/{$service->last_backup_path}";

            if (file_exists($previousPath)) {
                unlink($previousPath);
            }
        }

        $service->update([
            'last_backup_status' => 'done',
            'last_backup_at'     => now(),
            'last_backup_path'   => $filename,
        ]);

        $client = $service->client;
        $contact = $client->contacts->firstWhere('is_primary', true) ?? $client->contacts->first();

        if ($contact?->email) {
            $downloadUrl = URL::temporarySignedRoute(
                'maintenance.download-backup',
                now()->addDays(7),
                ['service' => $service->id],
            );

            try {
                Mail::to($contact->email)->send(new MaintenanceBackupReadyMail($service, $contact, $downloadUrl));
            } catch (\Throwable $e) {
                Log::error('RunMaintenanceBackup: fallo al enviar el mail de backup listo', [
                    'service_id' => $service->id,
                    'message'    => $e->getMessage(),
                ]);
            }
        }

        ClientEvent::log($client, 'Backup de mantenimiento mensual completado y enviado al cliente', ClientEventType::Service, $service);

        $charges->chargeFor($service);
    }

    private function markFailed(ClientService $service, string $message): void
    {
        $service->update(['last_backup_status' => 'failed', 'last_backup_at' => now()]);

        ClientEvent::log($service->client, "Error en el backup de mantenimiento: {$message}", ClientEventType::Service, $service);

        NotificationHelper::maintenanceBackupFailed($service, $message);

        Log::error('RunMaintenanceBackup: falló', ['service_id' => $service->id, 'message' => $message]);
    }
}
