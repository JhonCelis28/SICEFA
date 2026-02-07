<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Inventario de Herramientas - INFRASTOCK</title>
    <style>
        @page { margin-top: 2cm; margin-bottom: 2cm; margin-left: 1cm; margin-right: 1cm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 9px; line-height: 1.4; color: #333; background: #fff; }
        .container { padding: 0; }
        .header-container { width: 100%; margin-bottom: 15px; border-bottom: 3px solid #E65100; padding-bottom: 10px; }
        .header-table { width: 100%; border-collapse: collapse; }
        .header-left { width: 80%; vertical-align: middle; padding-right: 10px; }
        .header-right { width: 20%; text-align: right; vertical-align: middle; }
        .header-right img { height: 70px; width: auto; }
        .institution-name { font-size: 14px; font-weight: bold; color: #BF360C; margin-bottom: 3px; }
        .institution-region { font-size: 11px; color: #333; margin-bottom: 2px; }
        .institution-area { font-size: 10px; color: #555; margin-bottom: 2px; }
        .institution-responsible { font-size: 10px; color: #555; }
        .title-section { background-color: #E65100; color: white; padding: 12px 15px; text-align: center; margin-bottom: 12px; }
        .title-section h1 { font-size: 16px; margin-bottom: 3px; letter-spacing: 1px; }
        .title-section .subtitle { font-size: 10px; }
        .info-section { width: 100%; margin-bottom: 12px; background-color: #f8f9fa; padding: 8px 12px; border-left: 4px solid #E65100; }
        .info-table { width: 100%; }
        .info-table td { font-size: 9px; color: #555; padding: 2px 15px 2px 0; }
        .info-table strong { color: #333; }
        .summary-section { width: 100%; margin-bottom: 12px; background-color: #FFF3E0; padding: 10px; border: 1px solid #FFE0B2; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { text-align: center; padding: 5px 8px; border-right: 1px solid #FFCC80; }
        .summary-table td:last-child { border-right: none; }
        .summary-number { font-size: 14px; font-weight: bold; color: #E65100; display: block; }
        .summary-label { font-size: 7px; color: #555; text-transform: uppercase; display: block; margin-top: 2px; }
        .summary-good { color: #2E7D32 !important; }
        .summary-warning { color: #F57F17 !important; }
        .summary-critical { color: #C62828 !important; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 7px; margin-top: 10px; }
        .data-table thead { background-color: #E65100; }
        .data-table th { padding: 8px 4px; text-align: center; font-weight: bold; font-size: 8px; text-transform: uppercase; letter-spacing: 0.3px; border: 1px solid #BF360C; color: #FFFFFF; background-color: #E65100; }
        .data-table tbody tr:nth-child(even) { background-color: #f5f5f5; }
        .data-table td { padding: 5px 3px; text-align: center; vertical-align: middle; border: 1px solid #ddd; font-size: 7px; }
        .data-table td.text-left { text-align: left; }
        .status-disponible { background-color: #C8E6C9; color: #2E7D32; padding: 2px 4px; font-weight: bold; font-size: 6px; }
        .status-en_prestamo { background-color: #FFF9C4; color: #F57F17; padding: 2px 4px; font-weight: bold; font-size: 6px; }
        .status-mantenimiento { background-color: #FFE0B2; color: #E65100; padding: 2px 4px; font-weight: bold; font-size: 6px; }
        .status-no_disponible { background-color: #FFCDD2; color: #C62828; padding: 2px 4px; font-weight: bold; font-size: 6px; }
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
        $estadoLabels = [
            'disponible' => 'Disponible',
            'en_prestamo' => 'En Préstamo',
            'mantenimiento' => 'Mantenimiento',
            'no_disponible' => 'No Disponible'
        ];
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
            <h1>INVENTARIO DE HERRAMIENTAS</h1>
            <div class="subtitle">Sistema de Gestión INFRASTOCK</div>
        </div>

        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td><strong>Fecha de generación:</strong> {{ now()->format('d/m/Y H:i:s') }}</td>
                    <td><strong>Total de registros:</strong> {{ $tools->count() }}</td>
                </tr>
            </table>
        </div>

        @php
            $totalStock = $tools->sum('cantidad_total');
            $totalDisponible = $tools->sum('cantidad_disponible');
            $disponibles = $tools->where('estado', 'disponible')->count();
            $enPrestamo = $tools->where('estado', 'en_prestamo')->count();
            $mantenimiento = $tools->where('estado', 'mantenimiento')->count();
        @endphp
        <div class="summary-section">
            <table class="summary-table">
                <tr>
                    <td>
                        <span class="summary-number">{{ $tools->count() }}</span>
                        <span class="summary-label">Total Herramientas</span>
                    </td>
                    <td>
                        <span class="summary-number">{{ number_format($totalStock) }}</span>
                        <span class="summary-label">Stock Total</span>
                    </td>
                    <td>
                        <span class="summary-number summary-good">{{ number_format($totalDisponible) }}</span>
                        <span class="summary-label">Disponible</span>
                    </td>
                    <td>
                        <span class="summary-number summary-good">{{ $disponibles }}</span>
                        <span class="summary-label">Disponibles</span>
                    </td>
                    <td>
                        <span class="summary-number summary-warning">{{ $enPrestamo }}</span>
                        <span class="summary-label">En Préstamo</span>
                    </td>
                    <td>
                        <span class="summary-number summary-critical">{{ $mantenimiento }}</span>
                        <span class="summary-label">Mantenimiento</span>
                    </td>
                </tr>
            </table>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 20px;">#</th>
                    <th style="width: 90px;">NOMBRE</th>
                    <th style="width: 50px;">PLACA</th>
                    <th style="width: 80px;">DESCRIPCIÓN</th>
                    <th style="width: 50px;">MARCA</th>
                    <th style="width: 50px;">MODELO</th>
                    <th style="width: 55px;">CATEGORÍA</th>
                    <th style="width: 50px;">ESTADO</th>
                    <th style="width: 35px;">TOTAL</th>
                    <th style="width: 35px;">DISP.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tools as $index => $tool)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left" style="font-weight: 500;">{{ $tool->nombre ?? 'N/A' }}</td>
                        <td>{{ $tool->placa ?? 'N/A' }}</td>
                        <td class="text-left">{{ Str::limit($tool->descripcion ?? 'N/A', 40) }}</td>
                        <td>{{ $tool->marca ?? 'N/A' }}</td>
                        <td>{{ $tool->modelo ?? 'N/A' }}</td>
                        <td>{{ $tool->category->name ?? 'N/A' }}</td>
                        <td><span class="status-{{ $tool->estado ?? 'disponible' }}">{{ $estadoLabels[$tool->estado ?? 'disponible'] ?? 'N/A' }}</span></td>
                        <td>{{ $tool->cantidad_total ?? 0 }}</td>
                        <td>{{ $tool->cantidad_disponible ?? 0 }}</td>
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
