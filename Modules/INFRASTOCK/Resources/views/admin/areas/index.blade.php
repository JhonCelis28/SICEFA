<!-- Vista simplificada para probar funcionalidad básica -->
@extends('infrastock::layouts.master')

@section('title', 'Gestión de Áreas Productivas')

@section('content')
    <!-- Componente Alpine.js simplificado -->
    <div x-data="{
        isCreateModalOpen: false,
        isEditModalOpen: false,
        currentArea: { id: null, name: '', description: '' },
        
        init() {
            console.log('Alpine.js inicializado correctamente');
        },
        
        openCreateModal() {
            console.log('Abriendo modal de creación');
            this.isCreateModalOpen = true;
        },
        
        openEditModal(id, name, description) {
            console.log('Abriendo modal de edición:', { id, name, description });
            this.isEditModalOpen = true;
            this.currentArea = { id: id, name: name, description: description };
        },
        
        closeModals() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
        }
    }">
    <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Listado de Áreas Productivas</h2>
                <div class="flex space-x-2">
                    <button @click="openCreateModal()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Crear Nueva Área
                    </button>
                </div>
            </div>

            <!-- Filtro de búsqueda automático -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Buscar por nombre o descripción..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button onclick="clearSearch()" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>

        <!-- Tabla de Áreas Productivas -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Detalles de las Áreas</h3>
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $areas->firstItem() ?? 0 }} - {{ $areas->lastItem() ?? 0 }} de {{ $areas->total() }} registros
                        </div>
                    </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($areas as $area)
                                <tr class="hover:bg-gray-100 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $area->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $area->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $area->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openEditModal({{ $area->id }}, '{{ addslashes($area->name) }}', '{{ addslashes($area->description) }}')" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <form method="POST" action="{{ route('infrastock.admin.areas.destroy', $area->id) }}" style="display: inline;" onsubmit="return confirmDeleteSync('{{ addslashes($area->name) }}')"
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash-alt"></i> Eliminar
                                                </button>
                                            </form>
                                        </td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                    
                    <!-- Mensaje cuando no hay resultados -->
                    <div id="noResultsMessage" class="text-center py-8" style="display: none;">
                        <i class="fas fa-search text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron resultados</h3>
                        <p class="text-gray-500">No hay áreas que coincidan con tu búsqueda</p>
                    </div>
                    
                    <!-- Paginación -->
                    @if($areas->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $areas->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Modal de Creación -->
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Crear Nueva Área</h3>
                        <button @click="isCreateModalOpen = false" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('infrastock.admin.areas.store') }}">
                        @csrf
                        <input type="hidden" name="person_id" value="{{ Auth::id() }}">
                        <input type="hidden" name="sector_id" value="1">
                        <input type="hidden" name="farm_id" value="1">
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Área:</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                            <textarea name="description" id="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isCreateModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Guardar Área</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Área</h3>
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form method="POST" :action="'/infrastock/admin/areas/' + currentArea.id">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="person_id" value="{{ Auth::id() }}">
                        <input type="hidden" name="sector_id" value="1">
                        <input type="hidden" name="farm_id" value="1">
                        <div class="mb-4">
                            <label for="edit_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Área:</label>
                            <input type="text" name="name" id="edit_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" x-model="currentArea.name" required>
                        </div>
                        <div class="mb-6">
                            <label for="edit_description" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                            <textarea name="description" id="edit_description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" x-model="currentArea.description"></textarea>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isEditModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Actualizar Área</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
console.log('Script cargado correctamente');

// Verificar si hay errores de validación y abrir modal automáticamente
@if($errors->hasAny(['name', 'description']) || session('error'))
    document.addEventListener('DOMContentLoaded', function() {
        // Buscar el componente Alpine.js y abrir el modal de creación
        const alpineComponent = document.querySelector('[x-data]');
        if (alpineComponent && alpineComponent._x_dataStack) {
            alpineComponent._x_dataStack[0].isCreateModalOpen = true;
        }
        console.log('Errores encontrados:', @json($errors->messages()));
    });
@endif

// Función para confirmar eliminación con SweetAlert2 (versión síncrona)
function confirmDeleteSync(areaName) {
    let confirmed = false;
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Quieres eliminar el área "${areaName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Permitir que el formulario se envíe
            confirmed = true;
            // Enviar el formulario manualmente
            event.target.submit();
        }
    });
    
    // Retornar false para prevenir el envío inmediato del formulario
    return false;
}

// Verificar si hay mensajes de sesión
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success') === 'deleted')
        Swal.fire({
            icon: 'success',
            title: '¡Eliminado!',
            text: 'El área ha sido eliminada correctamente.',
            showConfirmButton: false,
            timer: 1500
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonText: 'Entendido'
        });
    @endif
    
    // Configurar filtro automático
    setupAutoFilter();
});

// Función para configurar el filtro automático
function setupAutoFilter() {
    const searchInput = document.getElementById('searchInput');
    const table = document.querySelector('table tbody');
    const rows = table.querySelectorAll('tr');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        rows.forEach(row => {
            const nameCell = row.cells[1]; // Columna de nombre
            const descriptionCell = row.cells[2]; // Columna de descripción
            
            const nameText = nameCell.textContent.toLowerCase();
            const descriptionText = descriptionCell.textContent.toLowerCase();
            
            if (nameText.includes(searchTerm) || descriptionText.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        // Actualizar contador de resultados visibles
        updateVisibleCount();
    });
}

// Función para limpiar la búsqueda
function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    searchInput.value = '';
    
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach(row => {
        row.style.display = '';
    });
    
    updateVisibleCount();
}

// Función para actualizar el contador de resultados visibles
function updateVisibleCount() {
    const visibleRows = document.querySelectorAll('table tbody tr:not([style*="display: none"])');
    const totalRows = document.querySelectorAll('table tbody tr').length;
    const noResultsMessage = document.getElementById('noResultsMessage');
    
    const counterElement = document.querySelector('.text-sm.text-gray-500');
    if (counterElement) {
        if (document.getElementById('searchInput').value) {
            counterElement.textContent = `Mostrando ${visibleRows.length} de ${totalRows} registros (filtrados)`;
        } else {
            counterElement.textContent = `Mostrando ${visibleRows.length} de ${totalRows} registros`;
        }
    }
    
    // Mostrar/ocultar mensaje de "no hay resultados"
    if (visibleRows.length === 0 && document.getElementById('searchInput').value) {
        noResultsMessage.style.display = 'block';
    } else {
        noResultsMessage.style.display = 'none';
    }
}
</script>
@endsection