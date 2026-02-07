<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Inventario de Insumos - INFRASTOCK</title>
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
            line-height: 1.4;
            color: #333;
            background: #fff;
        }
        
        .container {
            padding: 0;
        }
        
        /* Header con logo */
        .header-container {
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 3px solid #1E88E5;
            padding-bottom: 10px;
        }
        
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .header-left {
            width: 80%;
            vertical-align: middle;
            padding-right: 10px;
        }
        
        .header-right {
            width: 20%;
            text-align: right;
            vertical-align: middle;
        }
        
        .header-right img {
            height: 70px;
            width: auto;
        }
        
        .institution-name {
            font-size: 14px;
            font-weight: bold;
            color: #1565C0;
            margin-bottom: 3px;
        }
        
        .institution-region {
            font-size: 11px;
            color: #333;
            margin-bottom: 2px;
        }
        
        .institution-area {
            font-size: 10px;
            color: #555;
            margin-bottom: 2px;
        }
        
        .institution-responsible {
            font-size: 10px;
            color: #555;
        }
        
        /* Title Section */
        .title-section {
            background-color: #1E88E5;
            color: white;
            padding: 12px 15px;
            text-align: center;
            margin-bottom: 12px;
        }
        
        .title-section h1 {
            font-size: 16px;
            margin-bottom: 3px;
            letter-spacing: 1px;
        }
        
        .title-section .subtitle {
            font-size: 10px;
        }
        
        /* Info Section */
        .info-section {
            width: 100%;
            margin-bottom: 12px;
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-left: 4px solid #43A047;
        }
        
        .info-table {
            width: 100%;
        }
        
        .info-table td {
            font-size: 9px;
            color: #555;
            padding: 2px 15px 2px 0;
        }
        
        .info-table strong {
            color: #333;
        }
        
        /* Summary Section */
        .summary-section {
            width: 100%;
            margin-bottom: 12px;
            background-color: #e3f2fd;
            padding: 10px;
            border: 1px solid #bbdefb;
        }
        
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .summary-table td {
            text-align: center;
            padding: 5px 8px;
            border-right: 1px solid #90caf9;
        }
        
        .summary-table td:last-child {
            border-right: none;
        }
        
        .summary-number {
            font-size: 14px;
            font-weight: bold;
            color: #1565C0;
            display: block;
        }
        
        .summary-label {
            font-size: 7px;
            color: #555;
            text-transform: uppercase;
            display: block;
            margin-top: 2px;
        }
        
        .summary-good {
            color: #2E7D32 !important;
        }
        
        .summary-critical {
            color: #C62828 !important;
        }
        
        /* Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7px;
            margin-top: 10px;
        }
        
        .data-table thead {
            background-color: #43A047;
        }
        
        .data-table th {
            padding: 8px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #2E7D32;
            color: #FFFFFF;
            background-color: #43A047;
        }
        
        .data-table tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        
        .data-table td {
            padding: 5px 3px;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #ddd;
            font-size: 7px;
        }
        
        .data-table td.text-left {
            text-align: left;
        }
        
        /* Status Colors */
        .status-disponible {
            background-color: #C8E6C9;
            color: #2E7D32;
            padding: 2px 4px;
            font-weight: bold;
            font-size: 6px;
        }
        
        .status-agotado {
            background-color: #FFCDD2;
            color: #C62828;
            padding: 2px 4px;
            font-weight: bold;
            font-size: 6px;
        }
        
        .status-vencido {
            background-color: #FFE0B2;
            color: #E65100;
            padding: 2px 4px;
            font-weight: bold;
            font-size: 6px;
        }
        
        .status-bajo_stock {
            background-color: #FFF9C4;
            color: #F57F17;
            padding: 2px 4px;
            font-weight: bold;
            font-size: 6px;
        }
        
        .status-critico {
            background-color: #FFCDD2;
            color: #C62828;
            padding: 2px 4px;
            font-weight: bold;
            font-size: 6px;
        }
        
        /* Stock Colors */
        .stock-good {
            color: #2E7D32;
            font-weight: bold;
        }
        
        .stock-warning {
            color: #F57F17;
            font-weight: bold;
        }
        
        .stock-critical {
            color: #C62828;
            font-weight: bold;
        }
        
        /* Category Colors */
        .cat-ferreteria {
            background-color: #FFE0B2;
            color: #E65100;
            padding: 1px 3px;
            font-size: 6px;
        }
        
        .cat-aseo {
            background-color: #B3E5FC;
            color: #0277BD;
            padding: 1px 3px;
            font-size: 6px;
        }
        
        .cat-default {
            background-color: #E1BEE7;
            color: #7B1FA2;
            padding: 1px 3px;
            font-size: 6px;
        }
        
        /* Expiration Colors */
        .exp-ok {
            color: #2E7D32;
        }
        
        .exp-warning {
            color: #E65100;
            font-weight: bold;
        }
        
        .exp-expired {
            color: #C62828;
            font-weight: bold;
        }
        
        /* Footer */
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #e0e0e0;
            text-align: center;
            color: #666;
            font-size: 7px;
        }
    </style>
