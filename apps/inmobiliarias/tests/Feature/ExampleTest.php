<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_la_raiz_del_tenant_muestra_el_storefront_publico(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }
}
