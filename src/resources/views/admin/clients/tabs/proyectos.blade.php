{{-- Proyectos --}}
<div class="card" style="padding:24px; margin-bottom:20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0;">Proyectos</h3>
        <x-admin.modal id="add-project" title="Agregar proyecto" trigger-label="+ Agregar proyecto" trigger-class="btn btn-secondary" trigger-style="font-size:11.5px; padding:5px 10px;">
            <form method="POST" action="{{ route('clients.projects.store', $client) }}" style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                @csrf
                <input type="text" name="name" class="form-input" placeholder="Nombre del proyecto" style="grid-column:span 2;" required>
                <select name="type" class="form-select">
                    @foreach(\App\Enums\ProjectType::cases() as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                    @endforeach
                </select>
                <select name="priority" class="form-select">
                    @foreach(\App\Enums\Priority::cases() as $p)
                        <option value="{{ $p->value }}" {{ $p->value === 'media' ? 'selected' : '' }}>{{ $p->label() }}</option>
                    @endforeach
                </select>
                <select name="domain_id" class="form-select">
                    <option value="">— Dominio —</option>
                    @foreach($client->domains as $d)
                        <option value="{{ $d->id }}">{{ $d->domain_name }}</option>
                    @endforeach
                </select>
                <select name="hosting_id" class="form-select">
                    <option value="">— Hosting —</option>
                    @foreach($client->hostings as $h)
                        <option value="{{ $h->id }}">{{ $h->provider }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary" style="grid-column:span 2;">Agregar proyecto</button>
            </form>
        </x-admin.modal>
    </div>

    @foreach($client->projects as $project)
        <a href="{{ route('clients.projects.show', [$client, $project]) }}" style="display:block; text-decoration:none; padding:12px 0; border-bottom:1px solid #f3f4f6;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <div style="font-size:13.5px; font-weight:600; color:var(--text-primary);">{{ $project->name }}</div>
                    <div style="font-size:11.5px; color:var(--text-muted);">
                        {{ $project->type->label() }}
                        @if($project->domain) · {{ $project->domain->domain_name }} @endif
                        @if($project->hosting) · {{ $project->hosting->provider }} @endif
                        @if($project->sslCertificate) · SSL {{ $project->sslCertificate->provider }} @endif
                        @if($project->cloudflareService) · Cloudflare @endif
                        @if($project->license) · {{ $project->license->plan?->product?->name }} @endif
                    </div>
                </div>
                <div style="text-align:right;">
                    <span style="font-size:11px; color:var(--text-muted);">{{ $project->services->count() }} servicio(s)</span>
                    @php $inProgressCount = $project->tasks->where('status', \App\Enums\ProjectTaskStatus::InProgress)->count(); @endphp
                    @if($inProgressCount > 0)
                        <div style="font-size:10.5px; color:#92400e; font-weight:600; margin-top:2px;">🔧 {{ $inProgressCount }} en progreso</div>
                    @endif
                </div>
            </div>
        </a>
    @endforeach
</div>
