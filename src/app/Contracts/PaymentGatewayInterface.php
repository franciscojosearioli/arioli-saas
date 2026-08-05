<?php

namespace App\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Crea una preferencia/orden de pago en el proveedor.
     *
     * @param array $data ['title','amount','currency','payer_name','payer_email','external_reference',...]
     * @return array ['id', 'checkout_url', ...]
     */
    public function createPreference(array $data): array;

    /**
     * Consulta el estado de un pago existente en el proveedor.
     *
     * @return array ['id','status','amount','external_reference',...]
     */
    public function getPayment(string $paymentId): array;

    /**
     * Verifica que las credenciales configuradas funcionen contra el proveedor.
     *
     * @return array{success: bool, message: string}
     */
    public function testConnection(): array;
}
