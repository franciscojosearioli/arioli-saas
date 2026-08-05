<?php

namespace App\Contracts;

use App\ValueObjects\HostingOperationResult;
use App\ValueObjects\HostingProvisionResult;

interface HostingPanelInterface
{
    /**
     * Crea una cuenta de hosting nueva en el panel.
     *
     * @param array $data ['username','password','email','domain',...]
     */
    public function createAccount(array $data): HostingProvisionResult;

    public function getUsage(string $accountId): HostingOperationResult;

    public function suspend(string $accountId): HostingOperationResult;

    public function changePassword(string $accountId, string $newPassword): HostingOperationResult;

    public function testConnection(): HostingOperationResult;
}
