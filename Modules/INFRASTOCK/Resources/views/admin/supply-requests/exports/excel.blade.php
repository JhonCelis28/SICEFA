<table>
    {{-- Fila 1: Espacio para logo --}}
    <tr>
        <td></td>
    </tr>

    {{-- Fila 2: Título institucional --}}
    <tr>
        <td>📊 Centro de Formación Agroindustrial "La Angostura"</td>
    </tr>

    {{-- Fila 3: Regional --}}
    <tr>
        <td>Regional: Huila - Campoalegre</td>
    </tr>

    {{-- Fila 4: Área --}}
    <tr>
        <td>Área: Infraestructura - Sistema INFRASTOCK</td>
    </tr>

    {{-- Fila 5: Responsable --}}
    <tr>
        <td>Responsable: {{ $adminName }}</td>
    </tr>

    {{-- Fila 6: Vacía --}}
    <tr>
        <td></td>
    </tr>

    {{-- Fila 7: Título del reporte --}}
    <tr>
        <td>📦 REPORTE DE CONSUMOS DE INSUMOS</td>
    </tr>

    {{-- Fila 8: Período --}}
    <tr>
        <td>Período: {{ $periodLabel }} | Generado: {{ now()->format('d/m/Y H:i') }}</td>
    </tr>

    {{-- Fila 9: Vacía --}}
    <tr>
        <td></td>
    </tr>

    {{-- Fila 10: Encabezados estadísticas --}}
    <tr>
        <td>Total Solicitudes</td>
        <td></td>
        <td></td>
        <td>Total Unidades</td>
        <td></td>
        <td></td>
        <td>Insumos Diferentes</td>
    </tr>

    {{-- Fila 11: Valores estadísticas --}}
    <tr>
        <td>{{ $stats['total_requests'] }}</td>
        <td></td>
        <td></td>
        <td>{{ $stats['total_units'] }}</td>
        <td></td>
        <td></td>
        <td>{{ $stats['unique_supplies'] }}</td>
    </tr>

    {{-- Fila 12: Vacía --}}
    <tr>
        <td></td>
    </tr>

    {{-- Fila 13: Título Top Consumidos --}}
    <tr>
        <td>🔥 TOP 5 - INSUMOS MÁS CONSUMIDOS</td>
    </tr>

    {{-- Fila 14: Encabezados Top --}}
    <tr>
        <td>Posición</td>
        <td></td>
        <td>Insumo</td>
        <td></td>
        <td>Cantidad Total</td>
        <td></td>
        <td>% del Total</td>
    </tr>

    {{-- Filas de Top Consumidos --}}
    @foreach($topConsumed as $index => $top)
    <tr>
        <td>#{{ $index + 1 }}</td>
        <td></td>
        <td>{{ $top->equipment->name ?? 'N/A' }}</td>
        <td></td>
        <td>{{ number_format($top->total_consumed) }} {{ $top->equipment->unit ?? 'und' }}</td>
        <td></td>
        <td>{{ $stats['total_units'] > 0 ? number_format(($top->total_consumed / $stats['total_units']) * 100, 1) : 0 }}%</td>
    </tr>
    @endforeach

    {{-- Fila vacía --}}
    <tr>
        <td></td>
    </tr>

    {{-- Encabezados de la tabla de detalle --}}
    <tr>
        <th>#</th>
        <th>Insumo</th>
        <th>Categoría</th>
        <th>Cantidad</th>
        <th>Unidad</th>
        <th>Solicitante</th>
        <th>Rol</th>
        <th>Fecha</th>
        <th>Destino</th>
    </tr>

    {{-- Datos de consumos --}}
    @foreach($consumptions as $index => $consumption)
    @php
        $roleName = 'Usuario';
        if ($consumption->user) {
            $userRoles = $consumption->user->roles->pluck('name')->toArray();
            if (in_array('Operario', $userRoles)) {
                $roleName = 'Operario';
            } elseif (in_array('Aseo', $userRoles)) {
                $roleName = 'Aseo';
            } elseif (in_array('Centro de Convivencia', $userRoles)) {
                $roleName = 'Convivencia';
            } elseif (in_array('Ganadería', $userRoles)) {
                $roleName = 'Ganadería';
            } elseif (!empty($userRoles)) {
                $roleName = $userRoles[0];
            }
        }
    @endphp
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $consumption->equipment->name ?? 'N/A' }}</td>
        <td>{{ $consumption->equipment->category->name ?? 'Sin categoría' }}</td>
        <td>{{ $consumption->amount }}</td>
        <td>{{ $consumption->equipment->unit ?? 'und' }}</td>
        <td>{{ $consumption->user->name ?? 'N/A' }}</td>
        <td>{{ $roleName }}</td>
        <td>{{ $consumption->created_at->format('d/m/Y') }}</td>
        <td>{{ $consumption->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }}</td>
    </tr>
    @endforeach
</table>
