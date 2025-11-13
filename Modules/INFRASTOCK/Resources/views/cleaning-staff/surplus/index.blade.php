@extends('infrastock::layouts.cleaning-staff-master')

@section('title', 'Registro de Sobrantes')

@section('breadcrumb-items')
<li class="flex items-center">
    <span class="text-gray-500">Sobrantes</span>
</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header de la página -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Registro de Sobrantes</h1>
                <p class="text-gray-600">Registra la cantidad de insumos que sobraron de las solicitudes aprobadas</p>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-boxes text-green-500 text-2xl"></i>
                <span class="text-sm text-gray-500">Control de inventario</span>
            </div>
        </div>
    </div>

    <!-- Mensajes de éxito/error -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Lista de Solicitudes Aprobadas -->
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <i class="fas fa-clipboard-check text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Solicitudes Aprobadas</h3>
                    <p class="text-gray-600">Selecciona una solicitud para registrar los sobrantes</p>
                </div>
            </div>
            <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                {{ $surpluses->total() }} registros
            </span>
        </div>

        @if($surpluses->count() > 0)
            <div class="space-y-4">
                @foreach($surpluses as $surplus)
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="bg-green-100 p-2 rounded-full">
                                        <i class="fas fa-box text-green-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $surplus->equipment->name }}</h4>
                                        <p class="text-sm text-gray-500">{{ $surplus->equipment->category->name ?? 'Sin categoría' }}</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-3">
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Cantidad Solicitada:</span>
                                        <p class="text-sm text-gray-900 font-semibold">
                                            {{ $surplus->requestItem->requested_amount ?? 'N/A' }} {{ $surplus->equipment->unit ?? 'unidades' }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Cantidad Sobrante:</span>
                                        <p class="text-sm text-gray-900 font-semibold">
                                            {{ $surplus->surplus_amount }} {{ $surplus->equipment->unit ?? 'unidades' }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">ID Solicitud:</span>
                                        <p class="text-sm text-gray-900 font-semibold">#{{ $surplus->request_id }}</p>
                                    </div>
                                </div>

                                @if($surplus->description)
                                    <div class="mb-3">
                                        <span class="text-sm font-medium text-gray-700">Descripción:</span>
                                        <p class="text-sm text-gray-600 mt-1">{{ $surplus->description }}</p>
                                    </div>
                                @endif
                                
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-400">
                                        Aprobada el {{ $surplus->created_at->format('d/m/Y H:i') }}
                                    </span>
                                    <div class="flex space-x-2">
                                        <button onclick="openEditModal({{ $surplus->id }})" 
                                                class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 text-sm">
                                            <i class="fas fa-edit mr-1"></i>
                                            {{ $surplus->surplus_amount > 0 ? 'Editar' : 'Registrar' }}
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
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-gray-400 text-4xl mb-4"></i>
                <h4 class="text-lg font-medium text-gray-900 mb-2">No hay solicitudes aprobadas</h4>
                <p class="text-gray-500">Las solicitudes aprobadas aparecerán aquí para que puedas registrar los sobrantes.</p>
            </div>
        @endif
    </div>
</div>

<!-- Modal para editar sobrante -->
<div id="editSurplusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Registrar Sobrante</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="editSurplusForm" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                
                <div id="surplusInfo" class="bg-gray-50 p-4 rounded-lg mb-4">
                    <!-- Información se carga dinámicamente -->
                </div>

                <div>
                    <label for="edit_surplus_amount" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hashtag mr-2 text-green-500"></i>
                        Cantidad que Sobró *
                    </label>
                    <div class="relative">
                        <input type="number" name="surplus_amount" id="edit_surplus_amount" required min="1"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                               placeholder="Ingresa la cantidad que sobró">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 text-sm" id="edit_unit_display">unidades</span>
                        </div>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">La cantidad debe ser mayor a 0 y menor a la cantidad solicitada</p>
                    <p class="mt-1 text-sm text-red-600 hidden" id="amount_error"></p>
                </div>

                <div>
                    <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment-alt mr-2 text-green-500"></i>
                        Descripción *
                    </label>
                    <textarea name="description" id="edit_description" required rows="4"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                              placeholder="Describe los detalles del sobrante..."></textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-xs text-gray-500">Máximo 1000 caracteres</p>
                        <span class="text-xs text-gray-400" id="char-count-edit">0/1000</span>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <button type="button" onclick="closeEditModal()" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentMaxAmount = 0;

// Abrir modal de edición
function openEditModal(surplusId) {
    fetch(`{{ route('infrastock.cleaning-staff.surplus.show', '') }}/${surplusId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
            
            currentMaxAmount = data.requested_amount;
            
            // Actualizar formulario
            document.getElementById('editSurplusForm').action = `{{ route('infrastock.cleaning-staff.surplus.update', '') }}/${surplusId}`;
            document.getElementById('edit_surplus_amount').value = data.surplus_amount;
            document.getElementById('edit_surplus_amount').max = data.requested_amount;
            document.getElementById('edit_description').value = data.description || '';
            document.getElementById('edit_unit_display').textContent = data.unit;
            
            // Actualizar información
            document.getElementById('surplusInfo').innerHTML = `
                <div class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-box text-green-600"></i>
                        <span class="font-medium text-gray-700">Insumo:</span>
                        <span class="text-gray-900">${data.equipment_name}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-tag text-blue-600"></i>
                        <span class="font-medium text-gray-700">Categoría:</span>
                        <span class="text-gray-900">${data.equipment_category}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-hashtag text-orange-600"></i>
                        <span class="font-medium text-gray-700">Cantidad Solicitada:</span>
                        <span class="text-gray-900 font-semibold">${data.requested_amount} ${data.unit}</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-file-alt text-purple-600"></i>
                        <span class="font-medium text-gray-700">ID Solicitud:</span>
                        <span class="text-gray-900">#${data.request_id}</span>
                    </div>
                </div>
            `;
            
            // Actualizar contador de caracteres
            updateCharCount();
            
            // Mostrar modal
            document.getElementById('editSurplusModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los detalles del sobrante');
        });
}

// Cerrar modal
function closeEditModal() {
    document.getElementById('editSurplusModal').classList.add('hidden');
    document.getElementById('editSurplusForm').reset();
    document.getElementById('amount_error').classList.add('hidden');
}

// Validar cantidad antes de enviar
document.getElementById('editSurplusForm').addEventListener('submit', function(e) {
    const amount = parseInt(document.getElementById('edit_surplus_amount').value) || 0;
    const maxAmount = currentMaxAmount;
    
    if (amount <= 0) {
        e.preventDefault();
        document.getElementById('amount_error').textContent = 'La cantidad debe ser mayor a 0 y menor a la cantidad solicitada.';
        document.getElementById('amount_error').classList.remove('hidden');
        document.getElementById('edit_surplus_amount').classList.add('border-red-500');
        return false;
    }
    
    if (amount > maxAmount) {
        e.preventDefault();
        document.getElementById('amount_error').textContent = `La cantidad debe ser mayor a 0 y menor a la cantidad solicitada (${maxAmount}).`;
        document.getElementById('amount_error').classList.remove('hidden');
        document.getElementById('edit_surplus_amount').classList.add('border-red-500');
        return false;
    }
    
    document.getElementById('amount_error').classList.add('hidden');
    document.getElementById('edit_surplus_amount').classList.remove('border-red-500');
});

// Validar cantidad en tiempo real
document.getElementById('edit_surplus_amount').addEventListener('input', function() {
    const amount = parseInt(this.value) || 0;
    const maxAmount = currentMaxAmount;
    const errorElement = document.getElementById('amount_error');
    
    if (amount <= 0) {
        errorElement.textContent = 'La cantidad debe ser mayor a 0 y menor a la cantidad solicitada.';
        errorElement.classList.remove('hidden');
        this.classList.add('border-red-500');
    } else if (amount > maxAmount) {
        errorElement.textContent = `La cantidad debe ser mayor a 0 y menor a la cantidad solicitada (${maxAmount}).`;
        errorElement.classList.remove('hidden');
        this.classList.add('border-red-500');
    } else {
        errorElement.classList.add('hidden');
        this.classList.remove('border-red-500');
    }
});

// Contador de caracteres para descripción
function updateCharCount() {
    const textarea = document.getElementById('edit_description');
    const charCount = textarea.value.length;
    document.getElementById('char-count-edit').textContent = charCount + '/1000';
    
    if (charCount > 1000) {
        textarea.value = textarea.value.substring(0, 1000);
        document.getElementById('char-count-edit').textContent = '1000/1000';
    }
}

document.getElementById('edit_description').addEventListener('input', updateCharCount);

// Cerrar modal al hacer clic fuera
document.getElementById('editSurplusModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});
</script>
@endsection
