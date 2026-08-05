<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = App\Models\Product::where('slug', 'historias-clinicas')->first();
if (!$product) {
    echo "Producto historias-clinicas no encontrado\n";
    exit(1);
}

$plan = $product->plans()->where('period', 'monthly')->first();
if (!$plan) {
    echo "Plan mensual no encontrado\n";
    exit(1);
}

$license = App\Models\License::createForTenant('test', $plan->id, 1, $product);
echo "Licencia creada: " . $license->id . "\n";
echo "Dominio: test." . $product->public_domain . "." . config('app.tenant_domain', '127.0.0.1.nip.io') . "\n";