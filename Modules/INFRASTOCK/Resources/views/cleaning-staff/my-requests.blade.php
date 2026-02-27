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
@extends('infrastock::layouts.usuarios-master')

@section('title', 'Mis Solicitudes - Personal de Aseo INFRASTOCK')

@section('content')

<!-- Modal para Ver Detalles de Solicitud -->
<div id="showDetailsModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl my-8 max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header del Modal -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-green-50">
            <div>
                <h3 class="text-2xl font-bold text-gray-900" id="details-modal-title">Detalles de la Solicitud</h3>
                <p class="text-sm text-gray-600 mt-1">Información completa de la solicitud y sus insumos.</p>
            </div>
            <button onclick="closeDetailsModal()" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <!-- Contenido del Modal -->
        <div class="flex-1 overflow-y-auto p-6" id="details-modal-content">
            <div class="flex justify-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-green-500"></i>
            </div>
        </div>

        <!-- Footer del Modal -->
        <div class="flex justify-end p-6 border-t border-gray-200 bg-gray-50">
            <button onclick="closeDetailsModal()" 
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
    // Función global para abrir el modal - debe estar disponible inmediatamente
    window.openRequestModal = function openRequestModal() {
        const requestModal = document.getElementById('request-modal');
        if (requestModal) {
            requestModal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        } else {
            console.error('No se encontró el modal request-modal');
            // Fallback: redirigir a la página de creación
            window.location.href = '{{ route("infrastock.cleaning-staff.requests.index", ["open_modal" => 1]) }}';
        }
    };
</script>
<!-- Breadcrumb -->
@section('breadcrumb-items')
<li class="flex items-center">
    <a href="javascript:void(0)" onclick="openRequestModal()" class="text-green-600 hover:text-green-800">Nueva Solicitud</a>
    <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
