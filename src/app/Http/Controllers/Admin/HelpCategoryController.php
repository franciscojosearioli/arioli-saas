<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class HelpCategoryController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-clients');

        $categories = HelpCategory::whereNull('parent_id')
            ->with(['articles', 'children.articles'])
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('admin.centro-de-ayuda.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'icon'      => 'nullable|string|max:10',
            'parent_id' => 'nullable|exists:help_categories,id',
        ]);

        HelpCategory::create($validated);

        return back()->with('success', 'Categoría creada.');
    }

    public function update(Request $request, HelpCategory $category): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'icon'      => 'nullable|string|max:10',
            'parent_id' => 'nullable|exists:help_categories,id',
        ]);

        $category->update($validated);

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(HelpCategory $category): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $category->delete();

        return back()->with('success', 'Categoría eliminada.');
    }
}
