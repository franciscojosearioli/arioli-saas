<?php

namespace App\Http\Controllers\Admin;

use App\Enums\HelpArticleContentType;
use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HelpArticleController extends Controller
{
    public function create(): View
    {
        Gate::authorize('manage-clients');

        $categories = HelpCategory::orderBy('position')->orderBy('name')->get();

        return view('admin.centro-de-ayuda.articulos.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $this->validated($request);
        $validated['slug'] = $this->uniqueSlug($validated['title']);

        HelpArticle::create($validated);

        return redirect()->route('centro-de-ayuda.index')->with('success', 'Artículo creado.');
    }

    public function edit(HelpArticle $article): View
    {
        Gate::authorize('manage-clients');

        $categories = HelpCategory::orderBy('position')->orderBy('name')->get();

        return view('admin.centro-de-ayuda.articulos.edit', compact('article', 'categories'));
    }

    public function update(Request $request, HelpArticle $article): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $article->update($this->validated($request));

        return redirect()->route('centro-de-ayuda.index')->with('success', 'Artículo actualizado.');
    }

    public function destroy(HelpArticle $article): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $article->delete();

        return back()->with('success', 'Artículo eliminado.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'help_category_id' => 'required|exists:help_categories,id',
            'title'             => 'required|string|max:255',
            'content_type'      => ['required', Rule::in(array_column(HelpArticleContentType::cases(), 'value'))],
            'content'           => 'nullable|string',
            'video_url'         => 'nullable|string|max:255',
            'external_url'      => 'nullable|string|max:255',
        ]);

        $validated['published'] = $request->boolean('published');

        return $validated;
    }

    private function uniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (HelpArticle::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
