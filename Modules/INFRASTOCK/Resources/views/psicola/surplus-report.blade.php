@extends('infrastock::layouts.usuarios-master')

@section('title', 'Registro de Sobrantes')

@section('breadcrumb-items')
<li class="flex items-center">
    <span class="text-gray-500">Sobrantes</span>
</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Mensajes de Éxito/Error -->
    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-check-circle mr-3"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg flex items-center">
            <i class="fas fa-exclamation-circle mr-3"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Header de la página -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Registro de Sobrantes</h1>
                <p class="text-gray-600">Registra los insumos que han sobrado y especifica la causa</p>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-boxes text-green-500 text-2xl"></i>
                <span class="text-sm text-gray-500">Control de inventario</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Formulario de Registro de Sobrantes -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center mb-6">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-plus text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Registrar Sobrante</h3>
                    <p class="text-gray-600">Completa el formulario para registrar un insumo sobrante</p>
                    <p class="text-xs text-orange-600 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Solo puedes registrar sobrantes de herramientas e insumos generales (no de aseo)
                    </p>
                </div>
            </div>

            <form action="{{ route('infrastock.psicola.surplus.store') }}" method="POST" class="space-y-6" id="surplusForm">
                @csrf
                
                <!-- Selección de Solicitud Entregada (si aplica) -->
                @if(isset($deliveredRequests) && $deliveredRequests->count() > 0)
                <div>
                    <label for="request_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-clipboard-list mr-2 text-green-500"></i>
                        Solicitud Entregada (Opcional)
                    </label>
                    <select name="request_id" id="request_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200">
                        <option value="">-- Selecciona una solicitud entregada --</option>
                        @foreach($deliveredRequests as $deliveredRequest)
                            <option value="{{ $deliveredRequest->id }}" {{ old('request_id') == $deliveredRequest->id ? 'selected' : '' }}>
                                Solicitud #{{ $deliveredRequest->id }} - {{ $deliveredRequest->created_at->format('d/m/Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Selección de Insumo -->
                <div>
                    <label for="equipment_id" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-box mr-2 text-green-500"></i>
                        Seleccionar Insumo *
                    </label>
                    <select name="equipment_id" id="equipment_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('equipment_id') border-red-500 @enderror">
                        <option value="">-- Selecciona un insumo --</option>
                        @foreach($equipments as $equipment)
                            <option value="{{ $equipment->id }}" 
                                    @if(old('equipment_id') == $equipment->id) selected @endif
                                    data-category="{{ $equipment->category->name ?? 'Sin categoría' }}"
                                    data-unit="{{ $equipment->unit ?? 'unidades' }}">
                                {{ $equipment->name }} 
                                @if($equipment->category)
                                    - {{ $equipment->category->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('equipment_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Cantidad Sobrante -->
                <div>
                    <label for="surplus_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hashtag mr-2 text-green-500"></i>
                        Cantidad que Sobró *
                    </label>
                    <div class="relative">
                        <input type="number" name="surplus_amount" id="surplus_amount" required min="1"
                               value="{{ old('surplus_amount') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('surplus_amount') border-red-500 @enderror"
                               placeholder="Ingresa la cantidad que sobró">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 text-sm" id="unit-display">unidades</span>
                        </div>
                    </div>
                    @error('surplus_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha del Sobrante -->
                <div>
                    <label for="surplus_date" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar mr-2 text-green-500"></i>
                        Fecha del Sobrante *
                    </label>
                    <input type="date" name="surplus_date" id="surplus_date" required
                           value="{{ old('surplus_date', date('Y-m-d')) }}"
                           max="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('surplus_date') border-red-500 @enderror">
                    @error('surplus_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Causa del Sobrante -->
                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment-alt mr-2 text-green-500"></i>
                        Causa del Sobrante *
                    </label>
                    <textarea name="reason" id="reason" required rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('reason') border-red-500 @enderror"
                              placeholder="Describe la causa por la cual sobró este insumo...">{{ old('reason') }}</textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-500">Máximo 500 caracteres</p>
                        <span class="text-xs text-gray-400" id="char-count">0/500</span>
                    </div>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="reset" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-undo mr-2"></i>
                        Limpiar
                    </button>
                    <button type="submit" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Registrar Sobrante
                    </button>
                </div>
            </form>
        </div>

        <!-- Historial de Sobrantes -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-history text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Historial de Sobrantes</h3>
                        <p class="text-gray-600">Últimos registros de sobrantes</p>
                    </div>
                </div>
                <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                    {{ $surpluses->total() }} registros
                </span>
            </div>

            @if($surpluses->count() > 0)
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @foreach($surpluses as $surplus)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <div class="bg-orange-100 p-2 rounded-full">
                                            <i class="fas fa-box text-orange-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $surplus->equipment->name }}</h4>
                                            <p class="text-sm text-gray-500">{{ $surplus->equipment->category->name ?? 'Sin categoría' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-2 gap-4 mb-3">
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Cantidad:</span>
                                            <span class="ml-2 bg-orange-100 text-orange-800 text-sm px-2 py-1 rounded-full">
                                                {{ $surplus->surplus_amount }} {{ $surplus->equipment->unit ?? 'unidades' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-700">Fecha:</span>
                                            <span class="ml-2 text-sm text-gray-600">{{ $surplus->surplus_date->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="text-sm font-medium text-gray-700">Causa:</span>
                                        <p class="text-sm text-gray-600 mt-1">{{ $surplus->reason }}</p>
                                    </div>
                                    
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-400">
                                            Registrado el {{ $surplus->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <div class="flex space-x-2">
                                            <button onclick="showSurplusDetails({{ $surplus->id }})" 
                                                    class="text-blue-600 hover:text-blue-800 text-sm">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="deleteSurplus({{ $surplus->id }})" 
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                @if($surpluses->hasPages())
                    <div class="mt-6 flex justify-center">
                        {{ $surpluses->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-8">
                    <i class="fas fa-boxes text-gray-400 text-4xl mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">No hay sobrantes registrados</h4>
                    <p class="text-gray-500">Comienza registrando tu primer sobrante usando el formulario.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para ver detalles del sobrante -->
<div id="surplusDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Detalles del Sobrante</h3>
                <button onclick="closeSurplusDetailsModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="surplusDetailsContent">
                <!-- Contenido se carga dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar unidad cuando se selecciona un insumo
    const equipmentSelect = document.getElementById('equipment_id');
    if (equipmentSelect) {
        equipmentSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const unit = selectedOption.getAttribute('data-unit') || 'unidades';
            const unitDisplay = document.getElementById('unit-display');
            if (unitDisplay) {
                unitDisplay.textContent = unit;
            }
        });
    }

    // Contador de caracteres para el textarea
    const reasonTextarea = document.getElementById('reason');
    if (reasonTextarea) {
        const initialCount = reasonTextarea.value.length;
        const charCountDisplay = document.getElementById('char-count');
        if (charCountDisplay) {
            charCountDisplay.textContent = initialCount + '/500';
        }
        
        reasonTextarea.addEventListener('input', function() {
            const charCount = this.value.length;
            if (charCountDisplay) {
                charCountDisplay.textContent = charCount + '/500';
            }
            
            if (charCount > 500) {
                this.value = this.value.substring(0, 500);
                if (charCountDisplay) {
                    charCountDisplay.textContent = '500/500';
                }
            }
        });
    }

    // Validar formulario antes de enviar
    const surplusForm = document.getElementById('surplusForm');
    if (surplusForm) {
        surplusForm.addEventListener('submit', function(e) {
            const equipmentId = document.getElementById('equipment_id');
            const surplusAmount = document.getElementById('surplus_amount');
            const reason = document.getElementById('reason');
            const surplusDate = document.getElementById('surplus_date');
            
            if (!equipmentId || !equipmentId.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe seleccionar un insumo.',
                });
                return false;
            }
            
            if (!surplusAmount || !surplusAmount.value || parseInt(surplusAmount.value) < 1) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe especificar una cantidad válida mayor a 0.',
                });
                return false;
            }
            
            if (!reason || !reason.value.trim()) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe especificar la causa del sobrante.',
                });
                return false;
            }
            
            if (!surplusDate || !surplusDate.value) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe seleccionar una fecha.',
                });
                return false;
            }
        });
    }
});

