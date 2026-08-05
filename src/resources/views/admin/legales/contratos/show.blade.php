@php
    $statusColors = [
        'gray'  => ['bg' => '#f3f4f6', 'fg' => '#374151'],
        'amber' => ['bg' => '#fef3c7', 'fg' => '#92400e'],
        'green' => ['bg' => '#d1fae5', 'fg' => '#065f46'],
        'red'   => ['bg' => '#fee2e2', 'fg' => '#991b1b'],
    ];
    $c = $statusColors[$contract->status->color()] ?? $statusColors['gray'];
@endphp
<x-admin-layout title="Contrato">

    <div style="margin-bottom:24px;">
        <a href="{{ route('legales.contratos.index') }}" style="font-size:13px; color:var(--text-muted); text-decoration:none;">← Volver a Contratos</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:20px;">{{ $errors->first() }}</div>
    @endif

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
        <div>
            <h1 style="font-size:20px; font-weight:700; color:var(--text-primary); margin:0;">{{ $contract->title }}</h1>
            <p style="font-size:13px; color:var(--text-muted); margin:4px 0 0;">{{ $contract->tenant_id }} — {{ $contract->type->label() }}</p>
        </div>
        <span style="padding:4px 12px; border-radius:99px; font-size:12.5px; font-weight:600; background:{{ $c['bg'] }}; color:{{ $c['fg'] }};">
            {{ $contract->status->label() }}
        </span>
    </div>

    <div style="display:grid; grid-template-columns:1.4fr 1fr; gap:20px; align-items:start;">

        <div>
            {{-- Contenido --}}
            <div class="card" style="padding:24px; margin-bottom:20px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 12px;">Documento</h3>
                <div style="white-space:pre-wrap; font-size:13.5px; line-height:1.6; color:var(--text-primary); max-height:400px; overflow-y:auto; background:#f9fafb; border-radius:8px; padding:16px;">{{ $contract->content }}</div>
                @if($contract->status->value === 'signed' || $contract->status->value === 'rejected')
                    <div style="margin-top:16px;">
                        <a href="{{ route('legales.contratos.print', $contract) }}" target="_blank" class="btn btn-secondary">Ver / Imprimir</a>
                    </div>
                @endif
            </div>

            {{-- Línea de tiempo --}}
            <div class="card" style="padding:24px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Línea de tiempo</h3>
                <div style="display:flex; flex-direction:column; gap:14px;">
                    @foreach($contract->events as $event)
                        <div style="display:flex; gap:12px; font-size:12.5px;">
                            <div style="width:8px; height:8px; border-radius:50%; background:var(--accent); margin-top:5px; flex-shrink:0;"></div>
                            <div>
                                <div style="font-weight:600; color:var(--text-primary);">
                                    {{ $event->event->label() }}
                                    @if($event->signer) — {{ $event->signer->name }} @endif
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
        </div>

        <div>
            {{-- Firmantes --}}
            <div class="card" style="padding:24px; margin-bottom:20px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Firmantes</h3>

                @foreach($contract->signers as $signer)
                    @php $sc = $statusColors[$signer->status->color()] ?? $statusColors['gray']; @endphp
                    <div style="padding:12px 0; border-bottom:1px solid #f3f4f6;">
                        <div style="display:flex; justify-content:space-between; align-items:start;">
                            <div>
                                <div style="font-size:13px; font-weight:600; color:var(--text-primary);">{{ $signer->name }}</div>
                                <div style="font-size:11.5px; color:var(--text-muted);">{{ $signer->role->label() }} — {{ $signer->email }}</div>
                            </div>
                            <span style="padding:2px 8px; border-radius:99px; font-size:11px; font-weight:600; background:{{ $sc['bg'] }}; color:{{ $sc['fg'] }};">
                                {{ $signer->status->label() }}
                            </span>
                        </div>
                        @if($signer->signature)
                            <div style="font-size:11px; color:var(--text-muted); margin-top:6px;">
                                Firmado {{ $signer->signed_at->format('d/m/Y H:i') }} desde IP {{ $signer->signature->ip_address }}
                            </div>
                        @endif
                    </div>
                @endforeach

                @if($contract->status->value === 'draft' || $contract->status->value === 'pending_signature')
                    <form method="POST" action="{{ route('legales.contratos.signers.store', $contract) }}" style="margin-top:16px; padding-top:16px; border-top:1px solid #f3f4f6;">
                        @csrf
                        <label class="form-label">Agregar firmante</label>
                        <select name="role" class="form-select" style="margin-bottom:8px;">
                            @foreach(\App\Enums\SignerRole::cases() as $role)
                                <option value="{{ $role->value }}">{{ $role->label() }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="name" class="form-input" placeholder="Nombre" style="margin-bottom:8px;">
                        <input type="email" name="email" class="form-input" placeholder="Email" style="margin-bottom:8px;">
                        <button type="submit" class="btn btn-secondary" style="width:100%;">Agregar</button>
                    </form>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="card" style="padding:24px;">
                <h3 style="font-size:14px; font-weight:700; color:var(--text-primary); margin:0 0 16px;">Acciones</h3>
                <div style="display:flex; flex-direction:column; gap:10px;">
                    @if(in_array($contract->status->value, ['draft', 'pending_signature']))
                        <form method="POST" action="{{ route('legales.contratos.send', $contract) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="width:100%;">Enviar a firma</button>
                        </form>
                        <form method="POST" action="{{ route('legales.contratos.mark-signed', $contract) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="width:100%;" onclick="return confirm('¿Marcar como firmado manualmente? Se registrará sin evidencia electrónica del firmante.')">
                                Marcar firmado manualmente
                            </button>
                        </form>
                        <form method="POST" action="{{ route('legales.contratos.cancel', $contract) }}">
                            @csrf
                            <button type="submit" class="btn btn-secondary" style="width:100%; color:#dc2626;" onclick="return confirm('¿Cancelar este contrato?')">
                                Cancelar contrato
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

    </div>

</x-admin-layout>
