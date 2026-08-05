@extends('layouts.app')

@section('title', 'Versión del sistema')

@section('content')
<div style="max-width:720px; margin:0 auto; padding:24px 16px;">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <div>
            <h1 style="font-size:22px; font-weight:700; color:var(--t1); margin:0;">Versión del sistema</h1>
            <p style="font-size:13px; color:var(--t2); margin:4px 0 0;">Estado de actualizaciones de Sistema de Salud</p>
        </div>
        <a href="{{ route('system-version.index') }}"
           style="display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:var(--bg); color:var(--t2); border:1px solid var(--border);">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Verificar actualización
        </a>
    </div>

    @if(!$status)
        <div style="background:var(--err-bg); border:1px solid var(--err-bd); border-radius:10px; padding:16px 20px; color:var(--err); font-size:13px;">
            No se pudo obtener la información de versión. Verificá la conexión con el servidor de actualizaciones.
        </div>
    @else

        @if($status->updateAvailable)
        <div style="background:var(--war-bg); border:1px solid var(--war-bd); border-radius:10px; padding:14px 18px; margin-bottom:20px; display:flex; gap:12px; align-items:flex-start;">
            <span style="font-size:18px; flex-shrink:0;">⬆️</span>
            <div style="flex:1;">
                <p style="font-size:13px; font-weight:700; color:var(--war); margin:0 0 4px;">Nueva versión disponible: v{{ $status->latestVersion }}</p>
                <p style="font-size:13px; color:var(--t2); margin:0 0 12px;">Tu sistema está en v{{ $status->currentVersion }}. Podés actualizar ahora.</p>
                <form method="POST" action="{{ route('system-version.update') }}">
                    @csrf
                    <button type="submit"
                        onclick="return confirm('¿Actualizar el sistema a la versión {{ $status->latestVersion }}? El proceso puede tomar unos minutos.')"
                        style="display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600; border:none; cursor:pointer; background:var(--war); color:#fff;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:14px;height:14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Actualizar ahora
                    </button>
                </form>
            </div>
        </div>
        @elseif($status->latestVersion)
        <div style="background:var(--ok-bg); border:1px solid var(--ok-bd); border-radius:10px; padding:14px 18px; margin-bottom:20px; display:flex; gap:12px; align-items:center;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;flex-shrink:0;color:var(--ok);">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p style="font-size:13px; font-weight:600; color:var(--ok); margin:0;">El sistema está actualizado.</p>
        </div>
        @endif

        <div class="card" style="border-radius:10px; overflow:hidden; border:1px solid var(--border);">
            @php
            $rows = [
                ['Versión instalada',      'v' . $status->currentVersion],
                ['Última versión',         $status->latestVersion ? 'v' . $status->latestVersion : '—'],
                ['Actualización disponible', $status->updateAvailable ? 'Sí' : 'No'],
                ['API de actualizaciones', $status->apiReachable ? 'Conectada' : 'Sin conexión'],
            ];
            @endphp
            <table style="width:100%; border-collapse:collapse;">
                @foreach($rows as $i => [$label, $value])
                <tr style="border-bottom:{{ $i < count($rows)-1 ? '1px solid var(--border)' : 'none' }}; background:{{ $i % 2 === 0 ? 'var(--card)' : 'var(--bg)' }};">
                    <td style="padding:12px 20px; font-size:13px; color:var(--t2); width:220px; font-weight:500;">{{ $label }}</td>
                    <td style="padding:12px 20px; font-size:13px; color:var(--t1); font-weight:600; font-family:var(--font-mono);">
                        @if($label === 'Actualización disponible')
                            @if($status->updateAvailable)
                                <span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:700;background:var(--war-bg);color:var(--war);font-family:var(--font-sans);">Disponible</span>
                            @else
                                <span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:700;background:var(--ok-bg);color:var(--ok);font-family:var(--font-sans);">Al día</span>
                            @endif
                        @elseif($label === 'API de actualizaciones')
                            @if($status->apiReachable)
                                <span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:700;background:var(--ok-bg);color:var(--ok);font-family:var(--font-sans);">Conectada</span>
                            @else
                                <span style="display:inline-block;padding:2px 10px;border-radius:99px;font-size:11px;font-weight:700;background:var(--err-bg);color:var(--err);font-family:var(--font-sans);">Sin conexión</span>
                            @endif
                        @else
                            {{ $value }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>

    @endif

</div>
@endsection
