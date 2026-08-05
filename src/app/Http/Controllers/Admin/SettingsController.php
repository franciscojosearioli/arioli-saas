<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\Invoicing\InvoicingProviderManager;
use App\Services\Payments\PaymentGatewayManager;
use App\Services\Signatures\SignatureProviderManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use InvalidArgumentException;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('manage-settings');

        $tabs   = config('settings_tabs');
        $active = $request->query('tab', array_key_first($tabs));

        if (! array_key_exists($active, $tabs)) {
            $active = array_key_first($tabs);
        }

        $data = [
            'tabs'   => $tabs,
            'active' => $active,
        ];

        if ($active === 'auditoria') {
            $data['logs'] = AuditLog::with('user')->latest('created_at')->paginate(20);
        } else {
            $data['settings'] = Setting::group($active)->keyBy('key');
        }

        return view('admin.settings.index', $data);
    }

    public function update(Request $request, string $group): RedirectResponse
    {
        Gate::authorize('manage-settings');

        $validator = match ($group) {
            'empresa'       => $this->validateEmpresa($request),
            'mercadopago'   => $this->validateMercadoPago($request),
            'afip'          => $this->validateAfip($request),
            'firma_digital' => $this->validateFirmaDigital($request),
            'correos'       => $this->validateCorreos($request),
            'horas'         => $this->validateHoras($request),
            default         => throw new InvalidArgumentException("Grupo de configuración desconocido: [{$group}]."),
        };

        foreach ($validator as $key => $value) {
            if ($value === null) {
                continue;
            }

            $isEncrypted = in_array($key, $this->encryptedKeysFor($group), true);
            Setting::set("{$group}.{$key}", (string) $value, $group, $isEncrypted);
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }

    public function testConnection(Request $request, string $group): JsonResponse
    {
        Gate::authorize('manage-settings');

        $result = match ($group) {
            'mercadopago' => PaymentGatewayManager::driver('mercadopago')->testConnection(),
            'afip'        => InvoicingProviderManager::driver('afip')->testConnection(),
            'firma_digital' => SignatureProviderManager::driver()->testConnection(),
            'correos'     => $this->testSmtpConnection(),
            default       => ['success' => false, 'message' => 'Esta pestaña todavía no tiene prueba de conexión.'],
        };

        return response()->json($result);
    }

    private function validateEmpresa(Request $request): array
    {
        $data = $request->validate([
            'nombre'          => 'nullable|string|max:255',
            'razon_social'    => 'nullable|string|max:255',
            'cuit'            => 'nullable|string|max:20',
            'direccion'       => 'nullable|string|max:255',
            'telefono'        => 'nullable|string|max:50',
            'email'           => 'nullable|email|max:255',
            'sitio_web'       => 'nullable|string|max:255',
            'logo'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('empresa', 'public');
        }
        unset($data['logo']);

        return $data;
    }

    private function validateMercadoPago(Request $request): array
    {
        return $request->validate([
            'mode'               => 'nullable|in:test,production',
            'public_key_test'    => 'nullable|string|max:255',
            'access_token_test'  => 'nullable|string|max:255',
            'public_key_prod'    => 'nullable|string|max:255',
            'access_token_prod'  => 'nullable|string|max:255',
            'webhook_secret'     => 'nullable|string|max:255',
            'fee_percent'        => 'nullable|numeric|min:0|max:100',
            'transfer_alias'     => 'nullable|string|max:255',
        ]);
    }

    private function validateAfip(Request $request): array
    {
        $data = $request->validate([
            'cuit'             => 'nullable|string|max:20',
            'razon_social'     => 'nullable|string|max:255',
            'nombre_comercial' => 'nullable|string|max:255',
            'punto_venta'     => 'nullable|string|max:10',
            'condicion_iva'   => 'nullable|string|max:100',
            'ambiente'        => 'nullable|in:testing,production',
            'certificado'     => 'nullable|file|max:1024',
            'clave_privada'   => 'nullable|file|max:1024',
        ]);

        // Certificado y clave nunca van a un disco público.
        if ($request->hasFile('certificado')) {
            $data['certificado_path'] = $request->file('certificado')->store('afip', 'local');
        }
        if ($request->hasFile('clave_privada')) {
            $data['clave_privada_path'] = $request->file('clave_privada')->store('afip', 'local');
        }
        unset($data['certificado'], $data['clave_privada']);

        return $data;
    }

    private function validateFirmaDigital(Request $request): array
    {
        return $request->validate([
            'driver'  => 'nullable|in:self_hosted,manual',
            'api_key' => 'nullable|string|max:255',
        ]);
    }

    private function validateCorreos(Request $request): array
    {
        return $request->validate([
            'mail_mailer'       => 'nullable|string|max:50',
            'mail_host'         => 'nullable|string|max:255',
            'mail_port'         => 'nullable|numeric',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:255',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name'    => 'nullable|string|max:255',
        ]);
    }

    private function validateHoras(Request $request): array
    {
        return $request->validate([
            'hourly_rate_default' => 'nullable|numeric|min:0',
        ]);
    }

    private function encryptedKeysFor(string $group): array
    {
        return match ($group) {
            'mercadopago'   => ['access_token_test', 'access_token_prod', 'webhook_secret'],
            'afip'          => ['clave_privada_path'],
            'firma_digital' => ['api_key'],
            'correos'       => ['mail_password'],
            default         => [],
        };
    }

    private function testSmtpConnection(): array
    {
        $host = Setting::get('correos.mail_host');
        $port = Setting::get('correos.mail_port');

        if (! $host || ! $port) {
            return ['success' => false, 'message' => 'Cargá host y puerto SMTP antes de probar la conexión.'];
        }

        $connection = @fsockopen($host, (int) $port, $errno, $errstr, 5);

        if (! $connection) {
            return ['success' => false, 'message' => "No se pudo conectar a {$host}:{$port} ({$errstr})."];
        }

        fclose($connection);

        return ['success' => true, 'message' => "Conexión TCP a {$host}:{$port} exitosa."];
    }
}
