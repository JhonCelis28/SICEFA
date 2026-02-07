<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Consumo por Área - INFRASTOCK</title>
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
            border-bottom: 3px solid #10B981;
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
            color: #059669;
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
            background: linear-gradient(135deg, #D1FAE5 0%, #A7F3D0 100%);
            border-radius: 6px;
        }
        .report-info .period-label { font-size: 12px; font-weight: bold; color: #059669; }
        .report-info .generated { font-size: 8px; color: #666; margin-top: 3px; }
        
        .stats-grid { display: table; width: 100%; margin-bottom: 15px; }
        .stats-row { display: table-row; }
        .stat-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 8px 4px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
        }
        .stat-box:first-child { border-radius: 6px 0 0 6px; }
        .stat-box:last-child { border-radius: 0 6px 6px 0; }
        .stat-label { font-size: 7px; color: #666; text-transform: uppercase; }
        .stat-value { font-size: 14px; font-weight: bold; color: #059669; margin-top: 2px; }
        
        .section { margin-bottom: 15px; border-radius: 6px; overflow: hidden; border: 1px solid #10B981; }
        .section-header {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: bold;
        }
        
        table { width: 100%; border-collapse: collapse; }
        th {
            background-color: #D1FAE5;
            color: #065F46;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 5px;
            text-align: center;
            border-bottom: 2px solid #10B981;
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
            <h1>📊 CONSUMO DE INSUMOS POR ÁREA</h1>
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
                <div class="stat-label">Total Consumido</div>
                <div class="stat-value">{{ number_format($totalConsumed) }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Áreas con Consumo</div>
                <div class="stat-value">{{ $consumptionByArea->count() }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Transacciones</div>
                <div class="stat-value">{{ $consumptionDetails->count() }}</div>
            </div>
        </div>
    </div>

    @if($topSupplies->count() > 0)
    <div class="section">
        <div class="section-header">🏆 TOP 10 INSUMOS MÁS CONSUMIDOS</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%">#</th>
                    <th style="width: 50%">Insumo</th>
                    <th style="width: 20%">Cantidad</th>
                    <th style="width: 20%">% del Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSupplies as $index => $supply)
                <tr>
                    <td class="text-center font-bold">{{ $index + 1 }}</td>
                    <td>{{ $supply->equipment->name ?? 'N/A' }}</td>
                    <td class="text-center font-bold" style="color: #059669;">{{ number_format($supply->total_consumed) }}</td>
                    <td class="text-center">{{ $totalConsumed > 0 ? number_format(($supply->total_consumed / $totalConsumed) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="section-header">📋 RESUMEN POR ÁREA</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 10%">#</th>
                    <th style="width: 50%">Área</th>
                    <th style="width: 20%">Cantidad Consumida</th>
                    <th style="width: 20%">% del Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consumptionByArea as $index => $area)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="font-bold">{{ $area->area_name }}</td>
                    <td class="text-center font-bold" style="color: #059669;">{{ number_format($area->total_amount) }}</td>
                    <td class="text-center">{{ $totalConsumed > 0 ? number_format(($area->total_amount / $totalConsumed) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-header">📝 DETALLE DE CONSUMOS (Últimos 30)</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 12%">Fecha</th>
                    <th style="width: 25%">Insumo</th>
                    <th style="width: 15%">Categoría</th>
                    <th style="width: 10%">Cantidad</th>
                    <th style="width: 20%">Área</th>
                    <th style="width: 18%">Solicitante</th>
                </tr>
            </thead>
            <tbody>
                @foreach($consumptionDetails->take(30) as $detail)
                <tr>
                    <td class="text-center">{{ $detail->created_at->format('d/m/Y') }}</td>
                    <td>{{ $detail->equipment->name ?? 'N/A' }}</td>
                    <td>{{ $detail->equipment->category->name ?? 'Sin cat.' }}</td>
                    <td class="text-center font-bold">{{ $detail->amount }}</td>
                    <td>{{ $detail->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }}</td>
                    <td>{{ $detail->user->person->first_name ?? '' }} {{ $detail->user->person->first_last_name ?? 'N/A' }}</td>
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
