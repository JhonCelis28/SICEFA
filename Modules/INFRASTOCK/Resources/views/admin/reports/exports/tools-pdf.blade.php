<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Herramientas por Instructor - INFRASTOCK</title>
    <style>
        @page {
            margin-top: 2cm;
            margin-bottom: 2cm;
            margin-left: 1cm;
            margin-right: 1cm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
        }
        .header {
            position: relative;
            padding-bottom: 15px;
            border-bottom: 3px solid #3B82F6;
            margin-bottom: 15px;
        }
        .header-logo {
            position: absolute;
            top: 0;
            right: 0;
            width: 80px;
            height: auto;
        }
        .header h1 {
            font-size: 16px;
            color: #1D4ED8;
            margin-bottom: 4px;
        }
        .subtitle { font-size: 10px; color: #666; margin-bottom: 2px; }
        .institution { font-size: 10px; font-weight: bold; color: #333; margin-top: 6px; }
        .institution-details { font-size: 8px; color: #666; margin-top: 3px; }
        .institution-details div { margin-bottom: 1px; }
        
        .report-info {
            text-align: center;
            margin: 12px 0;
            padding: 8px;
            background: linear-gradient(135deg, #DBEAFE 0%, #BFDBFE 100%);
            border-radius: 6px;
        }
        .report-info .period-label { font-size: 12px; font-weight: bold; color: #1D4ED8; }
        .report-info .generated { font-size: 8px; color: #666; margin-top: 3px; }
        
        .stats-grid { display: table; width: 100%; margin-bottom: 15px; }
        .stats-row { display: table-row; }
        .stat-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 8px 4px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
        }
        .stat-box:first-child { border-radius: 6px 0 0 6px; }
        .stat-box:last-child { border-radius: 0 6px 6px 0; }
        .stat-label { font-size: 7px; color: #666; text-transform: uppercase; }
        .stat-value { font-size: 14px; font-weight: bold; color: #1D4ED8; margin-top: 2px; }
        
        .section { margin-bottom: 15px; border-radius: 6px; overflow: hidden; border: 1px solid #3B82F6; }
        .section-header {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            color: white;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: bold;
        }
        
        table { width: 100%; border-collapse: collapse; }
        th {
            background-color: #DBEAFE;
            color: #1E40AF;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 5px;
            text-align: center;
            border-bottom: 2px solid #3B82F6;
        }
        td {
            padding: 5px 5px;
            border-bottom: 1px solid #E0E0E0;
            font-size: 8px;
        }
        tr:nth-child(even) { background-color: #F5F5F5; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #E0E0E0;
            text-align: center;
            font-size: 7px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        @if($base64Logo)
            <img src="{{ $base64Logo }}" alt="Logo" class="header-logo">
        @endif
        <div>
            <h1>🔧 USO DE HERRAMIENTAS POR INSTRUCTOR</h1>
            <div class="subtitle">Sistema de Gestión INFRASTOCK</div>
            <div class="institution">Centro de Formación Agroindustrial "La Angostura" - Campoalegre, Huila</div>
            <div class="institution-details">
                <div><strong>Regional:</strong> Huila</div>
                <div><strong>Área:</strong> Infraestructura</div>
                <div><strong>Responsable:</strong> {{ $adminName }}</div>
            </div>
        </div>
    </div>

    <div class="report-info">
        <div class="period-label">📅 Período: {{ $periodLabel }}</div>
        <div class="generated">Generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</div>
    </div>

    <div class="stats-grid">
        <div class="stats-row">
            <div class="stat-box">
                <div class="stat-label">Total Préstamos</div>
                <div class="stat-value">{{ number_format($totalLoans) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Instructores Activos</div>
                <div class="stat-value">{{ $loansByInstructor->count() }}</div>
            </div>
        </div>
    </div>

    @if($topTools->count() > 0)
    <div class="section">
        <div class="section-header">🏆 TOP 10 HERRAMIENTAS MÁS PRESTADAS</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%">#</th>
                    <th style="width: 50%">Herramienta</th>
                    <th style="width: 20%">Total Préstamos</th>
                    <th style="width: 20%">% del Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topTools as $index => $tool)
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td>{{ $tool->tool->name ?? 'N/A' }}</td>
                    <td class="text-center font-bold" style="color: #1D4ED8;">{{ number_format($tool->total_loans) }}</td>
                    <td class="text-center">{{ $totalLoans > 0 ? number_format(($tool->total_loans / $totalLoans) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-header">👤 RESUMEN POR INSTRUCTOR</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%">#</th>
                    <th style="width: 50%">Instructor</th>
                    <th style="width: 20%">Total Préstamos</th>
                    <th style="width: 20%">% del Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loansByInstructor as $index => $instructor)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $instructor->user_name }} @if($instructor->user_nickname) <span style="color: #666; font-weight: normal;">({{ $instructor->user_nickname }})</span> @endif</td>
                    <td class="text-center font-bold" style="color: #1D4ED8;">{{ number_format($instructor->total_loans) }}</td>
                    <td class="text-center">{{ $totalLoans > 0 ? number_format(($instructor->total_loans / $totalLoans) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-header">📝 DETALLE DE PRÉSTAMOS (Últimos 30)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 15%">Fecha</th>
                    <th style="width: 35%">Herramienta</th>
                    <th style="width: 35%">Instructor</th>
                    <th style="width: 15%">Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($loanDetails->take(30) as $detail)
                <tr>
                    <td class="text-center">{{ $detail->created_at->format('d/m/Y') }}</td>
                    <td>{{ $detail->tool->name ?? 'N/A' }}</td>
                    <td>{{ $detail->user->person->first_name ?? '' }} {{ $detail->user->person->first_last_name ?? 'N/A' }}</td>
                    <td class="text-center font-bold">{{ $detail->amount }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Documento generado automáticamente por el Sistema INFRASTOCK - Centro de Formación Agroindustrial "La Angostura"</p>
        <p>Este documento es de uso interno y confidencial.</p>
    </div>
</body>
</html>
