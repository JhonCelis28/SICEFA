<table>
    <thead>
        <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>CENTRO DE FORMACIÓN AGROINDUSTRIAL LA ANGOSTURA</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>Regional Huila</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>Área: Infraestructura</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>Responsable: {{ $adminName }}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>INVENTARIO DE HERRAMIENTAS - INFRASTOCK</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>Generado el {{ now()->format('d/m/Y H:i:s') }} | Total de registros: {{ $tools->count() }}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr>
            <td>TOTAL HERRAMIENTAS</td><td></td>
            <td>STOCK TOTAL</td><td></td>
            <td>DISPONIBLES</td><td></td>
            <td>EN PRÉSTAMO</td><td></td>
            <td>MANTENIMIENTO</td><td></td>
        </tr>
        <tr>
            <td>{{ $totalTools }}</td><td></td>
            <td>{{ number_format($totalStock) }}</td><td></td>
            <td>{{ $disponibles }}</td><td></td>
            <td>{{ $enPrestamo }}</td><td></td>
            <td>{{ $mantenimiento }}</td><td></td>
        </tr>
        <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Placa</th>
            <th>Descripción</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Categoría</th>
            <th>Estado</th>
            <th>Cant. Total</th>
            <th>Cant. Disponible</th>
        </tr>
    </thead>
    <tbody>
        @php
            $estadoLabels = [
                'disponible' => 'Disponible',
                'en_prestamo' => 'En Préstamo',
                'mantenimiento' => 'Mantenimiento',
                'no_disponible' => 'No Disponible'
            ];
        @endphp
        @foreach($tools as $index => $tool)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $tool->nombre ?? 'N/A' }}</td>
            <td>{{ $tool->placa ?? 'N/A' }}</td>
            <td>{{ $tool->descripcion ?? 'N/A' }}</td>
            <td>{{ $tool->marca ?? 'N/A' }}</td>
            <td>{{ $tool->modelo ?? 'N/A' }}</td>
            <td>{{ $tool->category->name ?? 'N/A' }}</td>
            <td>{{ $estadoLabels[$tool->estado ?? 'disponible'] ?? 'N/A' }}</td>
            <td>{{ $tool->cantidad_total ?? 0 }}</td>
            <td>{{ $tool->cantidad_disponible ?? 0 }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
