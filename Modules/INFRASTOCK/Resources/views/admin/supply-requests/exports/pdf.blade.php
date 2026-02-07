<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Consumos de Insumos - INFRASTOCK</title>
    <style>
        @page {
            margin-top: 2cm;
            margin-bottom: 2cm;
            margin-left: 1cm;
            margin-right: 1cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
            color: #333;
            background-color: #fff;
        }

        .container {
            padding: 0;
        }

        /* Header con logo */
        .header {
            position: relative;
            padding-bottom: 15px;
            border-bottom: 3px solid #1E88E5;
            margin-bottom: 15px;
        }

        .header-logo {
            position: absolute;
            top: 0;
            right: 0;
            width: 90px;
            height: auto;
            object-fit: contain;
        }

        .header h1 {
            font-size: 16px;
            color: #1565C0;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 10px;
            color: #666;
            margin-bottom: 2px;
        }

        .institution {
            font-size: 10px;
            font-weight: bold;
            color: #333;
            margin-top: 6px;
        }

        .institution-details {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
        }

        .institution-details div {
            margin-bottom: 1px;
        }

        .report-info {
            text-align: center;
            margin: 12px 0;
            padding: 8px;
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            border-radius: 6px;
        }

        .report-info .period-label {
            font-size: 12px;
            font-weight: bold;
            color: #1565C0;
        }

        .report-info .generated {
            font-size: 8px;
            color: #666;
            margin-top: 3px;
        }

        /* Estadísticas */
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }

        .stats-row {
            display: table-row;
        }

        .stat-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 8px 4px;
            background: #f8f9fa;
            border: 1px solid #e0e0e0;
        }

        .stat-box:first-child {
            border-radius: 6px 0 0 6px;
        }

        .stat-box:last-child {
            border-radius: 0 6px 6px 0;
        }

        .stat-label {
            font-size: 7px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 14px;
            font-weight: bold;
            color: #1565C0;
            margin-top: 2px;
        }

        /* Top Consumidos */
        .top-section {
            margin-bottom: 15px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #FFB74D;
        }

        .top-header {
            background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);
            color: white;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .top-table {
            width: 100%;
            border-collapse: collapse;
        }

        .top-table th {
            background-color: #FFF3E0;
            color: #E65100;
            font-size: 7px;
            padding: 6px 8px;
            text-align: center;
            text-transform: uppercase;
            border-bottom: 1px solid #FFB74D;
        }

        .top-table td {
            padding: 6px 8px;
            text-align: center;
            border-bottom: 1px solid #FFF3E0;
            font-size: 8px;
        }

        .top-table tr:nth-child(even) {
            background-color: #FFF8E1;
        }

        .top-rank {
            font-weight: bold;
            color: #FF6F00;
        }

        .top-name {
            font-weight: bold;
            color: #333;
        }

        .top-value {
            color: #1565C0;
            font-weight: bold;
        }

        /* Tabla de detalle */
        .detail-section {
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #43A047;
        }

        .detail-header {
            background: linear-gradient(135deg, #43A047 0%, #2E7D32 100%);
            color: white;
            padding: 8px 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table th {
            background-color: #E8F5E9;
            color: #1B5E20;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 5px;
            text-align: center;
            border-bottom: 2px solid #81C784;
        }

        .detail-table td {
            padding: 5px 5px;
            border-bottom: 1px solid #E0E0E0;
            font-size: 8px;
            vertical-align: middle;
        }

        .detail-table tr:nth-child(even) {
            background-color: #F5F5F5;
        }

        .detail-table tr:hover {
            background-color: #E8F5E9;
        }

        /* Columnas específicas */
        .col-num {
            width: 5%;
            text-align: center;
        }

        .col-name {
            width: 18%;
            font-weight: 500;
        }

        .col-cat {
            width: 12%;
        }

        .col-qty {
            width: 8%;
            text-align: center;
            font-weight: bold;
            color: #1565C0;
        }

        .col-unit {
            width: 8%;
            text-align: center;
        }

        .col-user {
            width: 15%;
        }

        .col-role {
            width: 10%;
            text-align: center;
        }

        .col-date {
            width: 10%;
            text-align: center;
        }

        .col-dest {
            width: 14%;
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-operario {
            background-color: #E3F2FD;
            color: #1565C0;
        }

        .badge-aseo {
            background-color: #E8F5E9;
            color: #2E7D32;
        }

        .badge-convivencia {
            background-color: #FFF3E0;
            color: #E65100;
        }

        .badge-ganaderia {
            background-color: #F3E5F5;
            color: #7B1FA2;
        }

        .badge-default {
            background-color: #F5F5F5;
            color: #616161;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #E0E0E0;
            text-align: center;
            font-size: 7px;
            color: #999;
        }

        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            @if($base64Logo)
                <img src="{{ $base64Logo }}" alt="Logo" class="header-logo">
            @endif
            <div>
                <h1>📦 REPORTE DE CONSUMOS DE INSUMOS</h1>
                <div class="subtitle">Sistema de Gestión INFRASTOCK</div>
                <div class="institution">Centro de Formación Agroindustrial "La Angostura" - Campoalegre, Huila</div>
                <div class="institution-details">
                    <div><strong>Regional:</strong> Huila</div>
                    <div><strong>Área:</strong> Infraestructura</div>
                    <div><strong>Responsable:</strong> {{ $adminName }}</div>
                </div>
            </div>
        </div>

        <!-- Info del reporte -->
        <div class="report-info">
            <div class="period-label">📅 Período: {{ $periodLabel }}</div>
            <div class="generated">Generado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }}</div>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-label">Total Solicitudes</div>
                    <div class="stat-value">{{ $stats['total_requests'] }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Total Unidades Consumidas</div>
                    <div class="stat-value">{{ number_format($stats['total_units']) }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Insumos Diferentes</div>
                    <div class="stat-value">{{ $stats['unique_supplies'] }}</div>
                </div>
            </div>
        </div>

        <!-- Top Consumidos -->
        @if($topConsumed->count() > 0)
        <div class="top-section">
            <div class="top-header">🔥 TOP 5 - INSUMOS MÁS CONSUMIDOS</div>
            <table class="top-table">
                <thead>
                    <tr>
                        <th style="width: 10%">Posición</th>
                        <th style="width: 35%">Insumo</th>
                        <th style="width: 20%">Categoría</th>
                        <th style="width: 20%">Cantidad Total</th>
                        <th style="width: 15%">% del Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topConsumed as $index => $top)
                    <tr>
                        <td class="top-rank">#{{ $index + 1 }}</td>
                        <td class="top-name">{{ $top->equipment->name ?? 'N/A' }}</td>
                        <td>{{ $top->equipment->category->name ?? 'Sin categoría' }}</td>
                        <td class="top-value">{{ number_format($top->total_consumed) }} {{ $top->equipment->unit ?? 'und' }}</td>
                        <td>{{ $stats['total_units'] > 0 ? number_format(($top->total_consumed / $stats['total_units']) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Detalle de Consumos -->
        <div class="detail-section">
            <div class="detail-header">📋 DETALLE DE CONSUMOS ({{ $consumptions->count() }} registros)</div>
            @if($consumptions->count() > 0)
            <table class="detail-table">
                <thead>
                    <tr>
                        <th class="col-num">#</th>
                        <th class="col-name">Insumo</th>
                        <th class="col-cat">Categoría</th>
                        <th class="col-qty">Cant.</th>
                        <th class="col-unit">Unidad</th>
                        <th class="col-user">Solicitante</th>
                        <th class="col-role">Rol</th>
                        <th class="col-date">Fecha</th>
                        <th class="col-dest">Destino</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($consumptions as $index => $consumption)
                    @php
                        $roleName = 'Usuario';
                        $badgeClass = 'badge-default';
                        if ($consumption->user) {
                            $userRoles = $consumption->user->roles->pluck('name')->toArray();
                            if (in_array('Operario', $userRoles)) {
                                $roleName = 'Operario';
                                $badgeClass = 'badge-operario';
                            } elseif (in_array('Aseo', $userRoles)) {
                                $roleName = 'Aseo';
                                $badgeClass = 'badge-aseo';
                            } elseif (in_array('Centro de Convivencia', $userRoles)) {
                                $roleName = 'Convivencia';
                                $badgeClass = 'badge-convivencia';
                            } elseif (in_array('Ganadería', $userRoles)) {
                                $roleName = 'Ganadería';
                                $badgeClass = 'badge-ganaderia';
                            } elseif (!empty($userRoles)) {
                                $roleName = $userRoles[0];
                            }
                        }
                    @endphp
                    <tr>
                        <td class="col-num">{{ $index + 1 }}</td>
                        <td class="col-name">{{ $consumption->equipment->name ?? 'N/A' }}</td>
                        <td class="col-cat">{{ $consumption->equipment->category->name ?? 'Sin cat.' }}</td>
                        <td class="col-qty">{{ $consumption->amount }}</td>
                        <td class="col-unit">{{ $consumption->equipment->unit ?? 'und' }}</td>
                        <td class="col-user">{{ $consumption->user->name ?? 'N/A' }}</td>
                        <td class="col-role"><span class="badge {{ $badgeClass }}">{{ $roleName }}</span></td>
                        <td class="col-date">{{ $consumption->created_at->format('d/m/Y') }}</td>
                        <td class="col-dest">{{ $consumption->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="no-data">
                No hay consumos registrados para el período seleccionado.
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Documento generado automáticamente por el Sistema INFRASTOCK - Centro de Formación Agroindustrial "La Angostura"</p>
            <p>Este documento es de uso interno y confidencial.</p>
        </div>
    </div>
</body>
</html>
