<!--
    * @file surplus-report.blade.php
    * @brief Vista para generar reportes de sobrantes del Personal de Aseo.
    *
    * Esta vista permite al personal de aseo visualizar los insumos entregados
    * que podrían tener sobrantes, con filtros de búsqueda y análisis de uso.
    * NO incluye funcionalidad de exportación (PDF/Excel) según los requerimientos.
    * Utiliza Tailwind CSS para un diseño responsive y moderno.
    *
    * @param Collection $deliveredSupplies Insumos entregados al usuario.
    * @param int $totalDelivered Total de insumos entregados.
    * @param int $uniqueSupplies Cantidad de tipos únicos de insumos entregados.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.usuarios-master')

@section('title', 'Reporte de Sobrantes - Personal de Aseo INFRASTOCK')

@section('content')
<!-- Breadcrumb -->
@section('breadcrumb-items')
<li class="text-gray-700">Reporte de Sobrantes</li>
@endsection
        
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Reporte de Sobrantes</h2>
            <p class="text-gray-600 mt-2">Analiza los insumos entregados para identificar posibles sobrantes y optimizar el uso de recursos.</p>
        </div>

        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-boxes text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total Entregado</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalDelivered }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-list text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Tipos de Insumos</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $uniqueSupplies }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-chart-line text-orange-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Promedio por Tipo</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $uniqueSupplies > 0 ? round($totalDelivered / $uniqueSupplies, 1) : 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros de Búsqueda</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search-supply" class="block text-sm font-medium text-gray-700 mb-2">Buscar Insumo</label>
                    <input type="text" id="search-supply" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Nombre del insumo...">
                </div>
                
                <div>
                    <label for="date-range" class="block text-sm font-medium text-gray-700 mb-2">Rango de Fechas</label>
                    <select id="date-range" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todas las fechas</option>
                        <option value="week">Última semana</option>
                        <option value="month">Último mes</option>
                        <option value="quarter">Último trimestre</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort-by" class="block text-sm font-medium text-gray-700 mb-2">Ordenar por</label>
                    <select id="sort-by" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="date">Fecha de entrega</option>
                        <option value="name">Nombre del insumo</option>
                        <option value="amount">Cantidad</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de Insumos Entregados -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Insumos Entregados</h3>
                <p class="text-sm text-gray-500 mt-1">Total: {{ $deliveredSupplies->count() }} entregas</p>
            </div>
            
            @if($deliveredSupplies->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="supplies-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insumo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad Entregada</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha de Entrega</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado de Uso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($deliveredSupplies as $supply)
                                <tr class="supply-row" 
                                    data-equipment="{{ strtolower($supply->equipment->name ?? '') }}"
                                    data-date="{{ $supply->created_at->format('Y-m-d') }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                    <i class="fas fa-box text-green-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $supply->equipment->name ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $supply->equipment->category->name ?? 'Sin categoría' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $supply->amount }} {{ $supply->equipment->unit ?? 'unidades' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $supply->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Entregado
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button onclick="addObservation({{ $supply->id }})" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-plus-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-chart-bar text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay entregas registradas</h3>
                    <p class="text-gray-500 mb-6">Aún no tienes insumos entregados para analizar.</p>
                    <a href="{{ route('infrastock.cleaning-staff.requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Crear nueva solicitud
                    </a>
                </div>
            @endif
        </div>

        <!-- Nota sobre Exportación -->
        <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">Información sobre Exportación</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>Esta vista está diseñada para análisis y filtrado únicamente. La funcionalidad de exportación a PDF o Excel no está disponible para el rol de Personal de Aseo.</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-supply');
            const dateRangeSelect = document.getElementById('date-range');
            const sortBySelect = document.getElementById('sort-by');
            const tableRows = document.querySelectorAll('.supply-row');

            // Función para filtrar la tabla
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const dateRange = dateRangeSelect.value;
                const today = new Date();
                
                tableRows.forEach(row => {
                    const equipmentName = row.dataset.equipment;
                    const supplyDate = new Date(row.dataset.date);
                    
                    let showRow = true;
                    
                    // Filtro por búsqueda
                    if (searchTerm && !equipmentName.includes(searchTerm)) {
                        showRow = false;
                    }
                    
                    // Filtro por rango de fechas
                    if (dateRange) {
                        switch(dateRange) {
                            case 'week':
                                const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                                if (supplyDate < weekAgo) {
                                    showRow = false;
                                }
                                break;
                            case 'month':
                                const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
                                if (supplyDate < monthAgo) {
                                    showRow = false;
                                }
                                break;
                            case 'quarter':
                                const quarterAgo = new Date(today.getFullYear(), today.getMonth() - 3, today.getDate());
                                if (supplyDate < quarterAgo) {
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
            dateRangeSelect.addEventListener('change', filterTable);
            sortBySelect.addEventListener('change', filterTable);
        });

        // Función para agregar observaciones
        function addObservation(supplyId) {
            alert('Funcionalidad de observaciones para el insumo #' + supplyId + ' - Por implementar');
        }
    </script>
@endsection

@section('script')
<script>
    // Script específico para la vista de reporte de sobrantes
    console.log('Vista de Reporte de Sobrantes cargada correctamente');
</script>
@endsection
