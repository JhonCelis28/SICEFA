@extends('infrastock::layouts.usuarios-master')

@section('title', 'Mis Solicitudes - Operario INFRASTOCK')

@section('content')
<!-- Breadcrumb -->
@section('breadcrumb-items')
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
            <button onclick="openRequestModal()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-md transition-colors duration-200 font-medium inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Solicitar Insumo
            </button>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="bg-white rounded-xl shadow-md p-6 mb-6">
    <form method="GET" action="{{ route('infrastock.operator.requests.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent"
                   placeholder="Buscar solicitudes...">
        </div>
        
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
            <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-transparent">
                <option value="">Todos</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendientes</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprobadas</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregadas</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazadas</option>
            </select>
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md w-full">
                <i class="fas fa-filter mr-2"></i> Filtrar
            </button>
        </div>
    </form>
</div>

<!-- Tabla de Solicitudes -->
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Lista de Solicitudes</h3>
        <p class="text-sm text-gray-500 mt-1">Total: {{ $requests->total() }} solicitudes</p>
    </div>
    
    @if($requests->count() > 0)
        <div class="space-y-4 p-6">
            @foreach($requests as $request)
                <div class="bg-gray-50 rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Solicitud #{{ $request->id }}</h3>
                            <p class="text-sm text-gray-600">{{ $request->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            @if($request->status == 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Pendiente
                                </span>
                            @elseif($request->status == 'approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Aprobada
                                </span>
                            @elseif($request->status == 'delivered')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-truck mr-1"></i> Entregada
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i> Rechazada
                                </span>
                            @endif
                            <a href="{{ route('infrastock.operator.requests.show', $request->id) }}" class="text-blue-600 hover:text-blue-900 transition-colors duration-200 p-2 rounded-full hover:bg-blue-50" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        @foreach($request->items as $item)
                            <div class="bg-white rounded-lg p-3 border border-gray-200">
                                <p class="font-medium text-gray-900">{{ $item->equipment->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600">{{ $item->requested_amount }} {{ $item->equipment->unit ?? 'unidades' }}</p>
                            </div>
                        @endforeach
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
            <button onclick="openRequestModal()" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Crear primera solicitud
            </button>
        </div>
    @endif
</div>

<!-- Modal para Crear Solicitud -->
<div id="requestModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50" style="overflow-y: auto;">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-4xl my-8 max-h-[90vh] overflow-hidden flex flex-col">
        <!-- Header del Modal -->
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-green-50">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Nueva Solicitud de Insumos</h3>
                <p class="text-sm text-gray-600 mt-1">Solo puedes solicitar herramientas e insumos generales (no de aseo)</p>
            </div>
            <button onclick="closeRequestModal()" class="text-gray-500 hover:text-gray-700 transition-colors duration-200">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <!-- Contenido del Modal -->
        <div class="flex-1 overflow-y-auto p-6">
            <form id="requestForm" method="POST" action="{{ route('infrastock.operator.requests.store') }}" class="space-y-6">
                @csrf
                
                <!-- Unidad Productiva/Almacén -->
                <div>
                    <label for="modal_productive_unit_warehouse_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-warehouse mr-2 text-green-500"></i>
                        Unidad Productiva/Almacén <span class="text-red-500">*</span>
                    </label>
                    <select name="productive_unit_warehouse_id" id="modal_productive_unit_warehouse_id" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200" 
                            required>
                        <option value="">Seleccione una unidad</option>
                    </select>
                    <div id="productive_unit_error" class="hidden mt-1 text-sm text-red-600"></div>
                </div>

                <!-- Insumos a Solicitar -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            <i class="fas fa-boxes mr-2 text-green-500"></i>
                            Insumos a Solicitar <span class="text-red-500">*</span>
                        </label>
                        <button type="button" onclick="openEquipmentModal()" 
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 text-sm">
                            <i class="fas fa-plus mr-2"></i>
                            Agregar Insumo
                        </button>
                    </div>

                    <!-- Lista de Insumos Seleccionados -->
                    <div id="equipment-list" class="space-y-4 min-h-[100px]">
                        <div id="no-equipment-message" class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <i class="fas fa-box text-gray-400 text-4xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No hay insumos seleccionados</h4>
                            <p class="text-gray-500 mb-4">Haga clic en "Agregar Insumo" para comenzar</p>
                        </div>
                    </div>
                    <div id="equipment_error" class="hidden mt-1 text-sm text-red-600"></div>
                </div>

                <!-- Descripción/Justificación -->
                <div>
                    <label for="modal_description" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment-alt mr-2 text-green-500"></i>
                        Descripción/Justificación de la Solicitud
                    </label>
                    <textarea name="description" id="modal_description" rows="4" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200" 
                              placeholder="Explique brevemente por qué necesita estos insumos y para qué los utilizará..."></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-sm text-gray-500">Máximo 500 caracteres</p>
                        <span class="text-sm text-gray-400" id="char-count">0/500</span>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer del Modal -->
        <div class="flex justify-end space-x-4 p-6 border-t border-gray-200 bg-gray-50">
            <button onclick="closeRequestModal()" 
                    class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                <i class="fas fa-times mr-2"></i>
                Cancelar
            </button>
            <button onclick="submitRequestForm()" 
                    class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                <i class="fas fa-paper-plane mr-2"></i>
                Enviar Solicitud
            </button>
        </div>
    </div>
</div>

<!-- Modal para Seleccionar Insumos -->
<div id="equipmentModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 hidden z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-blue-50">
            <h3 class="text-xl font-semibold text-gray-900">Seleccionar Insumo</h3>
            <button onclick="closeEquipmentModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-y-auto flex-1">
            <div class="mb-4">
                <input type="text" id="equipmentSearch" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                       placeholder="Buscar insumo por nombre o categoría...">
            </div>
            <div id="equipment-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Los insumos se cargarán aquí dinámicamente -->
            </div>
            <div id="no-equipment-results" class="hidden text-center py-8">
                <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-500">No se encontraron insumos</p>
            </div>
        </div>
    </div>
</div>

<script>
let selectedEquipments = new Set();
let equipmentsData = [];
let productiveUnitWarehousesData = [];

// Abrir modal de solicitud
async function openRequestModal() {
    const modal = document.getElementById('requestModal');
    if (!modal) {
        console.error('Modal no encontrado');
        return;
    }
    
    // Abrir el modal primero
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Cargar datos en segundo plano si no están cargados
    // No bloquear la apertura del modal
    if (equipmentsData.length === 0 || productiveUnitWarehousesData.length === 0) {
        // Mostrar un indicador de carga sutil si es necesario
        loadRequestFormData().catch(err => {
            console.error('Error en carga de datos:', err);
            // El error se maneja dentro de loadRequestFormData
        });
    }
}

// Verificar si debe abrirse el modal automáticamente (desde parámetro GET)
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('open_modal') === '1') {
        // Remover el parámetro de la URL sin recargar
        window.history.replaceState({}, document.title, window.location.pathname);
        // Abrir el modal después de un pequeño delay para asegurar que todo esté cargado
        setTimeout(() => {
            openRequestModal();
        }, 300);
    }
});

// Cerrar modal de solicitud
function closeRequestModal() {
    const modal = document.getElementById('requestModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetRequestForm();
}

// Cargar datos del formulario
async function loadRequestFormData() {
    try {
        const response = await fetch('{{ route("infrastock.operator.requests.create") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        // Verificar el status de la respuesta
        if (!response.ok && response.status !== 200) {
            // Si es un error del servidor (5xx), lanzar error
            if (response.status >= 500) {
                throw new Error(`Error del servidor: ${response.status}`);
            }
            // Para otros códigos (3xx, 4xx), intentar parsear la respuesta
        }
        
        // Obtener datos como texto primero para verificar el tipo
        const text = await response.text();
        let data;
        
        // Verificar si es HTML (redirect o error)
        if (text.trim().startsWith('<!DOCTYPE') || text.trim().startsWith('<html')) {
            throw new Error('El servidor retornó HTML. La petición necesita headers AJAX correctos.');
        }
        
        // Intentar parsear como JSON
        try {
            data = JSON.parse(text);
        } catch (parseError) {
            console.error('Error parseando JSON:', parseError);
            console.error('Respuesta recibida:', text.substring(0, 200));
            throw new Error('La respuesta no es un JSON válido.');
        }
        
        if (data.success) {
            equipmentsData = data.equipments;
            productiveUnitWarehousesData = data.productiveUnitWarehouses;
            
            // Llenar select de unidades productivas
            const select = document.getElementById('modal_productive_unit_warehouse_id');
            if (select) {
                select.innerHTML = '<option value="">Seleccione una unidad</option>';
                productiveUnitWarehousesData.forEach(puw => {
                    const option = document.createElement('option');
                    option.value = puw.id;
                    option.textContent = puw.full_name;
                    select.appendChild(option);
                });
            }
        } else {
            throw new Error(data.message || 'Error al cargar los datos');
        }
    } catch (error) {
        console.error('Error cargando datos:', error);
        // Solo mostrar error si realmente no hay datos cargados
        // Si ya hay datos, no mostrar error (pueden ser datos de una carga anterior)
        if (equipmentsData.length === 0 && productiveUnitWarehousesData.length === 0) {
            // Esperar un momento para no interrumpir la apertura del modal
            setTimeout(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar datos',
                    text: 'No se pudieron cargar los datos del formulario. El modal se abrirá pero algunos datos pueden no estar disponibles. Por favor, recarga la página si el problema persiste.',
                    confirmButtonText: 'Entendido'
                });
            }, 500);
        }
    }
}

// Abrir modal de selección de insumos
function openEquipmentModal() {
    const modal = document.getElementById('equipmentModal');
    modal.classList.remove('hidden');
    renderEquipmentGrid();
}

// Cerrar modal de insumos
function closeEquipmentModal() {
    const modal = document.getElementById('equipmentModal');
    modal.classList.add('hidden');
}

// Renderizar grid de insumos
function renderEquipmentGrid(filtered = null) {
    const grid = document.getElementById('equipment-grid');
    const noResults = document.getElementById('no-equipment-results');
    const equipments = filtered || equipmentsData;
    
    grid.innerHTML = '';
    
    if (equipments.length === 0) {
        grid.classList.add('hidden');
        noResults.classList.remove('hidden');
        return;
    }
    
    grid.classList.remove('hidden');
    noResults.classList.add('hidden');
    
    equipments.forEach(equipment => {
        const isSelected = selectedEquipments.has(equipment.id);
        const isOutOfStock = equipment.stock <= 0;
        const stockClass = isOutOfStock ? 'bg-red-50 border-red-300' : 
                          equipment.stock <= 5 ? 'bg-yellow-50 border-yellow-300' : 
                          'bg-green-50 border-green-300';
        const disabledClass = (isSelected || isOutOfStock) ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:shadow-lg';
        
        const card = document.createElement('div');
        card.className = `equipment-card border-2 rounded-lg p-4 transition-all duration-200 ${stockClass} ${disabledClass}`;
        card.dataset.equipmentId = equipment.id;
        
        if (!isSelected && !isOutOfStock) {
            card.onclick = () => selectEquipment(equipment);
        }
        
        card.innerHTML = `
            <div class="flex items-start justify-between mb-2">
                <h4 class="font-medium text-gray-900 text-sm">${equipment.name}</h4>
                <div class="flex items-center space-x-2">
                    ${isOutOfStock ? 
                        '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Agotado</span>' :
                      equipment.stock <= 5 ?
                        '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-exclamation-triangle mr-1"></i>Poco Stock</span>' :
                        '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Disponible</span>'
                    }
                </div>
            </div>
            <div class="space-y-1 text-xs text-gray-600">
                <p><strong>Categoría:</strong> ${equipment.category}</p>
                <p><strong>Stock:</strong> ${equipment.stock} ${equipment.unit}</p>
                ${equipment.description ? `<p><strong>Descripción:</strong> ${equipment.description.substring(0, 50)}${equipment.description.length > 50 ? '...' : ''}</p>` : ''}
            </div>
            ${isSelected ? '<div class="mt-2 text-center text-xs font-medium text-green-700"><i class="fas fa-check-circle mr-1"></i>Ya seleccionado</div>' : ''}
        `;
        
        grid.appendChild(card);
    });
}

// Seleccionar insumo
function selectEquipment(equipment) {
    if (selectedEquipments.has(equipment.id)) {
        Swal.fire({
            icon: 'warning',
            title: 'Ya seleccionado',
            text: 'Este insumo ya ha sido agregado a tu solicitud.',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }
    
    if (equipment.stock <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Stock agotado',
            text: 'Este insumo no tiene stock disponible.',
            timer: 2000,
            showConfirmButton: false
        });
        return;
    }
    
    selectedEquipments.add(equipment.id);
    addEquipmentToList(equipment);
    closeEquipmentModal();
    renderEquipmentGrid();
}

// Agregar insumo a la lista
function addEquipmentToList(equipment) {
    const list = document.getElementById('equipment-list');
    const noMessage = document.getElementById('no-equipment-message');
    
    if (noMessage) {
        noMessage.classList.add('hidden');
    }
    
    const item = document.createElement('div');
    item.className = 'equipment-item bg-gray-50 border border-gray-200 rounded-lg p-4';
    item.id = `equipment-item-${equipment.id}`;
    
    item.innerHTML = `
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <h4 class="font-medium text-gray-900">${equipment.name}</h4>
                <p class="text-sm text-gray-600">${equipment.category}</p>
            </div>
            <button type="button" onclick="removeEquipment(${equipment.id})" class="text-red-500 hover:text-red-700 transition-colors duration-200">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad *</label>
                <input type="number" 
                       name="equipments[${equipment.id}][amount]" 
                       min="1" 
                       max="${equipment.stock}"
                       value="1"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-green-500 focus:border-green-500"
                       onchange="validateAmount(this, ${equipment.stock})">
                <p class="text-xs text-gray-500 mt-1">Máximo: ${equipment.stock} ${equipment.unit}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Información</label>
                <div class="text-xs text-gray-600 space-y-1">
                    <p><strong>Stock:</strong> ${equipment.stock} ${equipment.unit}</p>
                    ${equipment.price > 0 ? `<p><strong>Precio:</strong> $${equipment.price.toLocaleString()}</p>` : ''}
                </div>
            </div>
        </div>
    `;
    
    list.appendChild(item);
}

// Remover insumo
function removeEquipment(equipmentId) {
    selectedEquipments.delete(equipmentId);
    const item = document.getElementById(`equipment-item-${equipmentId}`);
    if (item) {
        item.remove();
    }
    
    const list = document.getElementById('equipment-list');
    if (list.children.length === 1) { // Solo queda el mensaje de "no hay insumos"
        const noMessage = document.getElementById('no-equipment-message');
        if (noMessage) {
            noMessage.classList.remove('hidden');
        }
    }
    
    renderEquipmentGrid();
}

// Validar cantidad
function validateAmount(input, maxStock) {
    const value = parseInt(input.value);
    if (value > maxStock) {
        Swal.fire({
            icon: 'warning',
            title: 'Cantidad inválida',
            text: `La cantidad no puede ser mayor a ${maxStock}.`,
            timer: 2000,
            showConfirmButton: false
        });
        input.value = maxStock;
    } else if (value < 1) {
        input.value = 1;
    }
}

// Filtrar insumos en el modal
document.getElementById('equipmentSearch')?.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();
    const filtered = equipmentsData.filter(eq => 
        eq.name.toLowerCase().includes(searchTerm) || 
        eq.category.toLowerCase().includes(searchTerm)
    );
    renderEquipmentGrid(filtered);
});

