{{-- Hoy --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Hoy</h3>
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">
        <div>
            <h4 style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin:0 0 10px;">Tareas</h4>
            @forelse($client->tasks as $task)
                <div style="display:flex; align-items:center; gap:8px; padding:6px 0; border-bottom:1px solid #f3f4f6;">
                    <form method="POST" action="{{ route('clients.tasks.toggle', [$client, $task]) }}">
                        @csrf @method('PATCH')
                        <button type="submit" title="Marcar {{ $task->isCompleted() ? 'pendiente' : 'completada' }}"
                                style="border:1.5px solid {{ $task->isCompleted() ? 'var(--accent)' : '#d1d5db' }}; background:{{ $task->isCompleted() ? 'var(--accent)' : 'transparent' }}; width:16px; height:16px; border-radius:4px; cursor:pointer; flex-shrink:0; padding:0;"></button>
                    </form>
                    <div style="flex:1; font-size:12.5px; {{ $task->isCompleted() ? 'text-decoration:line-through; color:var(--text-muted);' : 'color:var(--text-primary);' }}">
                        {{ $task->title }}
                        @if($task->due_date) <span style="color:var(--text-muted); font-size:11px;">— {{ $task->due_date->format('d/m/Y') }}</span> @endif
                    </div>
                    <form method="POST" action="{{ route('clients.tasks.destroy', [$client, $task]) }}">
                        @csrf @method('DELETE')
                        <button style="border:none; background:none; color:#dc2626; cursor:pointer; font-size:11px;">×</button>
                    </form>
                </div>
            @empty
                <p style="font-size:12px; color:var(--text-muted);">Sin tareas pendientes.</p>
            @endforelse
            <form method="POST" action="{{ route('clients.tasks.store', $client) }}" style="margin-top:10px; display:flex; gap:6px;">
                @csrf
                <input type="text" name="title" class="form-input" placeholder="Nueva tarea..." style="font-size:12px; padding:6px 10px;" required>
                <input type="date" name="due_date" class="form-input" style="font-size:12px; padding:6px 8px; width:130px;">
                <button type="submit" class="btn btn-secondary" style="font-size:11px; padding:6px 10px;">+</button>
            </form>
        </div>
        <div>
            <h4 style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin:0 0 10px;">Próximos vencimientos</h4>
            @forelse($upcomingRenewals as $asset)
                @php $urColor = $asset->renewalStatusLabel() === 'Vencido' ? '#991b1b' : '#92400e'; @endphp
                <div style="display:flex; justify-content:space-between; gap:8px; padding:6px 0; border-bottom:1px solid #f3f4f6; font-size:12.5px;">
                    <span style="color:var(--text-primary);">{{ $asset->label() }}</span>
                    <span style="color:{{ $urColor }}; font-weight:600; white-space:nowrap;">{{ $asset->expiresAt()->format('d/m/Y') }}</span>
                </div>
            @empty
                <p style="font-size:12px; color:var(--text-muted);">Nada por vencer pronto.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Línea de tiempo --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Línea de tiempo</h3>
    <div style="display:flex; flex-direction:column; gap:14px; max-height:280px; overflow-y:auto;">
        @foreach($client->events as $event)
            <div style="display:flex; gap:12px; font-size:12.5px;">
                <div style="width:8px; height:8px; border-radius:50%; background:var(--accent); margin-top:5px; flex-shrink:0;"></div>
                <div>
                    <div style="font-weight:600; color:var(--text-primary);">
                        {{ $event->event }}
                        @if($event->subject_label) — {{ $event->subject_label }} @endif
                    </div>
                    <div style="color:var(--text-muted); margin-top:2px;">
                        {{ $event->created_at->format('d/m/Y H:i') }}
                        @if($event->user) · {{ $event->user->name }} @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Notas --}}
<div class="card" style="padding:24px;">
    <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Notas</h3>
    @foreach($client->notes as $note)
        <div style="padding:10px 0; border-bottom:1px solid #f3f4f6;">
            <div style="font-size:12.5px; color:var(--text-primary);">{{ $note->is_pinned ? '📌 ' : '' }}{{ $note->body }}</div>
            <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">{{ $note->created_at->format('d/m/Y H:i') }} @if($note->user) · {{ $note->user->name }} @endif</div>
        </div>
    @endforeach
    <form method="POST" action="{{ route('notes.store') }}" style="margin-top:16px; padding-top:16px; border-top:1px solid #f3f4f6;">
        @csrf
        <input type="hidden" name="noteable_type" value="client">
        <input type="hidden" name="noteable_id" value="{{ $client->id }}">
        <textarea name="body" class="form-input" rows="2" placeholder="Escribir una nota..." style="margin-bottom:8px;" required></textarea>
        <button type="submit" class="btn btn-secondary" style="width:100%;">Agregar nota</button>
    </form>
</div>
