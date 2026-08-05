<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Product;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->query('search', '');
        $productFilter = $request->query('product', '');

        $notDemo = function ($q) {
            $q->where('is_demo', false);
        };

        $query = Plan::with('product')->withCount('licenses')->where($notDemo);

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($productFilter) {
            $query->where('product_id', $productFilter);
        }

        $plans    = $query->latest()->paginate(15)->withQueryString();
        $products = Product::where('active', true)->get();

        $totalPlans             = Plan::where($notDemo)->count();
        $activePlans            = Plan::where('active', true)->where($notDemo)->count();
        $totalLicensesFromPlans = \App\Models\License::notDemo()->count();

        if ($request->ajax()) {
            return response()->json([
                'tableBody'  => view('admin.plans.partials.plan-table-body',
                    compact('plans', 'search'))->render(),
                'pagination' => view('admin.plans.partials.plan-pagination',
                    compact('plans', 'search'))->render(),
                'total'      => $plans->total(),
            ]);
        }

        return view('admin.plans.index',
            compact('plans', 'products', 'search', 'productFilter',
                    'totalPlans', 'activePlans', 'totalLicensesFromPlans'));
    }

    public function create()
    {
        $products = Product::where('active', true)->get();
        return view('admin.plans.create', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'       => 'required|exists:products,id',
            'period'           => 'required|in:monthly,quarterly,semiannual,annual,perpetual',
            'price'            => 'required|numeric|min:0',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'active'           => 'boolean',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $data    = $this->planDataFor($validated, $product);

        Plan::create([
            ...$data,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('plans.index')
            ->with('success', 'Plan creado correctamente.');
    }

    /**
     * Arma los campos de un Plan a partir del período elegido — una licencia
     * indefinida no tiene mes que multiplicar: el "monto único" cargado es
     * directamente el precio final, sin descuento (no aplica a un pago único).
     */
    private function planDataFor(array $validated, Product $product): array
    {
        $isPerpetual = $validated['period'] === 'perpetual';

        $periodMonths = match ($validated['period']) {
            'monthly'    => 1,
            'quarterly'  => 3,
            'semiannual' => 6,
            'annual'     => 12,
            'perpetual'  => 0,
        };

        $periodLabel = match ($validated['period']) {
            'monthly'    => 'Mensual',
            'quarterly'  => 'Trimestral',
            'semiannual' => 'Semestral',
            'annual'     => 'Anual',
            'perpetual'  => 'Indefinida',
        };

        $basePrice = $validated['price'];
        $discount  = $isPerpetual ? 0 : (int) ($validated['discount_percent'] ?? 0);
        $finalPrice = $isPerpetual ? $basePrice : $basePrice * $periodMonths * (1 - $discount / 100);

        return [
            'product_id'       => $validated['product_id'],
            'name'             => $product->name . ' — ' . $periodLabel,
            'period'           => $validated['period'],
            'period_months'    => $periodMonths,
            'is_perpetual'     => $isPerpetual,
            'base_price'       => $basePrice,
            'price'            => $finalPrice,
            'discount_percent' => $discount,
        ];
    }

    public function show(string $id)
    {
        $plan = Plan::with('product')->withCount('licenses')->findOrFail($id);
        return view('admin.plans.show', compact('plan'));
    }

    public function edit(string $id)
    {
        $plan     = Plan::findOrFail($id);
        $products = Product::where('active', true)->get();
        return view('admin.plans.edit', compact('plan', 'products'));
    }

    public function update(Request $request, string $id)
    {
        $plan = Plan::findOrFail($id);

        $validated = $request->validate([
            'product_id'       => 'required|exists:products,id',
            'period'           => 'required|in:monthly,quarterly,semiannual,annual,perpetual',
            'price'            => 'required|numeric|min:0',
            'discount_percent' => 'nullable|integer|min:0|max:100',
            'active'           => 'boolean',
        ]);

        $product = Product::findOrFail($validated['product_id']);
        $data    = $this->planDataFor($validated, $product);

        $plan->update([
            ...$data,
            'active' => $request->boolean('active'),
        ]);

        return redirect()->route('plans.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        $plan = Plan::findOrFail($id);

        if ($plan->licenses()->count() > 0) {
            return redirect()->route('plans.index')
                ->with('error', 'No se puede eliminar un plan con licencias activas.');
        }

        $plan->delete();

        return redirect()->route('plans.index')
            ->with('success', 'Plan eliminado correctamente.');
    }
}