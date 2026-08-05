<?php

namespace App\Services\Invoicing\Afip;

use App\Exceptions\AfipException;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

/**
 * Llamadas WSFEv1 armadas a mano (sin ext-soap) — solo las 3 operaciones que
 * necesita este sistema: FEDummy (ping, sin efectos), FECompUltimoAutorizado
 * (lectura, sin efectos) y FECAESolicitar (autoriza un comprobante real —
 * único punto del sistema que puede crear una factura fiscal irreversible).
 */
class WsfeClient
{
    private const URL_PROD = 'https://servicios1.afip.gov.ar/wsfev1/service.asmx';
    private const URL_TEST = 'https://wswhomo.afip.gov.ar/wsfev1/service.asmx';

    public function __construct(private readonly WsaaClient $wsaa) {}

    public function dummy(): array
    {
        $envelope = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ar="http://ar.gov.afip.dif.FEV1/">
    <soapenv:Header/>
    <soapenv:Body>
        <ar:FEDummy/>
    </soapenv:Body>
</soapenv:Envelope>
XML;

        $result = $this->extract($this->post($envelope, 'FEDummy'), 'FEDummyResult');

        return [
            'app_server'  => (string) $result->AppServer,
            'db_server'   => (string) $result->DbServer,
            'auth_server' => (string) $result->AuthServer,
        ];
    }

    public function ultimoAutorizado(int $ptoVta, int $cbteTipo): int
    {
        $auth = $this->authBlock();

        $envelope = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ar="http://ar.gov.afip.dif.FEV1/">
    <soapenv:Header/>
    <soapenv:Body>
        <ar:FECompUltimoAutorizado>
            {$auth}
            <ar:PtoVta>{$ptoVta}</ar:PtoVta>
            <ar:CbteTipo>{$cbteTipo}</ar:CbteTipo>
        </ar:FECompUltimoAutorizado>
    </soapenv:Body>
</soapenv:Envelope>
XML;

        $result = $this->extract($this->post($envelope, 'FECompUltimoAutorizado'), 'FECompUltimoAutorizadoResult');
        $this->assertNoErrors($result);

        return (int) $result->CbteNro;
    }

    /**
     * Único método que autoriza un comprobante real ante AFIP — irreversible
     * (solo se revierte con una Nota de Crédito, no se puede anular acá).
     *
     * @param array{pto_vta:int,cbte_tipo:int,doc_tipo:int,doc_nro:string,cbte_nro:int,fecha:string,imp_total:string,imp_neto:string,imp_iva:string} $data
     */
    public function solicitarCae(array $data): array
    {
        $auth = $this->authBlock();

        $envelope = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ar="http://ar.gov.afip.dif.FEV1/">
    <soapenv:Header/>
    <soapenv:Body>
        <ar:FECAESolicitar>
            {$auth}
            <ar:FeCAEReq>
                <ar:FeCabReq>
                    <ar:CantReg>1</ar:CantReg>
                    <ar:PtoVta>{$data['pto_vta']}</ar:PtoVta>
                    <ar:CbteTipo>{$data['cbte_tipo']}</ar:CbteTipo>
                </ar:FeCabReq>
                <ar:FeDetReq>
                    <ar:FECAEDetRequest>
                        <ar:Concepto>2</ar:Concepto>
                        <ar:DocTipo>{$data['doc_tipo']}</ar:DocTipo>
                        <ar:DocNro>{$data['doc_nro']}</ar:DocNro>
                        <ar:CbteDesde>{$data['cbte_nro']}</ar:CbteDesde>
                        <ar:CbteHasta>{$data['cbte_nro']}</ar:CbteHasta>
                        <ar:CbteFch>{$data['fecha']}</ar:CbteFch>
                        <ar:FchServDesde>{$data['fecha']}</ar:FchServDesde>
                        <ar:FchServHasta>{$data['fecha']}</ar:FchServHasta>
                        <ar:FchVtoPago>{$data['fecha']}</ar:FchVtoPago>
                        <ar:ImpTotal>{$data['imp_total']}</ar:ImpTotal>
                        <ar:ImpTotConc>0</ar:ImpTotConc>
                        <ar:ImpNeto>{$data['imp_neto']}</ar:ImpNeto>
                        <ar:ImpOpEx>0</ar:ImpOpEx>
                        <ar:ImpIVA>{$data['imp_iva']}</ar:ImpIVA>
                        <ar:ImpTrib>0</ar:ImpTrib>
                        <ar:MonId>PES</ar:MonId>
                        <ar:MonCotiz>1</ar:MonCotiz>
                    </ar:FECAEDetRequest>
                </ar:FeDetReq>
            </ar:FeCAEReq>
        </ar:FECAESolicitar>
    </soapenv:Body>
</soapenv:Envelope>
XML;

        $result = $this->extract($this->post($envelope, 'FECAESolicitar'), 'FECAESolicitarResult');
        $detalle = $result->FeDetResp->FECAEDetResponse ?? null;

        if (! $detalle) {
            $this->assertNoErrors($result);

            throw new AfipException('AFIP no devolvió el detalle del comprobante.');
        }

        if ((string) $detalle->Resultado !== 'A') {
            $observaciones = [];
            foreach ($detalle->Observaciones->Obs ?? [] as $obs) {
                $observaciones[] = "{$obs->Code}: {$obs->Msg}";
            }

            $this->assertNoErrors($result);

            throw new AfipException('AFIP rechazó el comprobante: ' . (implode(' | ', $observaciones) ?: 'sin detalle.'));
        }

        return [
            'cae'             => (string) $detalle->CAE,
            'cae_vencimiento' => (string) $detalle->CAEFchVto,
        ];
    }

