<table>
    <thead>
        <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>CENTRO DE FORMACIÓN AGROINDUSTRIAL LA ANGOSTURA</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>Regional Huila</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>Área: Infraestructura</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>Responsable: {{ $adminName }}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>PRÉSTAMOS DE HERRAMIENTAS - {{ $periodLabel }}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td>Generado el {{ now()->format('d/m/Y H:i:s') }} | Total de registros: {{ $loans->count() }}</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr>
            <td>TOTAL MOVIMIENTOS</td><td></td>
            <td>PRÉSTAMOS</td><td></td>
            <td>DEVOLUCIONES</td><td></td>
            <td>PENDIENTES</td><td></td>
        </tr>
        <tr>
            <td>{{ $stats['total'] }}</td><td></td>
            <td>{{ $stats['prestamos'] }}</td><td></td>
            <td>{{ $stats['devoluciones'] }}</td><td></td>
            <td>{{ $stats['pendientes'] }}</td><td></td>
        </tr>
        <tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>
        <tr>
            <th>#</th>
            <th>Herramienta</th>
            <th>Usuario / Prestatario</th>
            <th>Cantidad</th>
            <th>Área / Bodega</th>
            <th>Movimiento</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Descripción</th>
        </tr>
    </thead>
    <tbody>
        @php
            $statusLabels = ['approved' => 'Aprobado', 'pending' => 'Pendiente', 'rejected' => 'Rechazado'];
        @endphp
        @foreach($loans as $index => $loan)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $loan->tool->nombre ?? 'N/A' }} {{ $loan->tool->placa ? '['.$loan->tool->placa.']' : '' }}</td>
            <td>{{ $loan->user->person->first_name ?? 'N/A' }} {{ $loan->user->person->first_last_name ?? '' }}</td>
            <td>{{ $loan->amount ?? 'N/A' }}</td>
            <td>
                {{ $loan->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }}
                @if($loan->productiveUnitWarehouse && $loan->productiveUnitWarehouse->warehouse)
                    ({{ $loan->productiveUnitWarehouse->warehouse->name }})
                @endif
            </td>
            <td>{{ $loan->role }}</td>
            <td>{{ $statusLabels[$loan->status] ?? 'N/A' }}</td>
            <td>{{ $loan->created_at->format('d/m/Y') }}</td>
            <td>{{ $loan->description ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
