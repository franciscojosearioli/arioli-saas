<?php

namespace App\Contracts;

interface SignatureProviderInterface
{
    /**
     * Envía un documento a firmar.
     *
     * @param array $document ['title','content','signer_name','signer_email',...]
     * @return array ['external_id','status','signing_url',...]
     *
     * @throws \App\Exceptions\IntegrationNotReadyException si el proveedor todavía no está operativo.
     */
    public function sendForSignature(array $document): array;

    /**
     * Consulta el estado de una firma en curso.
     *
     * @return array ['external_id','status','signed_at',...]
     */
    public function getStatus(string $externalId): array;

    /**
     * Verifica que el proveedor esté configurado y operativo.
     *
     * @return array{success: bool, message: string}
     */
    public function testConnection(): array;
}
