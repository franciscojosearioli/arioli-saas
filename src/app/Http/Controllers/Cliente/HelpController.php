<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use App\Models\HelpCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class HelpController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('client-access');

        $query = trim((string) $request->query('q', ''));

        if ($query !== '') {
            $results = HelpArticle::published()
                ->where(fn ($q) => $q->where('title', 'like', "%{$query}%")->orWhere('content', 'like', "%{$query}%"))
                ->with('category')
                ->orderBy('title')
                ->get();

            return view('cliente.help.index', ['query' => $query, 'results' => $results, 'categories' => null]);
        }

        $categories = HelpCategory::whereNull('parent_id')
            ->with([
                'articles' => fn ($q) => $q->published(),
                'children.articles' => fn ($q) => $q->published(),
            ])
            ->orderBy('position')
            ->orderBy('name')
            ->get();

        return view('cliente.help.index', ['query' => '', 'results' => null, 'categories' => $categories]);
    }

    public function show(HelpArticle $article): View
    {
        Gate::authorize('client-access');

        abort_if(! $article->published, 404);

        return view('cliente.help.show', compact('article'));
    }
}
