<?php

namespace App\Services\Invoicing\Afip;

use App\Exceptions\AfipException;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

/**
 * Autenticación WSAA (login con certificado) — sin ext-soap ni paquetes
 * nuevos: arma el TRA a mano, lo firma invocando el binario `openssl cms
 * -sign` (vía Process) y postea el sobre SOAP con Http (Guzzle). El Token/
 * Sign devuelto es válido 12hs y se cachea en Setting para no pedir uno
 * nuevo en cada request (AFIP puede bloquear logins repetidos).
 */
class WsaaClient
{
    private const URL_PROD = 'https://wsaa.afip.gov.ar/ws/services/LoginCms';
    private const URL_TEST = 'https://wsaahomo.afip.gov.ar/ws/services/LoginCms';

    public function getCredentials(string $service = 'wsfe'): array
    {
        $token = Setting::get('afip.wsaa_token');
        $sign = Setting::get('afip.wsaa_sign');
        $expiration = Setting::get('afip.wsaa_expiration');

        if ($token && $sign && $expiration && now()->lt(\Illuminate\Support\Carbon::parse($expiration))) {
            return ['token' => $token, 'sign' => $sign];
        }

        return $this->login($service);
    }

    private function login(string $service): array
    {
        $certPath = Setting::get('afip.certificado_path');
        $keyPath = Setting::get('afip.clave_privada_path');

        if (! $certPath || ! $keyPath) {
            throw new AfipException('Faltan el certificado o la clave privada de AFIP en Configuración.');
        }

        $signedCms = $this->signTra(
            $this->buildTra($service),
            Storage::disk('local')->path($certPath),
            Storage::disk('local')->path($keyPath),
        );

        $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction'   => '',
            ])
            ->withBody($this->buildLoginEnvelope($signedCms), 'text/xml')
            ->post($this->url());

        if ($response->failed()) {
            if (preg_match('/<faultstring>(.*?)<\/faultstring>/s', $response->body(), $fault)) {
                throw new AfipException('WSAA rechazó el login: ' . html_entity_decode($fault[1]));
            }

            throw new AfipException("Error de red al conectar con WSAA: HTTP {$response->status()} — {$response->body()}");
        }

        if (! preg_match('/<loginCmsReturn>(.*?)<\/loginCmsReturn>/s', $response->body(), $matches)) {
            throw new AfipException('Respuesta inesperada de WSAA: ' . $response->body());
        }

        $loginTicket = simplexml_load_string(html_entity_decode($matches[1]));

        if (! $loginTicket) {
            throw new AfipException('No se pudo interpretar la respuesta de WSAA.');
        }

        $token = (string) $loginTicket->credentials->token;
        $sign = (string) $loginTicket->credentials->sign;
        $expirationTime = (string) $loginTicket->header->expirationTime;

        Setting::set('afip.wsaa_token', $token, 'afip', true);
        Setting::set('afip.wsaa_sign', $sign, 'afip', true);
        Setting::set('afip.wsaa_expiration', $expirationTime, 'afip', false);

        return ['token' => $token, 'sign' => $sign];
    }

    private function url(): string
    {
        return Setting::get('afip.ambiente', 'production') === 'testing' ? self::URL_TEST : self::URL_PROD;
    }

    private function buildTra(string $service): string
    {
        $uniqueId = now()->timestamp;
        $generationTime = now()->subMinutes(10)->format('Y-m-d\TH:i:sP');
        $expirationTime = now()->addMinutes(10)->format('Y-m-d\TH:i:sP');

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<loginTicketRequest version="1.0">
    <header>
        <uniqueId>{$uniqueId}</uniqueId>
        <generationTime>{$generationTime}</generationTime>
        <expirationTime>{$expirationTime}</expirationTime>
    </header>
    <service>{$service}</service>
</loginTicketRequest>
XML;
    }

    private function signTra(string $tra, string $certPath, string $keyPath): string
    {
        $traPath = tempnam(sys_get_temp_dir(), 'afip_tra_');
        $cmsPath = tempnam(sys_get_temp_dir(), 'afip_cms_');

        file_put_contents($traPath, $tra);

        $result = Process::run([
            'openssl', 'cms', '-sign',
            '-in', $traPath,
            '-signer', $certPath,
            '-inkey', $keyPath,
            '-nodetach',
            '-outform', 'DER',
            '-out', $cmsPath,
        ]);

        @unlink($traPath);

        if ($result->failed()) {
            @unlink($cmsPath);

            throw new AfipException('No se pudo firmar el TRA con el certificado de AFIP: ' . $result->errorOutput());
        }

        $cmsContent = file_get_contents($cmsPath);
        @unlink($cmsPath);

        return base64_encode($cmsContent);
    }

    private function buildLoginEnvelope(string $signedCms): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:wsaa="http://wsaa.view.sua.dvadac.desein.afip.gov">
    <soapenv:Header/>
    <soapenv:Body>
        <wsaa:loginCms>
            <wsaa:in0>{$signedCms}</wsaa:in0>
        </wsaa:loginCms>
    </soapenv:Body>
</soapenv:Envelope>
XML;
    }
}
