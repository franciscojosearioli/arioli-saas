<?php

namespace App\Http\Controllers;

use App\Jobs\RunMaintenanceBackup;
use App\Models\ClientService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Pantallas públicas firmadas (temporarySignedRoute) que dispara el mail
 * MaintenanceConfirmationRequestMail — mismo principio de confianza que ya
 * usa HostingCredentialController/SignatureController: el link firmado ES
 * la autenticación, no hace falta que el cliente tenga sesión de portal.
 */
class MaintenanceConfirmationController extends Controller
{
    public function confirm(Request $request, ClientService $service)
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($service->auto_maintenance_hestia, 404);

        if ($service->maintenanceConfirmedThisMonth()) {
            return view('maintenance.already-confirmed', ['service' => $service]);
        }

        $service->update(['maintenance_confirmed_at' => now()]);

        RunMaintenanceBackup::dispatch($service);

        return view('maintenance.confirmed', ['service' => $service]);
    }

    public function downloadBackup(Request $request, ClientService $service): Response|\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        abort_unless($request->hasValidSignature(), 403);
        abort_unless($service->last_backup_path, 404);

        $path = storage_path('app/private/backups/' . $service->last_backup_path);

        abort_unless(file_exists($path), 404, 'El backup ya no está disponible — pedí uno nuevo desde el mantenimiento del próximo mes.');

        return response()->download($path, basename($path));
    }
}
