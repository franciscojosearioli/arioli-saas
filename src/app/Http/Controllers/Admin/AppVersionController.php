<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateAppInstance;
use App\Models\AppVersion;
use App\Models\License;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppVersionController extends Controller
{
    public function index(): View
    {
        $products = Product::where('active', true)
            ->with(['appVersions' => fn($q) => $q->orderByDesc('released_at')])
            ->get();

        // Para cada producto: cuántos tenants estándar (no custom) están desactualizados
        $pendingByProduct = [];
        $customByProduct  = [];

        foreach ($products as $product) {
            $current = $product->appVersions->firstWhere('is_current', true);
            if (!$current) {
                $pendingByProduct[$product->id] = 0;
                $customByProduct[$product->id]  = 0;
                continue;
            }

            // Tenants con licencia activa de este producto
            $licenses = License::where('active', true)
                ->whereHas('plan', fn($q) => $q->where('product_id', $product->id))
                ->with('tenant')
                ->get();

            $pending = 0;
            $custom  = 0;
            foreach ($licenses as $lic) {
                if (!$lic->tenant) continue;
                if ($lic->tenant->is_custom) {
                    $custom++;
                } elseif ($lic->installed_version !== $current->version) {
                    $pending++;
                }
            }

            $pendingByProduct[$product->id] = $pending;
            $customByProduct[$product->id]  = $custom;
        }

        // Conteo de tenants por versión instalada
        $installedCounts = License::where('active', true)
            ->whereNotNull('installed_version')
            ->selectRaw('installed_version, COUNT(*) as total')
            ->groupBy('installed_version')
            ->pluck('total', 'installed_version');

        return view('admin.app-versions.index', compact(
            'products', 'installedCounts', 'pendingByProduct', 'customByProduct'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'      => 'required|exists:products,id',
            'version'         => 'required|string|max:20|regex:/^\d+\.\d+\.\d+$/',
            'type'            => 'required|in:stable,beta,alpha',
            'changelog'       => 'required|string',
            'min_php_version' => 'nullable|string|max:10',
        ]);

        // Evitar versión duplicada en el mismo producto
        if (AppVersion::where('product_id', $validated['product_id'])
                       ->where('version', $validated['version'])->exists()) {
            return back()->withErrors(['version' => 'Esta versión ya existe para el producto.'])->withInput();
        }

        // Convertir changelog (texto multilínea → array de items)
        $changelogLines = array_values(array_filter(
            array_map('trim', explode("\n", $validated['changelog']))
        ));

        AppVersion::create([
            'product_id'      => $validated['product_id'],
            'version'         => $validated['version'],
            'type'            => $validated['type'],
            'changelog'       => $changelogLines,
            'released_at'     => now(),
            'is_current'      => true,
            'min_php_version' => $validated['min_php_version'] ?? null,
        ]);

        return redirect()->route('app-versions.index')
            ->with('success', "Versión {$validated['version']} publicada correctamente.");
    }

    public function pushUpdates(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $current = AppVersion::latestFor($product->slug);

        if (!$current) {
            return back()->withErrors(['product_id' => 'No hay versión actual para este producto.']);
        }

        // Tenants estándar (no custom) desactualizados
        $licenses = License::where('active', true)
            ->whereHas('plan', fn($q) => $q->where('product_id', $product->id))
            ->with('tenant')
            ->get()
            ->filter(fn($lic) =>
                $lic->tenant &&
                !$lic->tenant->is_custom &&
                $lic->installed_version !== $current->version
            );

        $dispatched = 0;
        foreach ($licenses as $lic) {
            UpdateAppInstance::dispatch($lic->tenant_id, $product->slug, $current->version);
            $dispatched++;
        }

        return redirect()->route('app-versions.index')
            ->with('success', "Se enviaron actualizaciones a {$dispatched} cliente(s) para {$product->name} v{$current->version}.");
    }
}
