<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Préstamos de Herramientas - INFRASTOCK</title>
    <style>
        @page { margin-top: 2cm; margin-bottom: 2cm; margin-left: 1cm; margin-right: 1cm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9px; line-height: 1.4; color: #333; background: #fff; }
        .container { padding: 0; }
        .header-container { width: 100%; margin-bottom: 15px; border-bottom: 3px solid #1565C0; padding-bottom: 10px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-left { width: 80%; vertical-align: middle; padding-right: 10px; }
        .header-right { width: 20%; text-align: right; vertical-align: middle; }
        .header-right img { height: 70px; width: auto; }
        .institution-name { font-size: 14px; font-weight: bold; color: #1565C0; margin-bottom: 3px; }
        .institution-region { font-size: 11px; color: #333; margin-bottom: 2px; }
        .institution-area { font-size: 10px; color: #555; margin-bottom: 2px; }
        .institution-responsible { font-size: 10px; color: #555; }
        .title-section { background-color: #1565C0; color: white; padding: 12px 15px; text-align: center; margin-bottom: 12px; }
        .title-section h1 { font-size: 16px; margin-bottom: 3px; letter-spacing: 1px; }
        .title-section .subtitle { font-size: 10px; }
        .info-section { width: 100%; margin-bottom: 12px; background-color: #f8f9fa; padding: 8px 12px; border-left: 4px solid #1565C0; }
        .info-table { width: 100%; }
        .info-table td { font-size: 9px; color: #555; padding: 2px 15px 2px 0; }
        .info-table strong { color: #333; }
        .summary-section { width: 100%; margin-bottom: 12px; background-color: #E3F2FD; padding: 10px; border: 1px solid #BBDEFB; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { text-align: center; padding: 5px 8px; border-right: 1px solid #90CAF9; }
        .summary-table td:last-child { border-right: none; }
        .summary-number { font-size: 14px; font-weight: bold; color: #1565C0; display: block; }
        .summary-label { font-size: 7px; color: #555; text-transform: uppercase; display: block; margin-top: 2px; }
        .summary-good { color: #2E7D32 !important; }
        .summary-warning { color: #F57F17 !important; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 7px; margin-top: 10px; }
        .data-table thead { background-color: #1565C0; }
        .data-table th { padding: 8px 4px; text-align: center; font-weight: bold; font-size: 8px; text-transform: uppercase; letter-spacing: 0.3px; border: 1px solid #0D47A1; color: #FFFFFF; background-color: #1565C0; }
        .data-table tbody tr:nth-child(even) { background-color: #f5f5f5; }
        .data-table td { padding: 5px 3px; text-align: center; vertical-align: middle; border: 1px solid #ddd; font-size: 7px; }
        .data-table td.text-left { text-align: left; }
        .role-prestamo { background-color: #BBDEFB; color: #0D47A1; padding: 2px 4px; font-weight: bold; font-size: 6px; }
        .role-devolucion { background-color: #C8E6C9; color: #2E7D32; padding: 2px 4px; font-weight: bold; font-size: 6px; }
        .status-approved { background-color: #C8E6C9; color: #2E7D32; padding: 2px 4px; font-weight: bold; font-size: 6px; }
        .status-pending { background-color: #FFF9C4; color: #F57F17; padding: 2px 4px; font-weight: bold; font-size: 6px; }
        .status-rejected { background-color: #FFCDD2; color: #C62828; padding: 2px 4px; font-weight: bold; font-size: 6px; }
        .stats-section { width: 100%; margin-bottom: 12px; }
        .stats-section h3 { font-size: 10px; font-weight: bold; color: #333; margin-bottom: 5px; }
        .stats-table { width: 100%; border-collapse: collapse; font-size: 8px; }
        .stats-table td { padding: 3px 6px; border: 1px solid #ddd; }
        .stats-table td:first-child { font-weight: bold; background-color: #f8f9fa; }
        .footer { margin-top: 15px; padding-top: 10px; border-top: 2px solid #e0e0e0; text-align: center; color: #666; font-size: 7px; }
    </style>
</head>
<body>
    @php
        $logoPath = public_path('assets/img/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }
        $statusLabels = ['approved' => 'Aprobado', 'pending' => 'Pendiente', 'rejected' => 'Rechazado'];
    @endphp

    <div class="container">
        <div class="header-container">
            <table class="header-table">
                <tr>
                    <td class="header-left">
                        <div class="institution-name">CENTRO DE FORMACIÓN AGROINDUSTRIAL LA ANGOSTURA</div>
                        <div class="institution-region">Regional Huila</div>
                        <div class="institution-area"><strong>Área:</strong> Infraestructura</div>
                        <div class="institution-responsible"><strong>Responsable:</strong> {{ auth()->user()->name ?? 'Administrador del Sistema' }}</div>
                    </td>
                    <td class="header-right">
                        @if($logoBase64)
                            <img src="{{ $logoBase64 }}" alt="Logo INFRASTOCK">
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="title-section">
            <h1>PRÉSTAMOS DE HERRAMIENTAS</h1>
            <div class="subtitle">{{ $periodLabel }} — Sistema de Gestión INFRASTOCK</div>
        </div>

        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td><strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i:s') }}</td>
                    <td><strong>Período:</strong> {{ $periodLabel }}</td>
                    <td><strong>Total de registros:</strong> {{ $loans->count() }}</td>
                </tr>
            </table>
        </div>

        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td>
                        <span class="summary-number">{{ $stats['total'] }}</span>
                        <span class="summary-label">Total Movimientos</span>
                    </td>
                    <td>
                        <span class="summary-number">{{ $stats['prestamos'] }}</span>
                        <span class="summary-label">Préstamos</span>
                    </td>
                    <td>
                        <span class="summary-number summary-good">{{ $stats['devoluciones'] }}</span>
                        <span class="summary-label">Devoluciones</span>
                    </td>
                    <td>
                        <span class="summary-number summary-warning">{{ $stats['pendientes'] }}</span>
                        <span class="summary-label">Pendientes</span>
                    </td>
                </tr>
            </table>
        </div>

        @if($stats['topTools']->count() > 0)
        <div class="stats-section">
            <h3>🔧 Herramientas Más Solicitadas</h3>
            <table class="stats-table">
                @foreach($stats['topTools'] as $top)
                <tr>
                    <td>{{ $top->tool->nombre ?? 'N/A' }}</td>
                    <td>{{ $top->total_loans }} préstamo(s)</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20px;">#</th>
                    <th style="width: 80px;">HERRAMIENTA</th>
                    <th style="width: 70px;">USUARIO / PRESTATARIO</th>
                    <th style="width: 35px;">CANT.</th>
                    <th style="width: 55px;">MOVIMIENTO</th>
                    <th style="width: 50px;">ESTADO</th>
                    <th style="width: 55px;">FECHA</th>
                    <th style="width: 120px;">DESCRIPCIÓN</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loans as $index => $loan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left">{{ $loan->tool->nombre ?? 'N/A' }} {{ $loan->tool->placa ? '['.$loan->tool->placa.']' : '' }}</td>
                        <td class="text-left">{{ $loan->user->person->first_name ?? 'N/A' }} {{ $loan->user->person->first_last_name ?? '' }}</td>
                        <td>{{ $loan->amount ?? 'N/A' }}</td>
                        <td><span class="role-{{ $loan->role === 'Préstamo' ? 'prestamo' : 'devolucion' }}">{{ $loan->role }}</span></td>
                        <td><span class="status-{{ $loan->status ?? 'pending' }}">{{ $statusLabels[$loan->status] ?? 'N/A' }}</span></td>
                        <td>{{ $loan->created_at->format('d/m/Y') }}</td>
                        <td class="text-left">{{ Str::limit($loan->description ?? '-', 60) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <div>Documento generado automáticamente por el Sistema INFRASTOCK</div>
            <div>© {{ date('Y') }} SENA - Centro de Formación Agroindustrial "La Angostura" | Todos los derechos reservados</div>
        </div>
    </div>
</body>
</html>
