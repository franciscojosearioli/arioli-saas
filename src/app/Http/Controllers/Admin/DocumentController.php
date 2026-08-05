<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Document;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    private const DOCUMENTABLE_TYPES = [
        'client'  => Client::class,
        'project' => Project::class,
    ];

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-clients');

        $validated = $request->validate([
            'documentable_type' => 'required|in:client,project',
            'documentable_id'   => 'required|integer',
            'file'              => 'required|file|max:10240',
        ]);

        $modelClass = self::DOCUMENTABLE_TYPES[$validated['documentable_type']];
        $documentable = $modelClass::findOrFail($validated['documentable_id']);

        $file = $request->file('file');
        $storedName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs("documents/{$validated['documentable_type']}/{$documentable->id}", $storedName, 'local');

        Document::create([
            'documentable_type' => $modelClass,
            'documentable_id'   => $documentable->id,
            'name'              => $file->getClientOriginalName(),
            'file_path'         => $path,
            'mime_type'         => $file->getClientMimeType(),
            'size'              => $file->getSize(),
            'user_id'           => Auth::id(),
        ]);

        return back()->with('success', 'Documento subido.');
    }

    public function download(Document $document): StreamedResponse
    {
        Gate::authorize('manage-clients');

        return Storage::disk('local')->download($document->file_path, $document->name);
    }

    public function destroy(Document $document): RedirectResponse
    {
        Gate::authorize('manage-clients');

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