// Mostrar detalles del sobrante
function showSurplusDetails(surplusId) {
    fetch(`{{ route('infrastock.psicola.surplus.show', '') }}/${surplusId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            document.getElementById('surplusDetailsContent').innerHTML = `
                <div class="space-y-4">
                    <div>
                        <span class="font-medium text-gray-700">Insumo:</span>
                        <p class="text-gray-900">${data.equipment_name}</p>
                        <p class="text-sm text-gray-500">${data.equipment_category}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Cantidad:</span>
                        <p class="text-gray-900">${data.surplus_amount} ${data.unit}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Fecha:</span>
                        <p class="text-gray-900">${data.surplus_date}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Causa:</span>
                        <p class="text-gray-900">${data.reason}</p>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Registrado:</span>
                        <p class="text-gray-900">${data.created_at}</p>
                    </div>
                </div>
            `;
            
            document.getElementById('surplusDetailsModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los detalles del sobrante');
        });
}

// Cerrar modal de detalles
function closeSurplusDetailsModal() {
    document.getElementById('surplusDetailsModal').classList.add('hidden');
}

// Eliminar sobrante
function deleteSurplus(surplusId) {
    if (confirm('¿Estás seguro de que quieres eliminar este registro de sobrante?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('infrastock.psicola.surplus.destroy', '') }}/${surplusId}`;
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        const tokenField = document.createElement('input');
        tokenField.type = 'hidden';
        tokenField.name = '_token';
        tokenField.value = '{{ csrf_token() }}';
        
        form.appendChild(methodField);
        form.appendChild(tokenField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection

