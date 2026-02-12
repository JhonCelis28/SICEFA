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
                               value="{{ request('search') }}"
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
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $areas->firstItem() ?? 0 }} - {{ $areas->lastItem() ?? 0 }} de {{ $areas->total() }} registros
                        </div>
                    </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Descripción</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($areas as $area)
                                <tr class="hover:bg-gray-100 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $area->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $area->description }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openEditModal({{ $area->id }}, '{{ addslashes($area->name) }}', '{{ addslashes($area->description) }}')" class="text-yellow-600 hover:text-yellow-900 mr-3" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form id="delete-area-{{ $area->id }}" method="POST" action="{{ route('infrastock.admin.areas.destroy', $area->id) }}" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $area->id }}, '{{ addslashes($area->name) }}')" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                    <i class="fas fa-trash-alt"></i>
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
@if($errors->hasAny(['name', 'description']) && old('_token'))
    document.addEventListener('DOMContentLoaded', function() {
        const alpineComponent = document.querySelector('[x-data]');
        if (alpineComponent && alpineComponent._x_dataStack) {
            alpineComponent._x_dataStack[0].isCreateModalOpen = true;
        }
    });
@endif

// Función para confirmar eliminación con SweetAlert2
function confirmDelete(areaId, areaName) {
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
            Swal.fire({
                title: 'Eliminando...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            document.getElementById('delete-area-' + areaId).submit();
        }
    });
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

// Búsqueda server-side con debounce
function setupAutoFilter() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    let debounceTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            serverSearch(this.value);
        }, 500);
    });
    
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(debounceTimer);
            serverSearch(this.value);
        }
    });
}

function serverSearch(term) {
    const url = new URL(window.location.href);
    if (term && term.trim() !== '') {
        url.searchParams.set('search', term.trim());
    } else {
        url.searchParams.delete('search');
    }
    url.searchParams.delete('page');
    window.location.href = url.toString();
}

function clearSearch() {
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    url.searchParams.delete('page');
    window.location.href = url.toString();
}
</script>
@endsection