</li>
<li class="text-gray-700">Mis Solicitudes</li>
@endsection
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Mis Solicitudes de Insumos</h2>
                    <p class="text-gray-600 mt-2">Aquí puedes ver el historial completo de todas tus solicitudes de insumos.</p>
                </div>
                <div>
                    <button type="button" id="open-request-modal" onclick="openRequestModal()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-md transition-colors duration-200 font-medium">
                        <i class="fas fa-plus mr-2"></i>
                        Solicitar Insumo
                    </button>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Filtros de Búsqueda</h3>
                <button type="button" id="clear-filters" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                    <i class="fas fa-times mr-1"></i>
                    Limpiar filtros
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar Insumo</label>
                    <div class="relative">
                        <input type="text" id="search" class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Buscar insumos disponibles...">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <button type="button" id="clear-request-search" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Escribe para buscar insumos disponibles en el sistema</p>
                </div>
                
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Estado</label>
                    <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="pending">Pendientes</option>
                        <option value="approved">Aprobadas</option>
                        <option value="rejected">Rechazadas</option>
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
            
            <!-- Indicador de filtros activos -->
            <div id="active-filters" class="mt-4 hidden">
                <div class="flex flex-wrap gap-2">
                    <span class="text-sm text-gray-600">Filtros activos:</span>
                    <div id="filter-tags" class="flex flex-wrap gap-2">
                        <!-- Los tags de filtros activos se mostrarán aquí -->
                    </div>
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
                <div class="space-y-4">
                    @foreach($requests as $request)
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                            <!-- Header de la Solicitud -->
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                                                <i class="fas fa-clipboard-list text-blue-600 text-lg"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">
                                                Solicitud #{{ $request->id }}
                                            </h3>
                                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar mr-1"></i>
                                                    {{ $request->created_at->format('d/m/Y H:i') }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-boxes mr-1"></i>
                                                    {{ $request->total_items }} insumo{{ $request->total_items > 1 ? 's' : '' }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                                    {{ $request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <!-- Estado de la Solicitud -->
                                        @if($request->status == 'pending')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pendiente
                                            </span>
                                        @elseif($request->status == 'approved')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>
                                                Aprobada
                                            </span>
                                        @elseif($request->status == 'rejected')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>
                                                Rechazada
                                            </span>
                                        @endif
                                        
                                        <!-- Acciones -->
                                        <div class="flex space-x-2">
                                            <button onclick="showRequestDetails({{ $request->id }})" class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-full hover:bg-blue-50" title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            @if($request->status == 'pending')
                                                <button onclick="editRequest({{ $request->id }})" class="text-green-600 hover:text-green-900 transition-colors duration-200 p-2 rounded-full hover:bg-green-50" title="Editar solicitud">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deleteRequest({{ $request->id }})" class="text-red-600 hover:text-red-900 transition-colors duration-200 p-2 rounded-full hover:bg-red-50" title="Eliminar solicitud">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de Insumos -->
                            <div class="px-6 py-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach($request->items as $item)
                                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                            <div class="flex-shrink-0">
                                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                    <i class="fas fa-box text-green-600 text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ $item->equipment->name ?? 'N/A' }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $item->requested_amount }} {{ $item->equipment->unit ?? 'unidades' }}
                                                </p>
                                            </div>
                                            <div class="flex-shrink-0">
                                                @if($item->status == 'pending')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                        <i class="fas fa-clock mr-1"></i>
                                                        Pendiente
                                                    </span>
                                                @elseif($item->status == 'approved')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        <i class="fas fa-check mr-1"></i>
                                                        Aprobado
                                                    </span>
                                                @elseif($item->status == 'rejected')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        <i class="fas fa-times mr-1"></i>
                                                        Rechazado
                                                    </span>
                                                @elseif($item->status == 'delivered')
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        <i class="fas fa-truck mr-1"></i>
                                                        Entregado
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Descripción si existe -->
                                @if($request->description)
                                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                                        <p class="text-sm text-blue-800">
                                            <i class="fas fa-comment mr-2"></i>
                                            <strong>Descripción:</strong> {{ $request->description }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
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
                    <a href="javascript:void(0)" onclick="openRequestModal()" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Crear primera solicitud
                    </a>
                </div>
            @endif
        </div>

        <!-- Modal para solicitar insumos -->
        <div id="request-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Nueva Solicitud de Insumos</h3>
                    <button type="button" id="close-request-modal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[70vh]">
                    <form id="request-form" action="{{ route('infrastock.cleaning-staff.requests.store') }}" method="POST">
                        @csrf
                        
                        <!-- Sección de Insumos -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-lg font-medium text-gray-900">Insumos a Solicitar</h4>
                                <button type="button" id="add-equipment-btn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Agregar Insumo
                                </button>
                            </div>

                            <!-- Lista de Insumos Seleccionados -->
                            <div id="equipment-list" class="space-y-4">
                                <!-- Los insumos se agregarán dinámicamente aquí -->
                            </div>

                            <!-- Mensaje cuando no hay insumos -->
                            <div id="no-equipment-message" class="text-center py-8 bg-gray-50 rounded-lg">
                                <i class="fas fa-box text-gray-400 text-4xl mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">No hay insumos seleccionados</h4>
                                <p class="text-gray-500 mb-4">Haga clic en "Agregar Insumo" para comenzar a seleccionar los insumos necesarios.</p>
                            </div>
                        </div>

                        <!-- Unidad Productiva/Almacén -->
                        <div class="mb-6">
                            <label for="productive_unit_warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Unidad Productiva/Almacén <span class="text-red-500">*</span>
                            </label>
                            <select name="productive_unit_warehouse_id" id="productive_unit_warehouse_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                <option value="">Seleccione una unidad</option>
                                @foreach($productiveUnitWarehouses as $puw)
                                    <option value="{{ $puw->id }}">
                                        {{ $puw->productiveUnit->name ?? 'Sin nombre' }} - {{ $puw->warehouse->name ?? 'Sin nombre' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Descripción/Justificación -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción/Justificación de la Solicitud
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                      placeholder="Explique brevemente por qué necesita estos insumos y para qué los utilizará..."></textarea>
                            <p class="text-sm text-gray-500 mt-1">Máximo 500 caracteres</p>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <button type="button" id="cancel-request" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Enviar Solicitud
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para seleccionar insumo -->
        <div id="equipment-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Seleccionar Insumo</h3>
                    <button type="button" id="close-equipment-modal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <!-- Buscador de insumos -->
                    <div class="mb-6 relative">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   id="equipment-search" 
                                   class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                                   placeholder="Buscar insumo por nombre o categoría...">
                            <button type="button" 
                                    id="clear-search" 
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 hidden">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Mensaje cuando no hay resultados -->
                    <div id="no-equipment-results" class="hidden text-center py-8">
                        <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No se encontraron insumos</p>
                    </div>
                    
                    <!-- Grid de insumos -->
                    <div id="equipment-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($equipments as $equipment)
                            <div class="equipment-card border border-gray-200 rounded-lg p-4 hover:border-green-500 hover:shadow-md transition-all duration-200 cursor-pointer" 
                                 data-equipment-id="{{ $equipment->id }}"
                                 data-equipment-name="{{ $equipment->name }}"
                                 data-equipment-stock="{{ $equipment->stock }}"
                                 data-equipment-category="{{ $equipment->category->name ?? 'Sin categoría' }}"
                                 data-equipment-description="{{ $equipment->description ?? '' }}"
                                 data-equipment-unit="{{ $equipment->unit ?? 'unidades' }}"
                                 data-equipment-price="{{ $equipment->price ?? 0 }}">
                                
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-medium text-gray-900 text-sm">{{ $equipment->name }}</h4>
                                    <div class="flex items-center space-x-2">
                                        @if($equipment->stock <= 0)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Agotado
                                            </span>
                                        @elseif($equipment->stock <= 5)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                                Poco Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Disponible
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="space-y-1 text-xs text-gray-600">
                                    <p><strong>Categoría:</strong> {{ $equipment->category->name ?? 'Sin categoría' }}</p>
                                    <p><strong>Stock:</strong> {{ $equipment->stock }} {{ $equipment->unit ?? 'unidades' }}</p>
                                    @if($equipment->description)
                                        <p><strong>Descripción:</strong> {{ Str::limit($equipment->description, 50) }}</p>
                                    @endif
                                    @if($equipment->price)
                                        <p><strong>Precio:</strong> ${{ number_format($equipment->price, 0, ',', '.') }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para ver detalles de la solicitud -->
        <div id="details-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Detalles de la Solicitud</h3>
                    <button type="button" id="close-details-modal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[70vh]" id="details-content">
                    <!-- El contenido se cargará dinámicamente aquí -->
                </div>
            </div>
        </div>

        <!-- Modal para editar solicitud -->
        <div id="edit-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-900">Editar Solicitud</h3>
                    <button type="button" id="close-edit-modal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[70vh]">
                    <form id="edit-form" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- Lista de Insumos -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium text-gray-900 mb-4">Insumos de la Solicitud</h4>
                            <div id="edit-items-list" class="space-y-4">
                                <!-- Los insumos se cargarán dinámicamente aquí -->
                                    </div>
                        </div>

                        <!-- Unidad Productiva/Almacén -->
                        <div class="mb-6">
                            <label for="edit-productive-unit-warehouse" class="block text-sm font-medium text-gray-700 mb-2">
                                Unidad Productiva/Almacén <span class="text-red-500">*</span>
                            </label>
                            <select name="productive_unit_warehouse_id" id="edit-productive-unit-warehouse" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" required>
                                <option value="">Seleccione una unidad</option>
                                @foreach($productiveUnitWarehouses as $puw)
                                    <option value="{{ $puw->id }}">
                                        {{ $puw->productiveUnit->name ?? 'Sin nombre' }} - {{ $puw->warehouse->name ?? 'Sin nombre' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Descripción -->
                        <div class="mb-6">
                            <label for="edit-description" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción/Justificación
                            </label>
                            <textarea name="description" 
                                      id="edit-description" 
                                      rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                      placeholder="Explique brevemente por qué necesita estos insumos..."></textarea>
                            <p class="text-sm text-gray-500 mt-1">Máximo 500 caracteres</p>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                            <button type="button" id="cancel-edit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para búsqueda de insumos -->
        <div id="equipment-search-modal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">Catálogo de Insumos</h3>
                        <p class="text-sm text-gray-500 mt-1">Busca y explora todos los insumos disponibles</p>
                    </div>
                    <button type="button" id="close-equipment-search-modal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <!-- Filtros del modal de búsqueda -->
                <div class="p-6 border-b border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="equipment-search-input" class="block text-sm font-medium text-gray-700 mb-2">Buscar Insumo</label>
                            <div class="relative">
                                <input type="text" id="equipment-search-input" class="w-full px-3 py-2 pl-10 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" placeholder="Nombre del insumo...">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="equipment-category-filter" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Categoría</label>
                            <select id="equipment-category-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Todas las categorías</option>
                                <!-- Las categorías se cargarán dinámicamente -->
                            </select>
                        </div>
                        
                        <div>
                            <label for="equipment-stock-filter" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Stock</label>
                            <select id="equipment-stock-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Todo el stock</option>
                                <option value="available">Disponible</option>
                                <option value="low">Poco stock (≤5)</option>
                                <option value="out">Agotado</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex justify-between items-center">
                        <div class="text-sm text-gray-600">
                            <span id="equipment-search-count">0</span> insumos encontrados
                        </div>
                        <button type="button" id="clear-equipment-search" class="text-sm text-gray-500 hover:text-gray-700 flex items-center">
                            <i class="fas fa-times mr-1"></i>
                            Limpiar filtros
                        </button>
                    </div>
                </div>
                
                <!-- Lista de insumos -->
                <div class="p-6 overflow-y-auto max-h-[60vh]">
                    <div id="equipment-search-results" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Los resultados se cargarán dinámicamente aquí -->
                    </div>
                    
                    <!-- Mensaje cuando no hay resultados -->
                    <div id="no-equipment-results" class="text-center py-12 hidden">
                        <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                        <h4 class="text-lg font-medium text-gray-900 mb-2">No se encontraron insumos</h4>
                        <p class="text-gray-500">Intenta con otros términos de búsqueda o filtros.</p>
                    </div>
                </div>
            </div>
        </div>

    </main>

    
<!-- Modal para Ver Detalles de Solicitud -->
<div id="showDetailsModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl my-8 max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header del Modal -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-green-50">
            <div>
                <h3 class="text-2xl font-bold text-gray-900" id="details-modal-title">Detalles de la Solicitud</h3>
                <p class="text-sm text-gray-600 mt-1">Información completa de la solicitud y sus insumos.</p>
            </div>
            <button onclick="closeDetailsModal()" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <!-- Contenido del Modal -->
        <div class="flex-1 overflow-y-auto p-6" id="details-modal-content">
            <div class="flex justify-center py-8">
                <i class="fas fa-spinner fa-spin text-4xl text-green-500"></i>
            </div>
        </div>

        <!-- Footer del Modal -->
        <div class="flex justify-end p-6 border-t border-gray-200 bg-gray-50">
            <button onclick="closeDetailsModal()" 
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>

        document.addEventListener('DOMContentLoaded', function() {
            // Check for open_modal query parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('open_modal') === '1') {
                if (typeof window.openRequestModal === 'function') {
                    window.openRequestModal();
                    
                    // Si trae equipmentId, intentar pre-seleccionarlo
                    const equipmentId = urlParams.get('equipment');
                    if (equipmentId) {
                        setTimeout(() => {
                            const equipmentCard = document.querySelector(`.equipment-card[data-equipment-id="${equipmentId}"]`);
                            if (equipmentCard) {
                                equipmentCard.click();
                            }
                        }, 500); // Pequeño retraso para asegurar que los elementos estén listos
                    }
                }
            }

            const searchInput = document.getElementById('search');
            const statusFilter = document.getElementById('status-filter');
            const dateFilter = document.getElementById('date-filter');
            const requestCards = document.querySelectorAll('.bg-white.rounded-lg.shadow-sm.border.border-gray-200');

            // Elementos del modal
            const openRequestModalBtn = document.getElementById('open-request-modal');
            const requestModal = document.getElementById('request-modal');
            const closeRequestModalBtn = document.getElementById('close-request-modal');
            const cancelRequestBtn = document.getElementById('cancel-request');
            
            const addEquipmentBtn = document.getElementById('add-equipment-btn');
            const equipmentModal = document.getElementById('equipment-modal');
            const closeEquipmentModalBtn = document.getElementById('close-equipment-modal');
            const equipmentCards = document.querySelectorAll('.equipment-card');
            const equipmentSearch = document.getElementById('equipment-search');
            const clearEquipmentSearchBtn = document.getElementById('clear-search');
            const equipmentGrid = document.getElementById('equipment-grid');
            const noEquipmentResults = document.getElementById('no-equipment-results');
            
            const equipmentList = document.getElementById('equipment-list');
            const noEquipmentMessage = document.getElementById('no-equipment-message');
            
            let equipmentCounter = 0;
            let selectedEquipments = new Set();

            // Función para filtrar las solicitudes
            function filterRequests() {
                if (!searchInput || !statusFilter || !dateFilter) {
                    console.error('Elementos de filtro no encontrados');
                    return;
                }
                
                const searchTerm = searchInput.value.toLowerCase().trim();
                const statusValue = statusFilter.value;
                const dateValue = dateFilter.value;
                const today = new Date();
                
                if (!requestCards || requestCards.length === 0) {
                    console.warn('No se encontraron tarjetas de solicitudes para filtrar');
                    return;
                }
                
                requestCards.forEach(card => {
                    // Obtener información de la solicitud
                    const requestId = card.querySelector('h3').textContent.match(/#(\d+)/)?.[1];
                    const requestDateText = card.querySelector('.flex.items-center.space-x-4.text-sm.text-gray-600 span:first-child').textContent;
                    const statusElement = card.querySelector('.inline-flex.items-center.px-3.py-1.rounded-full.text-sm.font-medium');
                    const statusText = statusElement ? statusElement.textContent.trim() : '';
                    
                    // Extraer información completa de la solicitud para búsqueda
                    const equipmentNames = Array.from(card.querySelectorAll('.text-sm.font-medium.text-gray-900.truncate'))
                        .map(el => el.textContent.toLowerCase());
                    
                    const requestDescription = card.querySelector('.text-sm.text-blue-800')?.textContent.toLowerCase() || '';
                    const productiveUnit = card.querySelector('.flex.items-center.space-x-4.text-sm.text-gray-600 span:last-child')?.textContent.toLowerCase() || '';
                    
                    // Convertir fecha
                    const requestDate = parseRequestDate(requestDateText);
                    
                    let showCard = true;
                    
                    // Filtro por búsqueda (buscar en múltiples campos)
                    if (searchTerm) {
                        const searchFields = [
                            ...equipmentNames,
                            requestDescription,
                            productiveUnit,
                            requestId || ''
                        ];
                        
                        const hasMatch = searchFields.some(field => 
                            field.includes(searchTerm)
                        );
                        
                        if (!hasMatch) {
                            showCard = false;
                        }
                    }
                    
                    // Filtro por estado
                    if (statusValue) {
                        const currentStatus = getStatusFromText(statusText);
                        if (currentStatus !== statusValue) {
                            showCard = false;
                        }
                    }
                    
                    // Filtro por fecha
                    if (dateValue && requestDate) {
                        switch(dateValue) {
                            case 'today':
                                if (requestDate.toDateString() !== today.toDateString()) {
                                    showCard = false;
                                }
                                break;
                            case 'week':
                                const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                                if (requestDate < weekAgo) {
                                    showCard = false;
                                }
                                break;
                            case 'month':
                                const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
                                if (requestDate < monthAgo) {
                                    showCard = false;
                                }
                                break;
                        }
                    }
                    
                    card.style.display = showCard ? '' : 'none';
                });
                
                // Actualizar contador de resultados
                updateResultsCount();
            }

            // Función para parsear la fecha de la solicitud
            function parseRequestDate(dateText) {
                try {
                    // Formato esperado: "dd/mm/yyyy H:i"
                    const match = dateText.match(/(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})/);
                    if (match) {
                        const [, day, month, year, hour, minute] = match;
                        return new Date(year, month - 1, day, hour, minute);
                    }
                } catch (e) {
                    console.warn('Error parsing date:', dateText);
                }
                return null;
            }

            // Función para obtener el estado desde el texto
            function getStatusFromText(statusText) {
                if (statusText.includes('Pendiente')) return 'pending';
                if (statusText.includes('Aprobada')) return 'approved';
                if (statusText.includes('Rechazada')) return 'rejected';
                return '';
            }

            // Función para actualizar el contador de resultados
            function updateResultsCount() {
                const visibleCards = Array.from(requestCards).filter(card => card.style.display !== 'none');
                const totalElement = document.querySelector('.text-sm.text-gray-500.mt-1');
                if (totalElement) {
                    totalElement.textContent = `Total: ${visibleCards.length} solicitudes`;
                }
                
                // Mostrar mensaje cuando no hay resultados
                const noResultsMessage = document.getElementById('no-results-message');
                if (visibleCards.length === 0 && (searchInput.value || statusFilter.value || dateFilter.value)) {
                    if (!noResultsMessage) {
                        createNoResultsMessage();
                    }
                } else if (noResultsMessage) {
                    noResultsMessage.remove();
                }
            }

            // Función para crear mensaje de no resultados
            function createNoResultsMessage() {
                const container = document.querySelector('.space-y-4');
                const noResultsDiv = document.createElement('div');
                noResultsDiv.id = 'no-results-message';
                noResultsDiv.className = 'text-center py-12 bg-gray-50 rounded-lg';
                noResultsDiv.innerHTML = `
                    <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron resultados</h3>
                    <p class="text-gray-500 mb-4">No hay solicitudes que coincidan con los filtros aplicados.</p>
                    <button type="button" id="clear-filters-from-no-results" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar filtros
                    </button>
                `;
                
                container.appendChild(noResultsDiv);
                
                // Event listener para el botón de limpiar filtros desde el mensaje
                document.getElementById('clear-filters-from-no-results').addEventListener('click', function() {
                    clearFiltersBtn.click();
                });
            }

            // Event listeners para los filtros
            if (searchInput) {
                searchInput.addEventListener('input', filterRequests);
            }
            if (statusFilter) {
                statusFilter.addEventListener('change', filterRequests);
            }
            if (dateFilter) {
                dateFilter.addEventListener('change', filterRequests);
            }

            // Botón para limpiar búsqueda de solicitudes
            const clearRequestSearchBtn = document.getElementById('clear-request-search');
            if (clearRequestSearchBtn && searchInput) {
                clearRequestSearchBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    filterRequests();
                    updateClearRequestButton();
                });

                // Mostrar/ocultar botón de limpiar búsqueda
                searchInput.addEventListener('input', function() {
                    updateClearRequestButton();
                });

                function updateClearRequestButton() {
                    if (searchInput && clearRequestSearchBtn) {
                        if (searchInput.value.length > 0) {
                            clearRequestSearchBtn.style.display = 'flex';
                        } else {
                            clearRequestSearchBtn.style.display = 'none';
                        }
                    }
                }
            }

            // Botón para limpiar todos los filtros
            const clearFiltersBtn = document.getElementById('clear-filters');
            clearFiltersBtn.addEventListener('click', function() {
                searchInput.value = '';
                statusFilter.value = '';
                dateFilter.value = '';
                filterRequests();
                updateClearButton();
                updateActiveFilters();
            });

            // Función para actualizar los filtros activos
            function updateActiveFilters() {
                const activeFiltersDiv = document.getElementById('active-filters');
                const filterTagsDiv = document.getElementById('filter-tags');
                
                const filters = [];
                
                if (searchInput.value) {
                    filters.push({
                        type: 'search',
                        label: `Búsqueda: "${searchInput.value}"`,
                        remove: () => {
                            searchInput.value = '';
                            filterRequests();
                            updateClearButton();
                            updateActiveFilters();
                        }
                    });
                }
                
                if (statusFilter.value) {
                    const statusLabels = {
                        'pending': 'Pendientes',
                        'approved': 'Aprobadas',
                        'rejected': 'Rechazadas'
                    };
                    filters.push({
                        type: 'status',
                        label: `Estado: ${statusLabels[statusFilter.value]}`,
                        remove: () => {
                            statusFilter.value = '';
                            filterRequests();
                            updateActiveFilters();
                        }
                    });
                }
                
                if (dateFilter.value) {
                    const dateLabels = {
                        'today': 'Hoy',
                        'week': 'Esta semana',
                        'month': 'Este mes'
                    };
                    filters.push({
                        type: 'date',
                        label: `Fecha: ${dateLabels[dateFilter.value]}`,
                        remove: () => {
                            dateFilter.value = '';
                            filterRequests();
                            updateActiveFilters();
                        }
                    });
                }
                
                if (filters.length > 0) {
                    activeFiltersDiv.classList.remove('hidden');
                    filterTagsDiv.innerHTML = filters.map(filter => `
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ${filter.label}
                            <button type="button" onclick="(${filter.remove})()" class="ml-1 hover:text-green-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    `).join('');
                } else {
                    activeFiltersDiv.classList.add('hidden');
                }
            }

            // Actualizar filtros activos cuando cambien los filtros
            statusFilter.addEventListener('change', updateActiveFilters);
            dateFilter.addEventListener('change', updateActiveFilters);

            // === FUNCIONALIDAD DE BÚSQUEDA DE INSUMOS ===
            
            // Variables para el modal de búsqueda de insumos
            const equipmentSearchModal = document.getElementById('equipment-search-modal');
            const closeEquipmentSearchModalBtn = document.getElementById('close-equipment-search-modal');
            const equipmentSearchInput = document.getElementById('equipment-search-input');
            const equipmentCategoryFilter = document.getElementById('equipment-category-filter');
            const equipmentStockFilter = document.getElementById('equipment-stock-filter');
            const equipmentSearchResults = document.getElementById('equipment-search-results');
            const equipmentSearchCount = document.getElementById('equipment-search-count');
            const noEquipmentSearchResults = document.getElementById('no-equipment-results');
            const clearEquipmentSearchModalBtn = document.getElementById('clear-equipment-search');

            // Función para abrir el modal de búsqueda de insumos
            function openEquipmentSearchModal() {
                equipmentSearchModal.classList.remove('hidden');
                equipmentSearchInput.focus();
                loadEquipmentCategories();
                searchEquipment();
            }

            // Función para manejar la entrada en el campo de búsqueda
            function handleSearchInput() {
                const searchTerm = searchInput.value.trim();
                if (searchTerm.length > 0) {
                    openEquipmentSearchModal();
                    equipmentSearchInput.value = searchTerm;
                    searchEquipment();
                }
            }

            // Función para cerrar el modal de búsqueda de insumos
            function closeEquipmentSearchModal() {
                equipmentSearchModal.classList.add('hidden');
                searchInput.value = '';
                updateClearButton();
            }

            // Event listeners para el modal de búsqueda de insumos
            closeEquipmentSearchModalBtn.addEventListener('click', closeEquipmentSearchModal);
            equipmentSearchModal.addEventListener('click', function(e) {
                if (e.target === equipmentSearchModal) {
                    closeEquipmentSearchModal();
                }
            });

            // Event listeners para los filtros del modal de búsqueda
            equipmentSearchInput.addEventListener('input', searchEquipment);
            equipmentCategoryFilter.addEventListener('change', searchEquipment);
            equipmentStockFilter.addEventListener('change', searchEquipment);

            // Botón para limpiar filtros de búsqueda de insumos
            if (clearEquipmentSearchModalBtn) {
                clearEquipmentSearchModalBtn.addEventListener('click', function() {
                    equipmentSearchInput.value = '';
                    equipmentCategoryFilter.value = '';
                    equipmentStockFilter.value = '';
                    searchEquipment();
                });
            }

            // Función para cargar las categorías de insumos
            function loadEquipmentCategories() {
                const categories = [...new Set(Array.from(equipmentCards).map(eq => eq.dataset.equipmentCategory))];
                equipmentCategoryFilter.innerHTML = '<option value="">Todas las categorías</option>';
                categories.forEach(category => {
                    if (category && category !== 'Sin categoría') {
                        const option = document.createElement('option');
                        option.value = category;
                        option.textContent = category;
                        equipmentCategoryFilter.appendChild(option);
                    }
                });
            }

            // Función para buscar insumos
            function searchEquipment() {
                const searchTerm = equipmentSearchInput.value.toLowerCase().trim();
                const categoryFilter = equipmentCategoryFilter.value;
                const stockFilter = equipmentStockFilter.value;

                let filteredEquipments = Array.from(equipmentCards);

                // Filtro por término de búsqueda
                if (searchTerm) {
                    filteredEquipments = filteredEquipments.filter(equipment => {
                        const name = equipment.dataset.equipmentName.toLowerCase();
                        const category = equipment.dataset.equipmentCategory.toLowerCase();
                        const description = equipment.dataset.equipmentDescription.toLowerCase();
                        return name.includes(searchTerm) || 
                               category.includes(searchTerm) || 
                               description.includes(searchTerm);
                    });
                }

                // Filtro por categoría
                if (categoryFilter) {
                    filteredEquipments = filteredEquipments.filter(equipment => 
                        equipment.dataset.equipmentCategory === categoryFilter
                    );
                }

                // Filtro por stock
                if (stockFilter) {
                    filteredEquipments = filteredEquipments.filter(equipment => {
                        const stock = parseInt(equipment.dataset.equipmentStock);
                        switch(stockFilter) {
                            case 'available':
                                return stock > 5;
                            case 'low':
                                return stock > 0 && stock <= 5;
                            case 'out':
                                return stock <= 0;
                            default:
                                return true;
                        }
                    });
                }

                // Mostrar resultados
                displayEquipmentResults(filteredEquipments);
            }

            // Función para mostrar los resultados de búsqueda de insumos
            function displayEquipmentResults(equipments) {
                equipmentSearchResults.innerHTML = '';
                equipmentSearchCount.textContent = equipments.length;

                if (equipments.length === 0) {
                    noEquipmentSearchResults.classList.remove('hidden');
                    return;
                }

                noEquipmentSearchResults.classList.add('hidden');

                equipments.forEach(equipment => {
                    const equipmentCard = document.createElement('div');
                    equipmentCard.className = 'equipment-search-card border border-gray-200 rounded-lg p-4 hover:border-green-500 hover:shadow-md transition-all duration-200 cursor-pointer';
                    
                    const stock = parseInt(equipment.dataset.equipmentStock);
                    const stockStatus = stock <= 0 ? 'Agotado' : stock <= 5 ? 'Poco Stock' : 'Disponible';
                    const stockClass = stock <= 0 ? 'bg-red-100 text-red-800' : stock <= 5 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800';
                    
                    equipmentCard.innerHTML = `
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900 text-sm">${equipment.dataset.equipmentName}</h4>
                                <p class="text-xs text-gray-600">${equipment.dataset.equipmentCategory}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${stockClass}">
                                <i class="fas ${stock <= 0 ? 'fa-times-circle' : stock <= 5 ? 'fa-exclamation-triangle' : 'fa-check-circle'} mr-1"></i>
                                ${stockStatus}
                            </span>
                        </div>
                        
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between">
                                <span><strong>Stock:</strong></span>
                                <span>${stock} ${equipment.dataset.equipmentUnit}</span>
                            </div>
                            ${equipment.dataset.equipmentDescription ? `
                                <div>
                                    <strong>Descripción:</strong>
                                    <p class="mt-1">${equipment.dataset.equipmentDescription}</p>
                                </div>
                            ` : ''}
                            ${equipment.dataset.equipmentPrice > 0 ? `
                                <div class="flex justify-between">
                                    <span><strong>Precio:</strong></span>
                                    <span>$${parseFloat(equipment.dataset.equipmentPrice).toLocaleString()}</span>
                                </div>
                            ` : ''}
                        </div>
                        
                        <div class="mt-4 flex space-x-2">
                            <button type="button" class="flex-1 bg-green-500 hover:bg-green-600 text-white text-xs py-2 px-3 rounded-md transition-colors duration-200" onclick="addEquipmentToRequest('${equipment.dataset.equipmentId}')">
                                <i class="fas fa-plus mr-1"></i>
                                Agregar a Solicitud
                            </button>
                            <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-2 px-3 rounded-md transition-colors duration-200" onclick="viewEquipmentDetails('${equipment.dataset.equipmentId}')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    `;
                    
                    equipmentSearchResults.appendChild(equipmentCard);
                });
            }

            // Función global para agregar insumo a solicitud
            window.addEquipmentToRequest = function(equipmentId) {
                const equipment = Array.from(equipmentCards).find(eq => eq.dataset.equipmentId === equipmentId);
                if (equipment) {
                    addEquipmentToList(equipment);
                    closeEquipmentSearchModal();
                    // Abrir modal de solicitud si no está abierto
                    if (requestModal.classList.contains('hidden')) {
                        requestModal.classList.remove('hidden');
                    }
                }
            };

            // Función global para ver detalles del insumo
            window.viewEquipmentDetails = function(equipmentId) {
                const equipment = Array.from(equipmentCards).find(eq => eq.dataset.equipmentId === equipmentId);
                if (equipment) {
                    // Aquí podrías implementar un modal de detalles del insumo
                    alert(`Detalles de ${equipment.dataset.equipmentName}\n\nStock: ${equipment.dataset.equipmentStock} ${equipment.dataset.equipmentUnit}\nCategoría: ${equipment.dataset.equipmentCategory}`);
                }
            };

            // === FUNCIONALIDAD DEL MODAL DE SOLICITUD ===
            
            // Abrir modal de solicitud
            if (openRequestModalBtn && requestModal) {
                openRequestModalBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    requestModal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            } else {
                console.error('No se encontró el botón open-request-modal o el modal request-modal');
                if (!openRequestModalBtn) {
                    console.error('El botón open-request-modal no existe en el DOM');
                }
                if (!requestModal) {
                    console.error('El modal request-modal no existe en el DOM');
                }
            }

            // Cerrar modal de solicitud
            if (closeRequestModalBtn && requestModal) {
                closeRequestModalBtn.addEventListener('click', function() {
                    requestModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    if (typeof resetForm === 'function') {
                        resetForm();
                    }
                });
            }

            if (cancelRequestBtn && requestModal) {
                cancelRequestBtn.addEventListener('click', function() {
                    requestModal.classList.add('hidden');
                    document.body.style.overflow = '';
                    if (typeof resetForm === 'function') {
                        resetForm();
                    }
                });
            }

            // Cerrar modal al hacer clic fuera
            if (requestModal) {
                requestModal.addEventListener('click', function(e) {
                    if (e.target === requestModal) {
                        requestModal.classList.add('hidden');
                        document.body.style.overflow = '';
                        if (typeof resetForm === 'function') {
                            resetForm();
                        }
                    }
                });
            }

            // === FUNCIONALIDAD DEL MODAL DE INSUMOS ===
            
            // Función para abrir el modal de insumos
            function openEquipmentModal() {
                const modal = document.getElementById('equipment-modal');
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    if (typeof updateEquipmentCards === 'function') {
                        updateEquipmentCards();
                    }
                    // Limpiar búsqueda al abrir el modal
                    const searchInput = document.getElementById('equipment-search');
                    const clearBtn = document.getElementById('clear-search');
                    if (searchInput) {
                        searchInput.value = '';
                        if (clearBtn) clearBtn.classList.add('hidden');
                        if (typeof filterEquipmentCards === 'function') {
                            filterEquipmentCards();
                        }
                    }
                } else {
                    console.error('No se encontró el modal equipment-modal');
                }
            }
            
            // Abrir modal de insumos - usar delegación de eventos para elementos dinámicos
            document.addEventListener('click', function(e) {
                if (e.target && (e.target.id === 'add-equipment-btn' || e.target.closest('#add-equipment-btn'))) {
                    e.preventDefault();
                    e.stopPropagation();
                    openEquipmentModal();
                }
            });
            
            // También agregar listener directo si el botón existe
            if (addEquipmentBtn && equipmentModal) {
                addEquipmentBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    openEquipmentModal();
                });
            }
            
            // Filtrar insumos en tiempo real
            if (equipmentSearch) {
                equipmentSearch.addEventListener('input', function() {
                    filterEquipmentCards();
                    // Mostrar/ocultar botón de limpiar
                    if (this.value.length > 0 && clearEquipmentSearchBtn) {
                        clearEquipmentSearchBtn.classList.remove('hidden');
                    } else if (clearEquipmentSearchBtn) {
                        clearEquipmentSearchBtn.classList.add('hidden');
                    }
                });
            }
            
            // Limpiar búsqueda
            if (clearEquipmentSearchBtn) {
                clearEquipmentSearchBtn.addEventListener('click', function() {
                    if (equipmentSearch) {
                        equipmentSearch.value = '';
                        clearEquipmentSearchBtn.classList.add('hidden');
                        filterEquipmentCards();
                        equipmentSearch.focus();
                    }
                });
            }
            
            // Función para filtrar las tarjetas de insumos
            function filterEquipmentCards() {
                if (!equipmentSearch || !equipmentCards) return;
                
                const searchTerm = equipmentSearch.value.toLowerCase().trim();
                let visibleCount = 0;
                
                equipmentCards.forEach(card => {
                    const equipmentName = card.dataset.equipmentName.toLowerCase();
                    const equipmentCategory = card.dataset.equipmentCategory.toLowerCase();
                    
                    if (searchTerm === '' || 
                        equipmentName.includes(searchTerm) || 
                        equipmentCategory.includes(searchTerm)) {
                        card.style.display = 'block';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
                
                // Mostrar/ocultar mensaje de no resultados
                if (noEquipmentResults && equipmentGrid) {
                    if (visibleCount === 0 && searchTerm !== '') {
                        noEquipmentResults.classList.remove('hidden');
                        equipmentGrid.classList.add('hidden');
                    } else {
                        noEquipmentResults.classList.add('hidden');
                        equipmentGrid.classList.remove('hidden');
                    }
                }
            }

            // Cerrar modal de insumos
            closeEquipmentModalBtn.addEventListener('click', function() {
                equipmentModal.classList.add('hidden');
            });

            // Cerrar modal al hacer clic fuera
            equipmentModal.addEventListener('click', function(e) {
                if (e.target === equipmentModal) {
                    equipmentModal.classList.add('hidden');
                }
            });

            // Seleccionar insumo
            equipmentCards.forEach(card => {
                card.addEventListener('click', function() {
                    const equipmentId = this.dataset.equipmentId;
                    const equipmentStock = parseInt(this.dataset.equipmentStock);
                    
                    // Verificar si el insumo está agotado
                    if (equipmentStock <= 0) {
                        alert('Este insumo está agotado y no se puede seleccionar.');
                        return;
                    }
                    
                    if (selectedEquipments.has(equipmentId)) {
                        alert('Este insumo ya ha sido seleccionado.');
                        return;
                    }

                    addEquipmentToList(this);
                    equipmentModal.classList.add('hidden');
                });
            });

            function addEquipmentToList(card) {
                const equipmentId = card.dataset.equipmentId;
                const equipmentName = card.dataset.equipmentName;
                const equipmentStock = parseInt(card.dataset.equipmentStock);
                const equipmentCategory = card.dataset.equipmentCategory;
                const equipmentDescription = card.dataset.equipmentDescription;
                const equipmentUnit = card.dataset.equipmentUnit;
                const equipmentPrice = parseFloat(card.dataset.equipmentPrice);

                selectedEquipments.add(equipmentId);
                equipmentCounter++;

                const equipmentItem = document.createElement('div');
                equipmentItem.className = 'equipment-item bg-gray-50 border border-gray-200 rounded-lg p-4';
                equipmentItem.innerHTML = `
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">${equipmentName}</h4>
                            <p class="text-sm text-gray-600">${equipmentCategory}</p>
                        </div>
                        <button type="button" class="remove-equipment text-red-500 hover:text-red-700" data-equipment-id="${equipmentId}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                            <input type="number" 
                                   name="equipments[${equipmentId}][amount]" 
                                   min="1" 
                                   max="${equipmentStock}"
                                   value="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Máximo: ${equipmentStock} ${equipmentUnit}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Información</label>
                            <div class="text-xs text-gray-600 space-y-1">
                                <p><strong>Stock:</strong> ${equipmentStock} ${equipmentUnit}</p>
                                ${equipmentDescription ? `<p><strong>Descripción:</strong> ${equipmentDescription}</p>` : ''}
                                ${equipmentPrice > 0 ? `<p><strong>Precio:</strong> $${equipmentPrice.toLocaleString()}</p>` : ''}
                            </div>
                        </div>
                    </div>
                `;

                equipmentList.appendChild(equipmentItem);
                noEquipmentMessage.style.display = 'none';

                // Agregar evento para remover insumo
                const removeBtn = equipmentItem.querySelector('.remove-equipment');
                removeBtn.addEventListener('click', function() {
                    const id = this.dataset.equipmentId;
                    selectedEquipments.delete(id);
                    equipmentItem.remove();
                    
                    if (selectedEquipments.size === 0) {
                        noEquipmentMessage.style.display = 'block';
                    }
                    
                    updateEquipmentCards();
                });

                updateEquipmentCards();
            }

            function updateEquipmentCards() {
                equipmentCards.forEach(card => {
                    const equipmentId = card.dataset.equipmentId;
                    const equipmentStock = parseInt(card.dataset.equipmentStock);
                    
                    if (selectedEquipments.has(equipmentId)) {
                        card.classList.add('opacity-50', 'cursor-not-allowed');
                        card.style.pointerEvents = 'none';
                    } else if (equipmentStock <= 0) {
                        // Insumos agotados: visibles pero no seleccionables
                        card.classList.add('opacity-60', 'cursor-not-allowed');
                        card.style.pointerEvents = 'none';
                        card.style.backgroundColor = '#fef2f2'; // Fondo rojo claro
                    } else {
                        card.classList.remove('opacity-50', 'opacity-60', 'cursor-not-allowed');
                        card.style.pointerEvents = 'auto';
                        card.style.backgroundColor = '';
                    }
                });
            }

            function resetForm() {
                // Limpiar formulario
                document.getElementById('productive_unit_warehouse_id').value = '';
                document.getElementById('description').value = '';
                
                // Limpiar lista de insumos
                equipmentList.innerHTML = '';
                selectedEquipments.clear();
                equipmentCounter = 0;
                noEquipmentMessage.style.display = 'block';
                
                // Actualizar tarjetas
                updateEquipmentCards();
            }

            // Validar formulario antes de enviar
            document.getElementById('request-form').addEventListener('submit', function(e) {
                if (selectedEquipments.size === 0) {
                    e.preventDefault();
                    alert('Debe seleccionar al menos un insumo para la solicitud.');
                    return false;
                }

                // Validar cantidades
                const amountInputs = document.querySelectorAll('input[name*="[amount]"]');
                let hasErrors = false;

                amountInputs.forEach(input => {
                    const maxAmount = parseInt(input.max);
                    const currentAmount = parseInt(input.value);

                    if (currentAmount > maxAmount) {
                        input.setCustomValidity(`La cantidad no puede ser mayor a ${maxAmount}`);
                        hasErrors = true;
                    } else if (currentAmount < 1) {
                        input.setCustomValidity('La cantidad debe ser al menos 1');
                        hasErrors = true;
                    } else {
                        input.setCustomValidity('');
                    }
                });

                if (hasErrors) {
                    e.preventDefault();
                    alert('Por favor, corrija los errores en las cantidades antes de enviar.');
                    return false;
                }
            });

            // Validar longitud de descripción
            const descriptionTextarea = document.getElementById('description');
            descriptionTextarea.addEventListener('input', function() {
                const maxLength = 500;
                const currentLength = this.value.length;
                
                if (currentLength > maxLength) {
                    this.setCustomValidity(`La descripción no puede tener más de ${maxLength} caracteres`);
                } else {
                    this.setCustomValidity('');
                }
            });
        });

        // === FUNCIONALIDAD DE DETALLES ===
        
        // Elementos del modal de detalles
        const detailsModal = document.getElementById('details-modal');
        const closeDetailsModalBtn = document.getElementById('close-details-modal');
        const detailsContent = document.getElementById('details-content');

        // Abrir modal de detalles
        function showRequestDetails(requestId) {
            // Mostrar loading
            detailsContent.innerHTML = `
                <div class="flex items-center justify-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-500"></div>
                    <span class="ml-3 text-gray-600">Cargando detalles...</span>
                </div>
            `;
            
            detailsModal.classList.remove('hidden');
            
            // Simular carga de datos (en una implementación real, harías una petición AJAX)
            setTimeout(() => {
                loadRequestDetails(requestId);
            }, 500);
        }

        // Cerrar modal de detalles
        closeDetailsModalBtn.addEventListener('click', function() {
            detailsModal.classList.add('hidden');
        });

        detailsModal.addEventListener('click', function(e) {
            if (e.target === detailsModal) {
                detailsModal.classList.add('hidden');
            }
        });

        // Cargar detalles de la solicitud
        function loadRequestDetails(requestId) {
            fetch(`/infrastock/cleaning-staff/requests/${requestId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        detailsContent.innerHTML = `
                            <div class="text-center py-8">
                                <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-900 mb-2">Error</h4>
                                <p class="text-gray-500">${data.error}</p>
                            </div>
                        `;
                        return;
                    }

                    const statusBadge = data.status === 'pending' 
                        ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-clock mr-1"></i>Pendiente</span>'
                        : data.status === 'approved'
                        ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Aprobada</span>'
                        : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times mr-1"></i>Rechazada</span>';

                    // Generar HTML para los insumos
                    let itemsHtml = '';
                    data.items.forEach(item => {
                        const itemStatusBadge = item.status === 'pending' 
                            ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-clock mr-1"></i>Pendiente</span>'
                            : item.status === 'approved'
                            ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Aprobado</span>'
                            : item.status === 'rejected'
                            ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times mr-1"></i>Rechazado</span>'
                            : '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-truck mr-1"></i>Entregado</span>';

                        itemsHtml += `
                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">${item.equipment_name}</h4>
                                        <p class="text-sm text-gray-600">${item.equipment_category}</p>
                                    </div>
                                    ${itemStatusBadge}
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500">Cantidad Solicitada</label>
                                        <p class="text-gray-900">${item.requested_amount} ${item.unit}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500">Cantidad Aprobada</label>
                                        <p class="text-gray-900">${item.approved_amount || item.requested_amount} ${item.unit}</p>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-500">Cantidad Entregada</label>
                                        <p class="text-gray-900">${item.delivered_amount || item.requested_amount} ${item.unit}</p>
                                    </div>
                                </div>
                                ${item.notes ? `<div class="mt-3 p-2 bg-gray-50 rounded"><p class="text-xs text-gray-600"><strong>Notas:</strong> ${item.notes}</p></div>` : ''}
                            </div>
                        `;
                    });

                    detailsContent.innerHTML = `
                        <div class="space-y-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Información General</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">ID de Solicitud</label>
                                        <p class="text-sm text-gray-900">#${data.id}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                                        ${statusBadge}
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Fecha de Solicitud</label>
                                        <p class="text-sm text-gray-900">${data.created_at}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Solicitado por</label>
                                        <p class="text-sm text-gray-900">${data.user_name}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Total de Insumos</label>
                                        <p class="text-sm text-gray-900">${data.total_items}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Cantidad Total Solicitada</label>
                                        <p class="text-sm text-gray-900">${data.total_requested_amount}</p>
                                    </div>
                                    </div>
                                    </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Detalles de los Insumos</h4>
                                <div class="space-y-4">
                                    ${itemsHtml}
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Ubicación</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Unidad Productiva</label>
                                        <p class="text-sm text-gray-900">${data.productive_unit}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Almacén</label>
                                        <p class="text-sm text-gray-900">${data.warehouse}</p>
                                    </div>
                                </div>
                            </div>
                            
                            ${data.description ? `
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Descripción/Justificación</h4>
                                <p class="text-sm text-gray-700">${data.description}</p>
                            </div>
                            ` : ''}
                        </div>
                    `;
                })
                .catch(error => {
                    detailsContent.innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Error</h4>
                            <p class="text-gray-500">No se pudieron cargar los detalles de la solicitud.</p>
                        </div>
                    `;
                });
        }

        // === FUNCIONALIDAD DE EDICIÓN ===
        
        // Elementos del modal de edición
        const editModal = document.getElementById('edit-modal');
        const closeEditModalBtn = document.getElementById('close-edit-modal');
        const cancelEditBtn = document.getElementById('cancel-edit');
        const editForm = document.getElementById('edit-form');

        // Abrir modal de edición
        function editRequest(requestId) {
            // Mostrar loading
            editModal.classList.remove('hidden');
            
            // Simular carga de datos
            setTimeout(() => {
                loadRequestForEdit(requestId);
            }, 300);
        }

        // Cerrar modal de edición
        closeEditModalBtn.addEventListener('click', function() {
            editModal.classList.add('hidden');
        });

        cancelEditBtn.addEventListener('click', function() {
            editModal.classList.add('hidden');
        });

        editModal.addEventListener('click', function(e) {
            if (e.target === editModal) {
                editModal.classList.add('hidden');
            }
        });

        // Cargar datos para edición
        function loadRequestForEdit(requestId) {
            fetch(`/infrastock/cleaning-staff/requests/${requestId}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        Swal.fire({
                            title: 'Error',
                            text: data.error,
                            icon: 'error',
                            confirmButtonColor: '#EF4444'
                        });
                        editModal.classList.add('hidden');
                        return;
                    }

                    // Limpiar la lista de items
                    const editItemsList = document.getElementById('edit-items-list');
                    editItemsList.innerHTML = '';

                    // Cargar cada insumo de la solicitud
                    data.items.forEach(item => {
                        const itemElement = document.createElement('div');
                        itemElement.className = 'bg-gray-50 border border-gray-200 rounded-lg p-4';
                        itemElement.innerHTML = `
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900">${item.equipment_name}</h4>
                                    <p class="text-sm text-gray-600">${item.equipment_category}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad</label>
                                    <input type="number" 
                                           name="items[${item.id}][requested_amount]" 
                                           min="1" 
                                           max="${item.stock}"
                                           value="${item.requested_amount}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                           required>
                                    <p class="text-xs text-gray-500 mt-1">Máximo: ${item.stock} ${item.unit}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Información</label>
                                    <div class="text-xs text-gray-600 space-y-1">
                                        <p><strong>Stock disponible:</strong> ${item.stock} ${item.unit}</p>
                                        <p><strong>Unidad:</strong> ${item.unit}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        editItemsList.appendChild(itemElement);
                    });

                    // Cargar datos generales de la solicitud
                    document.getElementById('edit-productive-unit-warehouse').value = data.productive_unit_warehouse_id;
                    document.getElementById('edit-description').value = data.description || '';
                    
                    // Actualizar la acción del formulario
                    editForm.action = `/infrastock/cleaning-staff/requests/${requestId}`;
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'No se pudieron cargar los datos para edición.',
                        icon: 'error',
                        confirmButtonColor: '#EF4444'
                    });
                    editModal.classList.add('hidden');
                });
        }

        // === FUNCIONALIDAD DE ELIMINACIÓN ===
        
        function deleteRequest(requestId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer. La solicitud será eliminada permanentemente.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Crear un formulario temporal para enviar la petición DELETE
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/infrastock/cleaning-staff/requests/${requestId}`;
                    
                    // Agregar token CSRF
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    form.appendChild(csrfToken);
                    
                    // Agregar método DELETE
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    form.appendChild(methodField);
                    
                    // Agregar al DOM y enviar
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endsection