</head>
<body>
    @php
        // Convertir el logo a base64 para que DomPDF lo renderice correctamente
        $logoPath = public_path('assets/img/logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }
    @endphp

    <div class="container">
        <!-- Header con Logo e Información Institucional -->
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
        
        <!-- Título -->
        <div class="title-section">
            <h1>INVENTARIO DE INSUMOS</h1>
            <div class="subtitle">Sistema de Gestión INFRASTOCK</div>
        </div>
        
        <!-- Información de Generación -->
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td><strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i:s') }}</td>
                    <td><strong>Total de registros:</strong> {{ $supplies->count() }}</td>
                </tr>
            </table>
        </div>
        
        <!-- Resumen Estadístico -->
        @php
            $totalInicial = $supplies->sum('initial_amount');
            $totalConsumos = $supplies->sum('used_amount');
            $totalStock = $supplies->sum('stock');
            $disponibles = $supplies->where('status', 'disponible')->count();
            $criticos = $supplies->whereIn('status', ['agotado', 'critico', 'vencido'])->count();
            $bajoStock = $supplies->where('status', 'bajo_stock')->count();
        @endphp
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td>
                        <span class="summary-number">{{ number_format($totalInicial) }}</span>
                        <span class="summary-label">Cantidad Total Inicial</span>
                    </td>
                    <td>
                        <span class="summary-number">{{ number_format($totalConsumos) }}</span>
                        <span class="summary-label">Total Consumos</span>
                    </td>
                    <td>
                        <span class="summary-number">{{ number_format($totalStock) }}</span>
                        <span class="summary-label">Stock Disponible</span>
                    </td>
                    <td>
                        <span class="summary-number summary-good">{{ $disponibles }}</span>
                        <span class="summary-label">Disponibles</span>
                    </td>
                    <td>
                        <span class="summary-number" style="color: #F57F17;">{{ $bajoStock }}</span>
                        <span class="summary-label">Bajo Stock</span>
                    </td>
                    <td>
                        <span class="summary-number summary-critical">{{ $criticos }}</span>
                        <span class="summary-label">Críticos/Agotados</span>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Tabla de Datos -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20px;">#</th>
                    <th style="width: 90px;">NOMBRE</th>
                    <th style="width: 60px;">CATEGORÍA</th>
                    <th style="width: 100px;">CARACTERÍSTICAS</th>
                    <th style="width: 40px;">INICIAL</th>
                    <th style="width: 40px;">CONSUMO</th>
                    <th style="width: 40px;">STOCK</th>
                    <th style="width: 30px;">MÍN.</th>
                    <th style="width: 50px;">ESTADO</th>
                    <th style="width: 45px;">UNIDAD</th>
                    <th style="width: 50px;">VENCIMIENTO</th>
                    <th style="width: 90px;">OBSERVACIONES</th>
                </tr>
            </thead>
            <tbody>
                @foreach($supplies as $index => $supply)
                    @php
                        $categoryName = strtolower($supply->category->name ?? '');
                        $categoryClass = 'cat-default';
                        if (strpos($categoryName, 'ferret') !== false) {
                            $categoryClass = 'cat-ferreteria';
                        } elseif (strpos($categoryName, 'aseo') !== false) {
                            $categoryClass = 'cat-aseo';
                        }
                        
                        $statusClass = 'status-' . str_replace(' ', '_', strtolower($supply->status));
                        
                        $stockClass = 'stock-good';
                        $minimumStock = $supply->minimum_stock ?? 0;
                        if ($supply->stock <= 0) {
                            $stockClass = 'stock-critical';
                        } elseif ($supply->stock <= $minimumStock + 5) {
                            $stockClass = 'stock-warning';
                        }
                        
                        $expClass = 'exp-ok';
                        if ($supply->expiration_date) {
                            if ($supply->expiration_date->isPast()) {
                                $expClass = 'exp-expired';
                            } elseif (now()->diffInDays($supply->expiration_date, false) <= 30) {
                                $expClass = 'exp-warning';
                            }
                        }
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left" style="font-weight: 500;">{{ $supply->name }}</td>
                        <td><span class="{{ $categoryClass }}">{{ $supply->category->name ?? 'N/A' }}</span></td>
                        <td class="text-left">{{ Str::limit($supply->characteristics ?? 'N/A', 45) }}</td>
                        <td>{{ number_format($supply->initial_amount) }}</td>
                        <td>{{ number_format($supply->used_amount) }}</td>
                        <td class="{{ $stockClass }}">{{ number_format($supply->stock) }}</td>
                        <td>{{ $supply->minimum_stock ?? 0 }}</td>
                        <td><span class="{{ $statusClass }}">{{ $supply->status_text }}</span></td>
                        <td>{{ $supply->unit_measure ?? 'N/A' }}</td>
                        <td class="{{ $expClass }}">{{ $supply->expiration_date ? $supply->expiration_date->format('d/m/Y') : 'N/A' }}</td>
                        <td class="text-left">{{ Str::limit($supply->observations ?? 'N/A', 35) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Footer -->
        <div class="footer">
            <div>Documento generado automáticamente por el Sistema INFRASTOCK</div>
            <div>© {{ date('Y') }} SENA - Centro de Formación Agroindustrial "La Angostura" | Todos los derechos reservados</div>
        </div>
    </div>
</body>
</html>
