<!--
    * @file my-requests.blade.php
    * @brief Vista para mostrar todas las solicitudes de insumos del Personal de Aseo.
    *
    * Esta vista presenta una tabla completa con todas las solicitudes realizadas
    * por el usuario actual, incluyendo filtros por estado, búsqueda y paginación.
    * Permite al usuario ver el historial completo de sus solicitudes y su estado actual.
    * Utiliza Tailwind CSS para un diseño responsive y moderno.
    *
    * @param Collection $requests Solicitudes paginadas del usuario actual.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.cleaning-staff-master')

@section('title', 'Mis Solicitudes - Personal de Aseo INFRASTOCK')

@section('content')
<!-- Breadcrumb -->
@section('breadcrumb-items')
<li class="flex items-center">
    <a href="{{ route('infrastock.cleaning-staff.requests.create') }}" class="text-green-600 hover:text-green-800">Nueva Solicitud</a>
    <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="text-gray-700">Mis Solicitudes</li>
@endsection
        
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Mis Solicitudes de Insumos</h2>
            <p class="text-gray-600 mt-2">Aquí puedes ver el historial completo de todas tus solicitudes de insumos.</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar Insumo</label>
                    <input type="text" id="search" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Nombre del insumo...">
                </div>
                
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Estado</label>
                    <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="Solicitud">Pendientes</option>
                        <option value="approved">Aprobadas</option>
                        <option value="rejected">Rechazadas</option>
                        <option value="delivered">Entregadas</option>
                    </select>
                </div>
                
                <div>
                    <label for="date-filter" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Fecha</label>
                    <select id="date-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todas las fechas</option>
                        <option value="today">Hoy</option>
                        <option value="week">Esta semana</option>
                        <option value="month">Este mes</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de Solicitudes -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Lista de Solicitudes</h3>
                <p class="text-sm text-gray-500 mt-1">Total: {{ $requests->total() }} solicitudes</p>
            </div>
            
            @if($requests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="requests-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insumo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unidad/Almacén</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Solicitud</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($requests as $request)
                                <tr class="request-row" 
                                    data-equipment="{{ strtolower($request->equipment->name ?? '') }}"
                                    data-status="{{ $request->role }}"
                                    data-date="{{ $request->created_at->format('Y-m-d') }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <i class="fas fa-box text-blue-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $request->equipment->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $request->equipment->category->name ?? 'Sin categoría' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $request->amount }} {{ $request->equipment->unit ?? 'unidades' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->productiveUnitWarehouse->warehouse->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($request->role == 'Solicitud')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pendiente
                                            </span>
                                        @elseif($request->role == 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>
                                                Aprobada
                                            </span>
                                        @elseif($request->role == 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>
                                                Rechazada
                                            </span>
                                        @elseif($request->role == 'delivered')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-truck mr-1"></i>
                                                Entregada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $request->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="showRequestDetails({{ $request->id }})" class="text-blue-600 hover:text-blue-900 transition-colors duration-200">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay solicitudes</h3>
                    <p class="text-gray-500 mb-6">Aún no has realizado ninguna solicitud de insumos.</p>
                    <a href="{{ route('infrastock.cleaning-staff.requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Crear primera solicitud
                    </a>
                </div>
            @endif
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const statusFilter = document.getElementById('status-filter');
            const dateFilter = document.getElementById('date-filter');
            const tableRows = document.querySelectorAll('.request-row');

            // Función para filtrar la tabla
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;
                const dateValue = dateFilter.value;
                const today = new Date();
                
                tableRows.forEach(row => {
                    const equipmentName = row.dataset.equipment;
                    const status = row.dataset.status;
                    const requestDate = new Date(row.dataset.date);
                    
                    let showRow = true;
                    
                    // Filtro por búsqueda
                    if (searchTerm && !equipmentName.includes(searchTerm)) {
                        showRow = false;
                    }
                    
                    // Filtro por estado
                    if (statusValue && status !== statusValue) {
                        showRow = false;
                    }
                    
                    // Filtro por fecha
                    if (dateValue) {
                        switch(dateValue) {
                            case 'today':
                                if (requestDate.toDateString() !== today.toDateString()) {
                                    showRow = false;
                                }
                                break;
                            case 'week':
                                const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                                if (requestDate < weekAgo) {
                                    showRow = false;
                                }
                                break;
                            case 'month':
                                const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
                                if (requestDate < monthAgo) {
                                    showRow = false;
                                }
                                break;
                        }
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }

            // Event listeners para los filtros
            searchInput.addEventListener('input', filterTable);
            statusFilter.addEventListener('change', filterTable);
            dateFilter.addEventListener('change', filterTable);
        });

        // Función para mostrar detalles de la solicitud
        function showRequestDetails(requestId) {
            alert('Mostrando detalles de la solicitud #' + requestId);
        }
    </script>
@endsection

@section('script')
<script>
    // Script específico para la vista de mis solicitudes
    console.log('Vista de Mis Solicitudes cargada correctamente');
</script>
@endsection
