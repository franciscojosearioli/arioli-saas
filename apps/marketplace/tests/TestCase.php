<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // No hay Node en esta máquina de desarrollo (ni build de Vite
        // comiteado) — los tests no deberían depender de que exista.
        $this->withoutVite();
    }
}
