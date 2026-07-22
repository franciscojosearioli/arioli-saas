@extends('layouts.app')

@section('title', 'Licencia')

@section('content')
<div style="max-width:720px; margin:0 auto; padding:24px 16px;">

    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
        <div>
            <h1 style="font-size:22px; font-weight:700; color:#111827; margin:0;">Licencia</h1>
            <p style="font-size:13px; color:#6b7280; margin:4px 0 0;">Información de tu suscripción a {{ $info?->product ?? 'Historias Clínicas' }}</p>
        </div>
        @if($info?->isDemo())
        <a href="{{ config('saas.landing_url') }}" target="_blank"
           style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:#f59e0b; color:#78350f;">
            Contratar licencia comercial
        </a>
        @else
        <a href="{{ config('saas.client_url') }}" target="_blank"
           style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px; border-radius:8px; font-size:13px; font-weight:600; text-decoration:none; background:#2563eb; color:#fff;">
            Administrar licencia
        </a>
        @endif
    </div>

    @if($info?->isDemo())
    <div style="background:#fef3c7; border:1px solid #f59e0b; border-radius:10px; padding:14px 18px; margin-bottom:20px; display:flex; gap:12px; align-items:flex-start;">
        <span style="font-size:18px; flex-shrink:0;">⚡</span>
        <div>
            <p style="font-size:13px; font-weight:700; color:#78350f; margin:0 0 2px;">Licencia de Demostración</p>
            <p style="font-size:13px; color:#92400e; margin:0;">Este es un entorno de demostración. Los datos se restablecen automáticamente cada 24 horas.</p>
        </div>
    </div>
    @endif

    <div class="card" style="border-radius:10px; overflow:hidden; border:1px solid #e5e7eb;">
        @if(!$info)
            <p style="text-align:center; padding:32px; color:#6b7280; font-size:14px;">No se pudo obtener la información de licencia.</p>
        @else
        @php
        $rows = [
            ['Producto',            $info->product],
            ['Plan',                $info->plan],
            ['Tipo de licencia',    $info->typeLabel()],
            ['Estado',              $info->active ? 'Activa' : 'Inactiva'],
            ['Fecha de activación', $info->startsAt ? \Carbon\Carbon::parse($info->startsAt)->format('d/m/Y') : '—'],
            ['Vencimiento',         $info->isDemo() ? 'Sin vencimiento' : ($info->expiresAt ? \Carbon\Carbon::parse($info->expiresAt)->format('d/m/Y') : '—')],
            ['Días restantes',      $info->isDemo() ? '∞' : ($info->daysRemaining !== null ? $info->daysRemaining . ' días' : '—')],
            ['Dominio',             $info->domain],
            ['Versión instalada',   $info->installedVersion ?: '—'],
            ['Última versión',      $info->latestVersion ?: '—'],
            ['Última validación',   $info->lastValidatedAt ? \Carbon\Carbon::parse($info->lastValidatedAt)->format('d/m/Y H:i') : '—'],
        ];
        @endphp
        <table style="width:100%; border-collapse:collapse;">
            @foreach($rows as $i => [$label, $value])
            <tr style="border-bottom:{{ $i < count($rows)-1 ? '1px solid #f3f4f6' : 'none' }}; background:{{ $i % 2 === 0 ? '#fff' : '#f9fafb' }};">
                <td style="padding:12px 20px; font-size:13px; color:#6b7280; width:220px; font-weight:500;">{{ $label }}</td>
                <td style="padding:12px 20px; font-size:13px; color:#111827; font-weight:600;">{{ $value }}</td>
            </tr>
            @endforeach
        </table>
        @endif
    </div>

</div>
@endsection
