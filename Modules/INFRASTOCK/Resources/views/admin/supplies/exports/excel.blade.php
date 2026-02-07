<table>
    <thead>
        <!-- Fila 1: Espacio para logo -->
        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <!-- Fila 2: Información Institucional -->
        <tr>
            <td>CENTRO DE FORMACIÓN AGROINDUSTRIAL LA ANGOSTURA</td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <!-- Fila 3: Regional -->
        <tr>
            <td>Regional Huila</td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <!-- Fila 4: Área -->
        <tr>
            <td>Área: Infraestructura</td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <!-- Fila 5: Responsable -->
        <tr>
            <td>Responsable: {{ $adminName }}</td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <!-- Fila 6: Vacía -->
        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <!-- Fila 7: Título del Documento -->
        <tr>
            <td>INVENTARIO DE INSUMOS - INFRASTOCK</td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <!-- Fila 8: Fecha de generación -->
        <tr>
            <td>Generado el {{ now()->format('d/m/Y H:i:s') }} | Total de registros: {{ $supplies->count() }}</td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <!-- Fila 9: Vacía -->
        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <!-- Fila 10: Resumen Estadístico - Títulos -->
        <tr>
            <td>CANTIDAD TOTAL INICIAL</td>
            <td></td>
            <td>TOTAL CONSUMOS</td>
            <td></td>
            <td>STOCK DISPONIBLE</td>
            <td></td>
            <td>DISPONIBLES</td>
            <td></td>
            <td>BAJO STOCK</td>
            <td></td>
            <td>CRÍTICOS/AGOTADOS</td>
            <td></td>
        </tr>
        <!-- Fila 11: Resumen Estadístico - Valores -->
        <tr>
            <td>{{ number_format($totalInicial) }}</td>
            <td></td>
            <td>{{ number_format($totalConsumos) }}</td>
            <td></td>
            <td>{{ number_format($totalStock) }}</td>
            <td></td>
            <td>{{ $disponibles }}</td>
            <td></td>
            <td>{{ $bajoStock }}</td>
            <td></td>
            <td>{{ $criticos }}</td>
            <td></td>
        </tr>
        <!-- Fila 12: Vacía -->
        <tr>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
        </tr>
        <!-- Fila 13: Encabezados de Tabla -->
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Características</th>
            <th>Cant. Inicial</th>
            <th>Consumos</th>
            <th>Stock Actual</th>
            <th>Mínimo</th>
            <th>Estado</th>
            <th>Unidad Medida</th>
            <th>Vencimiento</th>
            <th>Observaciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($supplies as $index => $supply)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $supply->name }}</td>
            <td>{{ $supply->category->name ?? 'N/A' }}</td>
            <td>{{ $supply->characteristics ?? 'N/A' }}</td>
            <td>{{ $supply->initial_amount }}</td>
            <td>{{ $supply->used_amount }}</td>
            <td>{{ $supply->stock }}</td>
            <td>{{ $supply->minimum_stock ?? 0 }}</td>
            <td>{{ $supply->status_text }}</td>
            <td>{{ $supply->unit_measure ?? 'N/A' }}</td>
            <td>{{ $supply->expiration_date ? $supply->expiration_date->format('d/m/Y') : 'N/A' }}</td>
            <td>{{ $supply->observations ?? 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
