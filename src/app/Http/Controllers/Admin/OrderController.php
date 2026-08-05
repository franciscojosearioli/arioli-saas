<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $status = $request->query('status', '');

        $query = Order::with('plan.product')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_company', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(15)->withQueryString();

        $totalOrders    = Order::count();
        $approvedOrders = Order::where('status', 'approved')->count();
        $pendingOrders  = Order::where('status', 'pending')->count();
        $totalRevenue   = Order::where('status', 'approved')->sum('amount');

        if ($request->ajax()) {
            return response()->json([
                'tableBody'  => view('admin.orders.partials.order-table-body',
                    compact('orders', 'search'))->render(),
                'pagination' => view('admin.orders.partials.order-pagination',
                    compact('orders', 'search', 'status'))->render(),
                'total'      => $orders->total(),
            ]);
        }

        return view('admin.orders.index',
            compact('orders', 'search', 'status',
                    'totalOrders', 'approvedOrders', 'pendingOrders', 'totalRevenue'));
    }

    public function show(string $id)
    {
        $order = Order::with('plan.product')->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }
}