<?php

namespace App\Contracts;

interface InvoicingProviderInterface
{
    /**
     * Emite un comprobante fiscal.
     *
     * @param array $data ['type','amount','customer_name','customer_cuit','items',...]
     * @return array ['number','cae','cae_expiration','pdf_url',...]
     *
     * @throws \App\Exceptions\IntegrationNotReadyException si el proveedor todavía no está operativo.
     */
    public function issueInvoice(array $data): array;

    /**
     * Verifica que la configuración fiscal (CUIT, certificado, ambiente) sea válida.
     *
     * @return array{success: bool, message: string}
     */
    public function testConnection(): array;
}
