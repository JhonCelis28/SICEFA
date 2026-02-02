@extends('infrastock::layouts.usuarios-master')

@section('title', 'Stock Disponible - Psicola INFRASTOCK')

@section('content')

<!-- Breadcrumb -->
@section('breadcrumb-items')
<li class="text-gray-700">Stock Disponible</li>
@endsection

<!-- Header -->
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Stock Disponible en Tiempo Real</h2>
            <p class="text-gray-600 mt-2">Consulta la disponibilidad actualizada de insumos y herramientas para planificar tu trabajo.</p>
        </div>
    </div>
</div>

<!-- Estadísticas Generales -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Total de Insumos</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalEquipment }}</p>
            </div>
            <div class="bg-blue-100 p-4 rounded-full">
                <i class="fas fa-boxes text-blue-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Stock Total</p>
                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($totalStock, 0, ',', '.') }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <i class="fas fa-warehouse text-green-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Stock Bajo</p>
                <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $lowStockCount }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-full">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Agotados</p>
                <p class="text-3xl font-bold text-red-600 mt-2">{{ $outOfStockCount }}</p>
            </div>
            <div class="bg-red-100 p-4 rounded-full">
                <i class="fas fa-times-circle text-red-600 text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filtros y Búsqueda -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <form method="GET" action="{{ route('infrastock.psicola.stock') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-filter mr-1"></i> Filtrar por Categoría
            </label>
            <select name="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="">Todas las categorías</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-search mr-1"></i> Buscar Insumo
            </label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   placeholder="Nombre o código..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500">
        </div>

        <div class="flex items-end">
            <label class="flex items-center space-x-2 cursor-pointer">
                <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}
                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                <span class="text-sm text-gray-700">Solo Stock Bajo</span>
            </label>
        </div>

        <div class="flex items-end space-x-2">
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                <i class="fas fa-filter mr-2"></i> Filtrar
            </button>
            <a href="{{ route('infrastock.psicola.stock') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                <i class="fas fa-redo mr-2"></i> Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Listado de Insumos -->
<div class="bg-white rounded-xl shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-gray-800">Listado de Insumos</h3>
        <span class="text-sm text-gray-500">{{ $equipments->count() }} insumos encontrados</span>
    </div>

    @if($equipments->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insumo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Disponible</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última Actualización</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($equipments as $equipment)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-box text-green-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $equipment->name }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $equipment->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $equipment->category->name ?? 'Sin categoría' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $equipment->stock }} {{ $equipment->unit ?? 'unidades' }}
                                </div>
                                @if($equipment->initial_amount)
                                    <div class="text-xs text-gray-500">Inicial: {{ $equipment->initial_amount }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($equipment->stock == 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>
                                        Agotado
                                    </span>
                                @elseif($equipment->stock <= 10)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Stock Bajo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Disponible
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $equipment->updated_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="showEquipmentDetails({{ $equipment->id }})" 
                                        class="text-green-600 hover:text-green-900">
                                    <i class="fas fa-eye mr-1"></i> Ver Detalles
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-box-open text-gray-400 text-5xl mb-4"></i>
            <h4 class="text-lg font-medium text-gray-900 mb-2">No se encontraron insumos</h4>
            <p class="text-gray-500 mb-4">Intenta ajustar los filtros de búsqueda.</p>
            <a href="{{ route('infrastock.psicola.stock') }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                <i class="fas fa-redo mr-2"></i>
                Limpiar Filtros
            </a>
        </div>
    @endif
</div>

<!-- Modal de Detalles del Insumo -->
<div id="equipmentDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-6 border-b border-gray-200">
                <h3 class="text-xl font-bold text-gray-900">Detalles del Insumo</h3>
                <button onclick="closeEquipmentDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="equipmentDetailsContent" class="p-6">
                <!-- El contenido se cargará aquí -->
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    function showEquipmentDetails(equipmentId) {
        fetch(`{{ route('infrastock.psicola.equipment.show', '') }}/${equipmentId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error
                    });
                    return;
                }

                const modal = document.getElementById('equipmentDetailsModal');
                const content = document.getElementById('equipmentDetailsContent');
                
                let statusBadge = '';
                if (data.out_of_stock) {
                    statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i> Agotado</span>';
                } else if (data.low_stock) {
                    statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-exclamation-triangle mr-1"></i> Stock Bajo</span>';
                } else {
                    statusBadge = '<span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i> Disponible</span>';
                }

                content.innerHTML = `
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-2xl font-bold text-gray-900">${data.name}</h4>
                            ${statusBadge}
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500">Categoría</p>
                                <p class="text-lg font-semibold text-gray-900">${data.category}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500">Stock Disponible</p>
                                <p class="text-lg font-semibold text-gray-900">${data.stock} ${data.unit}</p>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500">Cantidad Inicial</p>
                                <p class="text-lg font-semibold text-gray-900">${data.initial_amount} ${data.unit}</p>
                            </div>
                            
                            ${data.price ? `
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-sm text-gray-500">Precio Unitario</p>
                                <p class="text-lg font-semibold text-gray-900">$${parseFloat(data.price).toLocaleString()}</p>
                            </div>
                            ` : ''}
                        </div>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-500 mb-2">Última Actualización</p>
                            <p class="text-sm text-gray-900">${data.updated_at}</p>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-4 border-t">
                            <a href="{{ route('infrastock.psicola.requests.create') }}?equipment=${equipmentId}" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                                <i class="fas fa-cart-plus mr-2"></i>
                                Solicitar este Insumo
                            </a>
                            <button onclick="closeEquipmentDetailsModal()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                                Cerrar
                            </button>
                        </div>
                    </div>
                `;
                
                modal.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo cargar la información del insumo.'
                });
            });
    }

    function closeEquipmentDetailsModal() {
        document.getElementById('equipmentDetailsModal').classList.add('hidden');
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('equipmentDetailsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEquipmentDetailsModal();
        }
    });

    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeEquipmentDetailsModal();
        }
    });
</script>
@endsection

