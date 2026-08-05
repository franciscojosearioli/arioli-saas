<?php

namespace App\Services\Contracts;

class ContractTemplateRenderer
{
    public function __construct(private readonly PlaceholderResolver $resolver) {}

    /**
     * Sustitución simple de texto (no Blade/PHP embebido — evita abrir una
     * superficie de inyección si en el futuro las plantillas las carga
     * alguien menos confiable).
     */
    public function render(string $templateContent, array $context): string
    {
        $placeholders = $this->resolver->resolve($context);

        $replacements = [];
        foreach ($placeholders as $key => $value) {
            $replacements['{{'.$key.'}}'] = $value;
        }

        return strtr($templateContent, $replacements);
    }
}
