<?php

namespace App\Console\Commands;

use App\Models\AppVersion;
use App\Models\Product;
use Illuminate\Console\Command;

class AppRelease extends Command
{
    protected $signature = 'app:release
        {product   : Slug del producto (ej: tallerpro, loteos)}
        {version   : Número de versión semántica (ej: 1.1.0)}
        {--stable  : Marcar como versión estable (default)}
        {--beta    : Marcar como versión beta}
        {--alpha   : Marcar como versión alpha}
        {--changelog=* : Ítems del changelog (repetible: --changelog="Fix X" --changelog="Add Y")}';

    protected $description = 'Registra una nueva versión de una aplicación en el sistema SaaS';

    public function handle(): int
    {
        $productSlug = $this->argument('product');
        $version     = $this->argument('version');

        $product = Product::where('slug', $productSlug)->first();

        if (!$product) {
            $this->error("Producto '{$productSlug}' no encontrado en la base de datos.");
            $this->line('Productos disponibles: ' . Product::pluck('slug')->join(', '));
            return 1;
        }

        if (AppVersion::where('product_id', $product->id)->where('version', $version)->exists()) {
            $this->error("La versión {$version} ya existe para {$productSlug}.");
            return 1;
        }

        $type = 'stable';
        if ($this->option('beta'))  $type = 'beta';
        if ($this->option('alpha')) $type = 'alpha';

        $changelog = $this->option('changelog') ?: [];

        if (empty($changelog)) {
            $this->warn('No se especificó changelog. La versión se creará sin notas de cambio.');
        }

        $appVersion = AppVersion::create([
            'product_id'      => $product->id,
            'version'         => $version,
            'type'            => $type,
            'changelog'       => $changelog,
            'released_at'     => now(),
            'is_current'      => true,
            'min_php_version' => '8.3',
        ]);

        $this->info("✓ Versión {$version} ({$type}) registrada para {$productSlug} [ID: {$appVersion->id}]");
        $this->info("  Esta versión es ahora la corriente (is_current = true).");

        if (!empty($changelog)) {
            $this->table(['Changelog'], array_map(fn($item) => [$item], $changelog));
        }

        return 0;
    }
}
