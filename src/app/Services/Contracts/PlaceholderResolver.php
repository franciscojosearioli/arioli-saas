<?php

namespace App\Services\Contracts;

class PlaceholderResolver
{
    /**
     * @param array $context contexto de renderizado (tenant_id, customer_*, contractable, etc.)
     * @return array<string, string> todos los placeholders disponibles para este contexto
     */
    public function resolve(array $context): array
    {
        $placeholders = [];

        foreach (config('contract_placeholders', []) as $providerClass) {
            $provider = app($providerClass);

            if ($provider->supports($context)) {
                $placeholders = [...$placeholders, ...$provider->resolve($context)];
            }
        }

        return $placeholders;
    }

    /**
     * Nombres disponibles (sin resolver valores), para mostrar como ayuda
     * en el editor de plantillas.
     */
    public function availableKeys(): array
    {
        // Contexto "completo" ficticio, solo para listar qué placeholders
        // existen — no se usan los valores reales.
        $sample = ['tenant_id' => 'x', 'customer_name' => 'x', 'contractable' => null];

        $keys = [];
        foreach (config('contract_placeholders', []) as $providerClass) {
            $provider = app($providerClass);
            if ($provider->supports($sample)) {
                $keys = [...$keys, ...array_keys($provider->resolve($sample))];
            }
        }

        return $keys;
    }
}
