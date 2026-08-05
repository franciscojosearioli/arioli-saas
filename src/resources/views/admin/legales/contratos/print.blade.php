<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $contract->title }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #111827; margin: 0; padding: 40px; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 16px; margin-bottom: 24px; }
        .header h1 { font-size: 20px; margin: 0 0 4px; }
        .header p { font-size: 12px; color: #6b7280; margin: 2px 0; }
        .content { white-space: pre-wrap; font-size: 13.5px; line-height: 1.7; margin-bottom: 32px; }
        .signatures { border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .signatures h3 { font-size: 12px; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; margin: 0 0 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #e5e7eb; font-size: 12.5px; }
        th { color: #6b7280; font-weight: 600; font-size: 10.5px; text-transform: uppercase; }
        .print-btn { margin-bottom: 20px; }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="print-btn">
        <button onclick="window.print()">Imprimir / Guardar como PDF</button>
    </div>

    <div class="header">
        <h1>{{ $contract->title }}</h1>
        <p>Tenant: {{ $contract->tenant_id }} · Tipo: {{ $contract->type->label() }} · Estado: {{ $contract->status->label() }}</p>
        <p>Creado: {{ $contract->created_at->format('d/m/Y') }}</p>
    </div>

    <div class="content">{{ $contract->content }}</div>

    <div class="signatures">
        <h3>Firmantes</h3>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contract->signers as $signer)
                    <tr>
                        <td>{{ $signer->name }}</td>
                        <td>{{ $signer->role->label() }}</td>
                        <td>{{ $signer->status->label() }}</td>
                        <td>{{ $signer->signed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>{{ $signer->signature?->ip_address ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p style="font-size:10.5px; color:#9ca3af; margin-top:32px;">
        Documento generado por el sistema de gestión de Arioli. La evidencia completa de cada firma
        (IP, navegador, hash del contenido) queda disponible en el panel administrativo.
    </p>

</body>
</html>
