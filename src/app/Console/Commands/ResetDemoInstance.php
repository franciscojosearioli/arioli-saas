<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResetDemoInstance extends Command
{
    protected $signature   = 'demo:reset {product : Slug del producto (loteos|tallerpro|historias-clinicas|turnos|subastas)}';
    protected $description = 'Reinicia los datos demo de un producto al estado inicial';

    private const VALID_SLUGS = ['loteos', 'tallerpro', 'historias-clinicas', 'turnos', 'subastas'];

    public function handle(): int
    {
        $slug = $this->argument('product');

        if (!in_array($slug, self::VALID_SLUGS)) {
            $this->error("Producto inválido: {$slug}. Use: " . implode(', ', self::VALID_SLUGS));
            return 1;
        }

        $product = Product::where('slug', $slug)->firstOrFail();

        $this->info("Reiniciando demo {$slug}...");

        try {
            $this->callInternalEndpoint($product->public_domain, 'reset');
            Log::info("demo:reset completed", ['product' => $slug]);
            $this->info("Demo {$slug} reiniciada correctamente.");
            return 0;
        } catch (\Throwable $e) {
            $this->error("Error: " . $e->getMessage());
            Log::error("demo:reset failed", ['product' => $slug, 'error' => $e->getMessage()]);
            return 1;
        }
    }

    private function callInternalEndpoint(string $publicDomain, string $action): void
    {
        $domain = env('SAAS_TENANT_DOMAIN', '127.0.0.1.nip.io');
        $host   = "demo.{$publicDomain}.{$domain}";
        $secret = env('API_LICENSE_SECRET');

        $response = Http::timeout(30)
            ->withToken($secret)
            ->withHeaders(['Host' => $host])
            ->post("http://nginx/internal/demo/{$action}");

        if (!$response->successful()) {
            throw new \RuntimeException("Internal {$action} call failed [{$response->status()}]: " . $response->body());
        }
    }
}
