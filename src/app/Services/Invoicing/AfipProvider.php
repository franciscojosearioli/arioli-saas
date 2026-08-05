<?php

namespace App\Services\Invoicing;

use App\Contracts\InvoicingProviderInterface;
use App\Exceptions\AfipException;
use App\Models\Invoice;
use App\Models\Setting;
use App\Services\Invoicing\Afip\WsfeClient;

/**
 * Driver real de facturación electrónica AFIP (WSAA + WSFEv1) — mientras el
 * emisor sea Monotributista (confirmado con el usuario), siempre emite
 * Factura C (cbte_tipo=11), sin discriminar IVA. Ver WsaaClient/WsfeClient
 * para el detalle de las llamadas.
 */
class AfipProvider implements InvoicingProviderInterface
{
    private const CBTE_TIPO_FACTURA_C = 11;

    public function __construct(private readonly WsfeClient $wsfe) {}

    public function testConnection(): array
    {
        if (! Setting::get('afip.cuit') || ! Setting::get('afip.certificado_path') || ! Setting::get('afip.clave_privada_path')) {
            return [
                'success' => false,
                'message' => 'Faltan datos: cargá CUIT, certificado y clave privada en esta pestaña.',
            ];
        }

        try {
            $result = $this->wsfe->dummy();
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Error al conectar con AFIP: ' . $e->getMessage()];
        }

        $allOk = $result['app_server'] === 'OK' && $result['db_server'] === 'OK' && $result['auth_server'] === 'OK';

        return [
            'success' => $allOk,
            'message' => $allOk
                ? 'Conexión OK con AFIP — AppServer, DbServer y AuthServer disponibles.'
                : "AFIP respondió con problemas — AppServer={$result['app_server']}, DbServer={$result['db_server']}, AuthServer={$result['auth_server']}.",
        ];
    }

    /**
     * @param array{invoice: Invoice} $data
     */
    public function issueInvoice(array $data): array
    {
        /** @var Invoice $invoice */
        $invoice = $data['invoice'];

        if (strtoupper($invoice->currency) !== 'ARS') {
            throw new AfipException("La facturación electrónica AFIP solo soporta pesos (ARS) por ahora — este comprobante está en {$invoice->currency}.");
        }

        $ptoVta = (int) Setting::get('afip.punto_venta', 0);

        if (! $ptoVta) {
            throw new AfipException('Falta configurar el punto de venta en la pestaña AFIP.');
        }

        $cuit = $invoice->client?->cuit ?: $invoice->customer_cuit;

        if ($cuit) {
            $docTipo = 80;
            $docNro = preg_replace('/\D/', '', $cuit);
        } else {
            $docTipo = 99;
            $docNro = '0';
        }

        $proximoNumero = $this->wsfe->ultimoAutorizado($ptoVta, self::CBTE_TIPO_FACTURA_C) + 1;

        $importe = number_format((float) $invoice->amount, 2, '.', '');

        $resultado = $this->wsfe->solicitarCae([
            'pto_vta'   => $ptoVta,
            'cbte_tipo' => self::CBTE_TIPO_FACTURA_C,
            'doc_tipo'  => $docTipo,
            'doc_nro'   => $docNro,
            'cbte_nro'  => $proximoNumero,
            'fecha'     => now()->format('Ymd'),
            'imp_total' => $importe,
            'imp_neto'  => $importe,
            'imp_iva'   => '0.00',
        ]);

        return [
            'number'          => sprintf('%04d-%08d', $ptoVta, $proximoNumero),
            'cbte_tipo'       => self::CBTE_TIPO_FACTURA_C,
            'doc_tipo'        => $docTipo,
            'doc_nro'         => $docNro,
            'cae'             => $resultado['cae'],
            'cae_vencimiento' => $resultado['cae_vencimiento'],
        ];
    }
}
