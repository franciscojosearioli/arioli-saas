<?php

namespace App\Contracts;

interface DashboardProviderInterface
{
    /**
     * @return array<int, array{group: string, label: string, value: int|string, color: string}>
     */
    public function widgets(): array;
}
