<x-admin-layout title="{{ $project->name }}">

    <div style="margin-bottom:24px;">
        <a href="{{ route('clients.show', $client) }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a {{ $client->name }}</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0;">{{ $project->name }}</h1>
            <p style="font-size:13px; color:var(--text-muted); margin:4px 0 0;">{{ $project->type->label() }} — {{ $client->name }}</p>
        </div>
        <form method="POST" action="{{ route('clients.projects.destroy', [$client, $project]) }}" onsubmit="return confirm('¿Eliminar este proyecto?')">
            @csrf @method('DELETE')
            <button class="btn btn-secondary" style="color:#dc2626;">Eliminar proyecto</button>
        </form>
    </div>

    {{-- Tablero --}}
    <div class="card" style="padding:24px; margin-bottom:20px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Tablero</h3>
            <x-admin.modal id="add-task" title="Agregar tarea" trigger-label="+ Agregar tarea" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
                <form method="POST" action="{{ route('clients.projects.tasks.store', [$client, $project]) }}">
                    @csrf
                    <input type="text" name="title" class="form-input" placeholder="Título" style="margin-bottom:8px;" required>
                    <textarea name="description" class="form-input" placeholder="Descripción (opcional)" rows="3" style="margin-bottom:12px;"></textarea>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Agregar tarea</button>
                </form>
            </x-admin.modal>
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:16px;">
            @foreach(\App\Enums\ProjectTaskStatus::cases() as $colStatus)
                <div class="kanban-column" data-status="{{ $colStatus->value }}" style="background:var(--body-bg); border-radius:10px; padding:10px; min-height:140px;">
                    <div style="font-size:11px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin-bottom:10px;">
                        {{ $colStatus->label() }} ({{ $project->tasks->where('status', $colStatus)->count() }})
                    </div>
                    <div class="kanban-cards" style="display:flex; flex-direction:column; gap:8px; min-height:80px;">
                        @foreach($project->tasks->where('status', $colStatus) as $task)
                            <div class="kanban-card" draggable="true" data-task-id="{{ $task->id }}" style="background:var(--card-bg); border:1px solid var(--card-border); border-radius:8px; padding:10px; cursor:grab;">
                                <div style="font-size:12.5px; font-weight:600; color:var(--text-primary);">{{ $task->title }}</div>
                                @if($task->description)
                                    <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">{{ \Illuminate\Support\Str::limit($task->description, 80) }}</div>
                                @endif
                                <div style="margin-top:8px;">
                                    <x-admin.modal id="edit-task-{{ $task->id }}" title="Editar tarea" trigger-label="✎ Editar" trigger-style="font-size:11px;">
                                        <form method="POST" action="{{ route('clients.projects.tasks.update', [$client, $project, $task]) }}">
                                            @csrf @method('PATCH')
                                            <input type="text" name="title" class="form-input" style="margin-bottom:8px;" value="{{ $task->title }}" required>
                                            <textarea name="description" class="form-input" rows="3" style="margin-bottom:12px;">{{ $task->description }}</textarea>
                                            <button type="submit" class="btn btn-primary" style="width:100%;">Guardar cambios</button>
                                        </form>
                                        <form method="POST" action="{{ route('clients.projects.tasks.destroy', [$client, $project, $task]) }}" onsubmit="return confirm('¿Eliminar esta tarea?')" style="margin-top:10px; padding-top:10px; border-top:1px solid var(--card-border);">
                                            @csrf @method('DELETE')
                                            <button type="submit" style="border:none; background:none; color:#dc2626; cursor:pointer; font-size:11px;">Eliminar tarea</button>
                                        </form>
                                    </x-admin.modal>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let dragged = null;
            const statusUrlTemplate = "{{ route('clients.projects.tasks.update-status', [$client, $project, '__TASK__']) }}";

            document.querySelectorAll('.kanban-card').forEach(card => {
                card.addEventListener('dragstart', () => {
                    dragged = card;
                    card.style.opacity = '0.4';
                });
                card.addEventListener('dragend', () => {
                    card.style.opacity = '1';
                });
            });

            document.querySelectorAll('.kanban-column').forEach(column => {
                const cardsContainer = column.querySelector('.kanban-cards');

                column.addEventListener('dragover', (e) => e.preventDefault());

                column.addEventListener('drop', async (e) => {
                    e.preventDefault();
                    if (!dragged) return;

                    cardsContainer.appendChild(dragged);

                    const status = column.dataset.status;
                    const position = Array.from(cardsContainer.children).indexOf(dragged);
                    const taskId = dragged.dataset.taskId;

                    try {
                        await fetch(statusUrlTemplate.replace('__TASK__', taskId), {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({ status, position }),
                        });
                    } catch (err) {
                        console.error('Error moviendo la tarea:', err);
                    }
                });
            });
        });
    </script>

    <div style="display:grid; grid-template-columns:1.2fr 1fr; gap:20px; align-items:start;">

        <div>
            {{-- Datos --}}
            <div class="card" style="padding:24px; margin-bottom:20px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Datos del proyecto</h3>
                <form method="POST" action="{{ route('clients.projects.update', [$client, $project]) }}">
                    @csrf @method('PATCH')
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                        <input type="text" name="name" class="form-input" value="{{ $project->name }}">
                        <select name="type" class="form-select">
                            @foreach(\App\Enums\ProjectType::cases() as $t)
                                <option value="{{ $t->value }}" {{ $project->type->value === $t->value ? 'selected' : '' }}>{{ $t->label() }}</option>
                            @endforeach
                        </select>
                        <select name="priority" class="form-select">
                            @foreach(\App\Enums\Priority::cases() as $p)
                                <option value="{{ $p->value }}" {{ $project->priority->value === $p->value ? 'selected' : '' }}>{{ $p->label() }}</option>
                            @endforeach
                        </select>
                        <select name="status" class="form-select">
                            <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>Activo</option>
                            <option value="inactive" {{ $project->status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            <option value="archived" {{ $project->status === 'archived' ? 'selected' : '' }}>Archivado</option>
                        </select>
                        <select name="domain_id" class="form-select">
                            <option value="">— Dominio —</option>
                            @foreach($client->domains as $d)
                                <option value="{{ $d->id }}" {{ $project->domain_id === $d->id ? 'selected' : '' }}>{{ $d->domain_name }}</option>
                            @endforeach
                        </select>
                        <select name="hosting_id" class="form-select">
                            <option value="">— Hosting —</option>
                            @foreach($client->hostings as $h)
                                <option value="{{ $h->id }}" {{ $project->hosting_id === $h->id ? 'selected' : '' }}>{{ $h->provider }}</option>
                            @endforeach
                        </select>
                        <select name="ssl_certificate_id" class="form-select">
                            <option value="">— SSL —</option>
                            @foreach($client->sslCertificates as $s)
                                <option value="{{ $s->id }}" {{ $project->ssl_certificate_id === $s->id ? 'selected' : '' }}>{{ $s->provider }}</option>
                            @endforeach
                        </select>
                        <select name="cloudflare_service_id" class="form-select">
                            <option value="">— Cloudflare —</option>
                            @foreach($client->cloudflareServices as $cf)
                                <option value="{{ $cf->id }}" {{ $project->cloudflare_service_id === $cf->id ? 'selected' : '' }}>Cloudflare ({{ $cf->managed_by }})</option>
                            @endforeach
                        </select>
                        <select name="license_id" class="form-select">
                            <option value="">— Licencia —</option>
                            @foreach($client->licenses as $lic)
                                <option value="{{ $lic->id }}" {{ $project->license_id === $lic->id ? 'selected' : '' }}>{{ $lic->plan?->product?->name }} ({{ $lic->tenant_id }})</option>
                            @endforeach
                        </select>
                        <input type="text" name="production_url" class="form-input" value="{{ $project->production_url }}" placeholder="URL producción">
                        <input type="text" name="staging_url" class="form-input" value="{{ $project->staging_url }}" placeholder="URL staging">
                        <div>
                            <label class="form-label">Inicio</label>
                            <input type="date" name="started_at" class="form-input" value="{{ $project->started_at?->format('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="form-label">Entrega</label>
                            <input type="date" name="delivered_at" class="form-input" value="{{ $project->delivered_at?->format('Y-m-d') }}">
                        </div>
                    </div>

                    <h4 style="font-size:12.5px; font-weight:700; color:var(--text-muted); text-transform:uppercase; letter-spacing:.05em; margin:20px 0 12px; padding-top:16px; border-top:1px solid #f3f4f6;">Caso de éxito</h4>

                    <div style="margin-bottom:12px;">
                        <label class="form-label">Nombre comercial (si es distinto del nombre interno)</label>
                        <input type="text" name="public_name" class="form-input" value="{{ $project->public_name }}" placeholder="Ej: Sistema de Salud">
                    </div>
                    <div style="margin-bottom:12px;">
                        <label class="form-label">Descripción comercial</label>
                        <textarea name="commercial_description" class="form-input" rows="2">{{ $project->commercial_description }}</textarea>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label class="form-label">¿Qué problema resuelve?</label>
                        <textarea name="problem_solved" class="form-input" rows="2">{{ $project->problem_solved }}</textarea>
                    </div>
                    <div style="margin-bottom:12px;">
                        <label class="form-label">Funcionalidades principales (una por línea)</label>
                        <textarea name="key_features" class="form-input" rows="4">{{ is_array($project->key_features) ? implode("\n", $project->key_features) : '' }}</textarea>
                    </div>
                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px;">
                        <div>
                            <label class="form-label">Orden (menor = primero)</label>
                            <input type="number" name="display_order" class="form-input" value="{{ $project->display_order }}">
                        </div>
                    </div>
                    <div style="display:flex; gap:20px; margin-bottom:16px;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-primary);">
                            <input type="checkbox" name="is_featured" value="1" {{ $project->is_featured ? 'checked' : '' }}>
                            Destacado en la home
                        </label>
                        <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-primary);">
                            <input type="checkbox" name="show_publicly" value="1" {{ $project->show_publicly ? 'checked' : '' }}>
                            Mostrar públicamente
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>

            {{-- Galería (caso de éxito) --}}
            <div class="card" style="padding:24px; margin-bottom:20px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Galería del sistema</h3>
                <div style="display:flex; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
                    @forelse($project->images as $image)
                        <div style="position:relative; width:120px;">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($image->path) }}" alt="{{ $image->title }}" style="width:120px; height:80px; object-fit:cover; border-radius:8px; border:1px solid #e5e7eb;">
                            <form method="POST" action="{{ route('clients.projects.images.destroy', [$client, $project, $image]) }}" onsubmit="return confirm('¿Eliminar esta imagen?')" style="position:absolute; top:4px; right:4px;">
                                @csrf @method('DELETE')
                                <button type="submit" style="background:rgba(0,0,0,.6); color:#fff; border:none; border-radius:6px; padding:2px 6px; font-size:11px; cursor:pointer;">×</button>
                            </form>
                        </div>
                    @empty
                        <p style="font-size:12.5px; color:var(--text-muted);">Todavía no hay imágenes cargadas.</p>
                    @endforelse
                </div>
                <form method="POST" action="{{ route('clients.projects.images.store', [$client, $project]) }}" enctype="multipart/form-data" style="display:grid; grid-template-columns:2fr 1fr auto; gap:8px;">
                    @csrf
                    <input type="file" name="image" class="form-input" accept="image/*" required>
                    <input type="text" name="title" class="form-input" placeholder="Título (opcional)">
                    <button type="submit" class="btn btn-secondary">Agregar imagen</button>
                </form>
            </div>

            {{-- Repositorios --}}
            <div class="card" style="padding:24px; margin-bottom:20px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Repositorios</h3>
                @foreach($project->repositories as $repo)
                    <div style="display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #f3f4f6;">
                        <div>
                            <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $repo->provider }} {{ $repo->is_main ? '(principal)' : '' }}</div>
                            <div style="font-size:11.5px; color:var(--text-muted);">{{ $repo->url }} @if($repo->branch) — {{ $repo->branch }} @endif</div>
                        </div>
                        <form method="POST" action="{{ route('clients.projects.repositories.destroy', [$client, $project, $repo]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-secondary" style="padding:4px 8px; font-size:11px;">×</button>
                        </form>
                    </div>
                @endforeach
                <form method="POST" action="{{ route('clients.projects.repositories.store', [$client, $project]) }}" style="margin-top:16px; padding-top:16px; border-top:1px solid #f3f4f6; display:grid; grid-template-columns:1fr 2fr; gap:8px;">
                    @csrf
                    <select name="provider" class="form-select">
                        <option value="github">GitHub</option>
                        <option value="gitlab">GitLab</option>
                        <option value="bitbucket">Bitbucket</option>
                        <option value="privado">Privado</option>
                        <option value="otro">Otro</option>
                    </select>
                    <input type="text" name="url" class="form-input" placeholder="URL del repositorio" required>
                    <button type="submit" class="btn btn-secondary" style="grid-column:span 2;">Agregar repositorio</button>
                </form>
            </div>

            {{-- Tecnologías --}}
            <div class="card" style="padding:24px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Tecnologías</h3>
                <form method="POST" action="{{ route('clients.projects.technologies', [$client, $project]) }}">
                    @csrf
                    <div style="display:flex; flex-wrap:wrap; gap:10px; margin-bottom:12px;">
                        @foreach($technologies as $tech)
                            <label style="display:flex; align-items:center; gap:4px; font-size:12.5px; border:1px solid #e5e7eb; border-radius:6px; padding:4px 8px;">
                                <input type="checkbox" name="technologies[]" value="{{ $tech->id }}" {{ $project->technologies->contains($tech->id) ? 'checked' : '' }}>
                                {{ $tech->name }}
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="btn btn-secondary">Guardar tecnologías</button>
                </form>
            </div>
        </div>

        <div>
            {{-- Servicios del proyecto --}}
            <div class="card" style="padding:24px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Servicios de este proyecto</h3>
                @forelse($project->services as $service)
                    <div style="padding:10px 0; border-bottom:1px solid #f3f4f6;">
                        <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $service->service_type->label() }}</div>
                        <div style="font-size:11.5px; color:var(--text-muted);">${{ number_format($service->amount, 2) }} — {{ $service->status->label() }}</div>
                    </div>
                @empty
                    <p style="font-size:12.5px; color:var(--text-muted);">Sin servicios asociados. Se agregan desde la ficha del cliente.</p>
                @endforelse
            </div>

            @include('admin.partials.documents-card', ['documentable' => $project, 'documentableType' => 'project'])
            @include('admin.partials.credentials-card', ['credentialable' => $project, 'credentialableType' => 'project'])
        </div>

    </div>

</x-admin-layout>
