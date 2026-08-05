<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ResetDemoInstance;
use App\Models\License;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;

class DemoController extends Controller
{
    public function index()
    {
        $products = Product::where('active', true)->get();

        $demos = [];
        foreach ($products as $product) {
            $license = License::where('tenant_id', 'demo')
                ->whereHas('plan.product', fn($q) => $q->where('id', $product->id))
                ->with('plan')
                ->first();

            $demos[] = [
                'product'    => $product,
                'license'    => $license,
                'db_exists'  => $license ? $this->dbExists($product->slug) : false,
                'demo_url'   => 'demo.' . $product->public_domain . '.' . config('app.tenant_domain'),
                'demo_email' => $this->demoEmail($product->slug),
            ];
        }

        return view('admin.demos.index', compact('demos'));
    }

    public function provision(Request $request): RedirectResponse
    {
        $slug = $request->input('product');

        $exitCode = Artisan::call('demo:provision', ['product' => $slug]);
        $output   = Artisan::output();

        if ($exitCode === 0) {
            return redirect()->route('demos.index')
                ->with('success', "Demo '{$slug}' provisionada exitosamente.");
        }

        return redirect()->route('demos.index')
            ->with('error', "Error al provisionar demo '{$slug}': {$output}");
    }

    public function reset(Request $request): RedirectResponse
    {
        $slug = $request->input('product');

        ResetDemoInstance::dispatch($slug);

        return redirect()->route('demos.index')
            ->with('success', "Reset de demo '{$slug}' encolado. Se ejecutará en breve.");
    }

    public function factoryReset(Request $request): RedirectResponse
    {
        Gate::authorize('admin-access');

        $exitCode = Artisan::call('saas:purge-non-demo', ['--force' => true]);
        $output   = Artisan::output();

        if ($exitCode === 0) {
            return redirect()->route('demos.index')
                ->with('success', 'Factory reset completado. El sistema quedó en estado demo limpio.');
        }

        return redirect()->route('demos.index')
            ->with('error', "Error durante el factory reset: {$output}");
    }

    private function dbExists(string $slug): bool
    {
        $prefix = match($slug) {
            'loteos'             => 'loteos_',
            'tallerpro'          => 'tallerpro_',
            'historias-clinicas' => 'historias_',
            default              => $slug . '_',
        };

        try {
            $result = \Illuminate\Support\Facades\DB::select(
                "SELECT SCHEMA_NAME FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = ?",
                [$prefix . 'demo']
            );
            return !empty($result);
        } catch (\Throwable) {
            return false;
        }
    }

    private function demoEmail(string $slug): string
    {
        $credentials = config("demo.credentials.{$slug}", []);
        return $credentials[0]['email'] ?? 'admin@demo.com';
    }
}
