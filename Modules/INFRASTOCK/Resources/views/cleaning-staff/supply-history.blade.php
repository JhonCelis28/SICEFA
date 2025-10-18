<!--
    * @file supply-history.blade.php
    * @brief Vista para visualizar el historial de insumos del Personal de Aseo.
    *
    * Esta vista permite al personal de aseo consultar el historial completo de
    * todos los insumos disponibles en el sistema, incluyendo su disponibilidad
    * actual, movimientos históricos y estadísticas de uso. Incluye filtros de
    * búsqueda y análisis de disponibilidad en tiempo real.
    * Utiliza Tailwind CSS para un diseño responsive y moderno.
    *
    * @param Collection $supplies Lista de todos los insumos con su historial.
    * @param int $totalSupplies Total de insumos en el sistema.
    * @param int $availableSupplies Cantidad de insumos disponibles.
    * @param int $lowStockSupplies Cantidad de insumos con stock bajo.
    * @param int $outOfStockSupplies Cantidad de insumos sin stock.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.cleaning-staff-master')

@section('title', 'Historial de Insumos - Personal de Aseo INFRASTOCK')

@section('content')
<!-- Breadcrumb -->
@section('breadcrumb-items')
<li class="flex items-center">
    <a href="{{ route('infrastock.cleaning-staff.surplus-report') }}" class="text-green-600 hover:text-green-800">Reporte de Sobrantes</a>
    <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="text-gray-700">Historial de Insumos</li>
@endsection
        
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Historial de Insumos</h2>
            <p class="text-gray-600 mt-2">Consulta la disponibilidad actual y el historial de todos los insumos del sistema.</p>
        </div>

        <!-- Estadísticas de Disponibilidad -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-boxes text-blue-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Total Insumos</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalSupplies }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Disponibles</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $availableSupplies }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Stock Bajo</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $lowStockSupplies }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500">Sin Stock</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $outOfStockSupplies }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Filtros de Búsqueda</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search-supply" class="block text-sm font-medium text-gray-700 mb-2">Buscar Insumo</label>
                    <input type="text" id="search-supply" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Nombre del insumo...">
                </div>
                
                <div>
                    <label for="category-filter" class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                    <select id="category-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todas las categorías</option>
                        @foreach($supplies->pluck('category.name')->unique()->filter() as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="stock-filter" class="block text-sm font-medium text-gray-700 mb-2">Estado de Stock</label>
                    <select id="stock-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="available">Disponible</option>
                        <option value="low">Stock bajo</option>
                        <option value="out">Sin stock</option>
                    </select>
                </div>
                
                <div>
                    <label for="sort-by" class="block text-sm font-medium text-gray-700 mb-2">Ordenar por</label>
                    <select id="sort-by" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="name">Nombre</option>
                        <option value="stock">Stock disponible</option>
                        <option value="category">Categoría</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de Insumos -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Inventario de Insumos</h3>
                <p class="text-sm text-gray-500 mt-1">Total: {{ $supplies->count() }} insumos</p>
            </div>
            
            @if($supplies->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="supplies-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insumo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Disponible</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mis Solicitudes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($supplies as $supply)
                                <tr class="supply-row" 
                                    data-equipment="{{ strtolower($supply->name) }}"
                                    data-category="{{ strtolower($supply->category->name ?? '') }}"
                                    data-stock="{{ $supply->stock }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <i class="fas fa-box text-blue-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $supply->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $supply->description ?? 'Sin descripción' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $supply->category->name ?? 'Sin categoría' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $supply->stock }} {{ $supply->unit ?? 'unidades' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($supply->stock > 5)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>
                                                Disponible
                                            </span>
                                        @elseif($supply->stock > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Stock Bajo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>
                                                Sin Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $supply->warehouseMovements->count() }} solicitudes
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        @if($supply->stock > 0)
                                            <a href="{{ route('infrastock.cleaning-staff.requests.create') }}" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-plus-circle mr-1"></i>
                                                Solicitar
                                            </a>
                                        @else
                                            <span class="text-gray-400">
                                                <i class="fas fa-ban mr-1"></i>
                                                No disponible
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay insumos registrados</h3>
                    <p class="text-gray-500">No hay insumos disponibles en el sistema en este momento.</p>
                </div>
            @endif
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search-supply');
            const categoryFilter = document.getElementById('category-filter');
            const stockFilter = document.getElementById('stock-filter');
            const sortBySelect = document.getElementById('sort-by');
            const tableRows = document.querySelectorAll('.supply-row');

            // Función para filtrar la tabla
            function filterTable() {
                const searchTerm = searchInput.value.toLowerCase();
                const categoryValue = categoryFilter.value.toLowerCase();
                const stockValue = stockFilter.value;
                
                tableRows.forEach(row => {
                    const equipmentName = row.dataset.equipment;
                    const category = row.dataset.category;
                    const stock = parseInt(row.dataset.stock);
                    
                    let showRow = true;
                    
                    // Filtro por búsqueda
                    if (searchTerm && !equipmentName.includes(searchTerm)) {
                        showRow = false;
                    }
                    
                    // Filtro por categoría
                    if (categoryValue && !category.includes(categoryValue)) {
                        showRow = false;
                    }
                    
                    // Filtro por estado de stock
                    if (stockValue) {
                        switch(stockValue) {
                            case 'available':
                                if (stock <= 5) showRow = false;
                                break;
                            case 'low':
                                if (stock <= 0 || stock > 5) showRow = false;
                                break;
                            case 'out':
                                if (stock > 0) showRow = false;
                                break;
                        }
                    }
                    
                    row.style.display = showRow ? '' : 'none';
                });
            }

            // Event listeners para los filtros
            searchInput.addEventListener('input', filterTable);
            categoryFilter.addEventListener('change', filterTable);
            stockFilter.addEventListener('change', filterTable);
            sortBySelect.addEventListener('change', filterTable);
        });
    </script>
@endsection

@section('script')
<script>
    // Script específico para la vista de historial de insumos
    console.log('Vista de Historial de Insumos cargada correctamente');
</script>
@endsection
