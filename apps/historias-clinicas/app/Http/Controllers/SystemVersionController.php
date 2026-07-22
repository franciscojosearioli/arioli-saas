<?php

namespace App\Http\Controllers;

use App\Services\Versioning\VersioningClient;

class SystemVersionController extends Controller
{
    public function index()
    {
        try {
            $status = VersioningClient::fromConfig()->status();
        } catch (\Throwable) {
            $status = null;
        }

        return view('system-version.index', compact('status'));
    }

    public function requestUpdate()
    {
        $tenantId = request()->attributes->get('tenant_id', '');

        if (empty($tenantId)) {
            return back()->with('error', 'No se pudo identificar el tenant para solicitar la actualización.');
        }

        try {
            $ok = VersioningClient::fromConfig()->requestUpdate($tenantId);
        } catch (\Throwable) {
            $ok = false;
        }

        if ($ok) {
            return back()->with('success', 'Actualización iniciada. El sistema se actualizará en unos instantes.');
        }

        return back()->with('error', 'Error al solicitar la actualización. Contactá a soporte.');
    }
}
