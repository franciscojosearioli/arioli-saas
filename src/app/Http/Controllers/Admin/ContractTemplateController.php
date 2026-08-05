<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractTemplate;
use App\Services\Contracts\PlaceholderResolver;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContractTemplateController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-legal');

        $templates = ContractTemplate::withCount('contracts')->orderBy('name')->paginate(20);

        return view('admin.legales.plantillas.index', compact('templates'));
    }

    public function create(PlaceholderResolver $resolver): View
    {
        Gate::authorize('manage-legal');

        $availablePlaceholders = $resolver->availableKeys();

        return view('admin.legales.plantillas.create', compact('availablePlaceholders'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-legal');

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required|in:licencia,servicio,otro',
            'content' => 'required|string',
            'active'  => 'nullable|boolean',
        ]);

        $template = ContractTemplate::create([
            ...$validated,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('legales.plantillas.edit', $template)->with('success', 'Plantilla creada.');
    }

    public function edit(ContractTemplate $template, PlaceholderResolver $resolver): View
    {
        Gate::authorize('manage-legal');

        $availablePlaceholders = $resolver->availableKeys();

        return view('admin.legales.plantillas.edit', compact('template', 'availablePlaceholders'));
    }

    public function update(Request $request, ContractTemplate $template): RedirectResponse
    {
        Gate::authorize('manage-legal');

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'type'    => 'required|in:licencia,servicio,otro',
            'content' => 'required|string',
            'active'  => 'nullable|boolean',
        ]);

        $template->update([
            ...$validated,
            'active' => $request->boolean('active', true),
        ]);

        return redirect()->route('legales.plantillas.edit', $template)->with('success', 'Plantilla actualizada. Se guardó la versión anterior en el historial.');
    }

    public function versions(ContractTemplate $template): View
    {
        Gate::authorize('manage-legal');

        $versions = $template->versions()->with('createdBy')->get();

        return view('admin.legales.plantillas.versions', compact('template', 'versions'));
    }
}