// Contador de caracteres
document.getElementById('modal_description')?.addEventListener('input', function() {
    const charCount = this.value.length;
    document.getElementById('char-count').textContent = `${charCount}/500`;
    if (charCount > 500) {
        this.value = this.value.substring(0, 500);
        document.getElementById('char-count').textContent = '500/500';
    }
});

// Enviar formulario
async function submitRequestForm() {
    const form = document.getElementById('requestForm');
    const formData = new FormData(form);
    
    // Validaciones
    if (!formData.get('productive_unit_warehouse_id')) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debe seleccionar una unidad productiva/almacén.',
        });
        return;
    }
    
    if (selectedEquipments.size === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Debe seleccionar al menos un insumo para la solicitud.',
        });
        return;
    }
    
    // Validar cantidades
    const amountInputs = document.querySelectorAll('input[name*="[amount]"]');
    let hasErrors = false;
    amountInputs.forEach(input => {
        const maxAmount = parseInt(input.max);
        const currentAmount = parseInt(input.value);
        if (currentAmount > maxAmount || currentAmount < 1) {
            hasErrors = true;
        }
    });
    
    if (hasErrors) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Por favor, corrija las cantidades antes de enviar.',
        });
        return;
    }
    
    // Mostrar loading
    Swal.fire({
        title: 'Enviando solicitud...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        const contentType = response.headers.get('content-type');
        let result;
        
        if (contentType && contentType.includes('application/json')) {
            result = await response.json();
        } else {
            // Si la respuesta es HTML (redirect), significa que hubo un error
            throw new Error('Error al procesar la solicitud. Por favor, inténtalo de nuevo.');
        }
        
        if (response.ok && result.success) {
            Swal.fire({
                icon: 'success',
                title: '¡Solicitud enviada!',
                text: result.message || 'Tu solicitud ha sido registrada exitosamente.',
                timer: 3000,
                showConfirmButton: false
            }).then(() => {
                closeRequestModal();
                window.location.reload();
            });
        } else {
            throw new Error(result.error || result.message || 'Error al enviar la solicitud');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Hubo un problema al enviar la solicitud. Por favor, inténtalo de nuevo.',
        });
    }
}

// Resetear formulario
function resetRequestForm() {
    document.getElementById('requestForm').reset();
    document.getElementById('equipment-list').innerHTML = `
        <div id="no-equipment-message" class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
            <i class="fas fa-box text-gray-400 text-4xl mb-4"></i>
            <h4 class="text-lg font-medium text-gray-900 mb-2">No hay insumos seleccionados</h4>
            <p class="text-gray-500 mb-4">Haga clic en "Agregar Insumo" para comenzar</p>
        </div>
    `;
    selectedEquipments.clear();
    document.getElementById('char-count').textContent = '0/500';
}

// Cerrar modales al hacer clic fuera
document.getElementById('requestModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRequestModal();
    }
});

document.getElementById('equipmentModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeEquipmentModal();
    }
});
</script>
@endsection