    private function authBlock(): string
    {
        $credentials = $this->wsaa->getCredentials();
        $cuit = (string) Setting::get('afip.cuit');

        return <<<XML
<ar:Auth>
    <ar:Token>{$credentials['token']}</ar:Token>
    <ar:Sign>{$credentials['sign']}</ar:Sign>
    <ar:Cuit>{$cuit}</ar:Cuit>
</ar:Auth>
XML;
    }

    private function assertNoErrors(SimpleXMLElement $result): void
    {
        if (! isset($result->Errors->Err)) {
            return;
        }

        $errors = [];
        foreach ($result->Errors->Err as $err) {
            $errors[] = "{$err->Code}: {$err->Msg}";
        }

        throw new AfipException('AFIP devolvió un error: ' . implode(' | ', $errors));
    }

    private function extract(SimpleXMLElement $response, string $resultTag): SimpleXMLElement
    {
        $matches = $response->xpath("//*[local-name()='{$resultTag}']");

        if (empty($matches)) {
            throw new AfipException("Respuesta inesperada de WSFEv1: no se encontró {$resultTag}.");
        }

        return $matches[0];
    }

    private function post(string $envelope, string $soapAction): SimpleXMLElement
    {
        $url = Setting::get('afip.ambiente', 'production') === 'testing' ? self::URL_TEST : self::URL_PROD;

        // WSFEv1 de AFIP negocia TLS con un parámetro Diffie-Hellman viejo y
        // débil que OpenSSL moderno rechaza por defecto ("dh key too small").
        // Se baja el SECLEVEL solo para esta conexión puntual (no afecta
        // ninguna otra request del sistema).
        $response = Http::withHeaders([
                'Content-Type' => 'text/xml; charset=utf-8',
                'SOAPAction'   => "http://ar.gov.afip.dif.FEV1/{$soapAction}",
            ])
            ->withOptions(['curl' => [CURLOPT_SSL_CIPHER_LIST => 'DEFAULT@SECLEVEL=1']])
            ->withBody($envelope, 'text/xml')
            ->post($url);

        if ($response->failed()) {
            if (preg_match('/<faultstring>(.*?)<\/faultstring>/s', $response->body(), $fault)) {
                throw new AfipException('WSFEv1 rechazó la solicitud: ' . html_entity_decode($fault[1]));
            }

            throw new AfipException("Error de red al conectar con WSFEv1: HTTP {$response->status()} — {$response->body()}");
        }

        $xml = simplexml_load_string($response->body());

        if ($xml === false) {
            throw new AfipException('No se pudo interpretar la respuesta de WSFEv1: ' . $response->body());
        }

        return $xml;
    }
}
