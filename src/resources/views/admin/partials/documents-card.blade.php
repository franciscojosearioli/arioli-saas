{{-- Requiere: $documentable (modelo con relación documents cargada), $documentableType (string: client|project) --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Documentos</h3>
        <x-admin.modal id="add-document-{{ $documentableType }}-{{ $documentable->id }}" title="Subir documento" trigger-label="+ Subir documento" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
            <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="documentable_type" value="{{ $documentableType }}">
                <input type="hidden" name="documentable_id" value="{{ $documentable->id }}">
                <input type="file" name="file" class="form-input" style="margin-bottom:12px;" required>
                <button type="submit" class="btn btn-primary" style="width:100%;">Subir documento</button>
            </form>
        </x-admin.modal>
    </div>

    @forelse($documentable->documents as $document)
        <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div>
                <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $document->name }}</div>
                <div style="font-size:11.5px; color:var(--text-muted);">{{ $document->humanSize() }} · {{ $document->created_at->format('d/m/Y') }} @if($document->user) · {{ $document->user->name }} @endif</div>
            </div>
            <div style="display:flex; gap:6px;">
                <a href="{{ route('documents.download', $document) }}" class="btn btn-secondary" style="padding:4px 10px; font-size:11px;">Descargar</a>
                <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('¿Eliminar este documento?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;">×</button>
                </form>
            </div>
        </div>
    @empty
        <p style="font-size:12.5px; color:var(--text-muted);">Sin documentos.</p>
    @endforelse
</div>
