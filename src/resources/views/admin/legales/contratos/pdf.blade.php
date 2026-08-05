<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
        .header { border-bottom: 2px solid #111827; padding-bottom: 12px; margin-bottom: 20px; }
        .header h1 { font-size: 16px; margin: 0 0 4px; }
        .header p { font-size: 10px; color: #6b7280; margin: 2px 0; }
        .content { white-space: pre-wrap; font-size: 11px; line-height: 1.6; margin-bottom: 24px; }
        .signatures h3 { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; margin: 0 0 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 10px; }
        th { color: #6b7280; font-size: 9px; text-transform: uppercase; }
        .footer { font-size: 8.5px; color: #9ca3af; margin-top: 24px; }
    </style>
</head>
<body>

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
                        <td>{{ $signer->signed_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $signer->signature?->ip_address ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="footer">
        Documento generado por el sistema de gestión de Arioli. La evidencia completa de cada firma
        (IP, navegador, hash del contenido) queda disponible en el panel administrativo.
    </p>

</body>
</html>
