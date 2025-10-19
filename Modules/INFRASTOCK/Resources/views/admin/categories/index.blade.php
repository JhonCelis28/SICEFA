<!--
    * @file index.blade.php
    * @brief Vista para la gestión (CRUD) de Categorías en el módulo INFRASTOCK.
    *
    * Esta vista Blade permite al administrador visualizar, crear, editar y eliminar
    * categorías de insumos y herramientas. Utiliza Tailwind CSS para un diseño moderno y responsive,
    * y Alpine.js para la interactividad de los modales de creación y edición. Estos modales
    * manejan las operaciones de forma asíncrona (AJAX) y muestran notificaciones con SweetAlert2.
    * Extiende la plantilla `master.blade.php` y define el título y los ítems de las migas de pan.
    *
    * @param Modules\INFRASTOCK\Entities\InfrastockCategory[] $categories Colección de categorías existentes.
    * @param Illuminate\Support\ViewErrorBag $errors Objeto que contiene los errores de validación de Laravel.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Gestión de Categorías')

@section('breadcrumb-items')
    <!-- Ítem de migas de pan para "Categorías" -->
    <li class="flex items-center">
        <a href="{{ route('infrastock.admin.categories.index') }}" class="text-green-600 hover:text-green-800">Categorías</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
@endsection

@section('content')
    <!-- Contenedor principal de la vista de gestión de categorías -->
    <div x-data="{
        isCreateModalOpen: false,
        isEditModalOpen: false,
        currentCategory: { id: null, name: '', type: '' },
        
        init() {
            console.log('Alpine.js inicializado correctamente');
        },
        
        openCreateModal() {
            console.log('Abriendo modal de creación');
            this.isCreateModalOpen = true;
        },
        
        openEditModal(id, name, type) {
            console.log('Abriendo modal de edición:', { id, name, type });
            this.isEditModalOpen = true;
            this.currentCategory = { id: id, name: name, type: type };
        },
        
        closeModals() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
        }
    }">
    <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Listado de Categorías</h2>
                <div class="flex space-x-2">
                    <button @click="openCreateModal()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Crear Nueva Categoría
                    </button>
                </div>
            </div>

            <!-- Filtro de búsqueda automático -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Buscar por nombre o tipo..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button onclick="clearSearch()" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>

        <!-- Tabla de Categorías -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Detalles de las Categorías</h3>
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $categories->firstItem() ?? 0 }} - {{ $categories->lastItem() ?? 0 }} de {{ $categories->total() }} registros
                        </div>
                    </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- Encabezados de la tabla -->
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Iteración sobre cada categoría para mostrar sus datos -->
                            @foreach($categories as $category)
                                <tr class="hover:bg-gray-100 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $category->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $category->type === 'supply' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $category->type === 'supply' ? 'Insumo' : 'Herramienta' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <!-- Botón para abrir el modal de edición, pasando los datos de la categoría actual -->
                                        <button @click="openEditModal({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ $category->type }}')" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <!-- Formulario para eliminar una categoría -->
                                        <form method="POST" action="{{ route('infrastock.admin.categories.destroy', $category->id) }}" style="display: inline;" onsubmit="return confirmDeleteSync('{{ addslashes($category->name) }}')">
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
                        <p class="text-gray-500">No hay categorías que coincidan con tu búsqueda</p>
                    </div>
                    
                    <!-- Paginación -->
                    @if($categories->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $categories->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Modal de Creación -->
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Crear Nueva Categoría</h3>
                        <button @click="closeModals()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <!-- Formulario de creación de categoría -->
                    <form method="POST" action="{{ route('infrastock.admin.categories.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre de la Categoría:</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Tipo:</label>
                            <select name="type" id="type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('type') border-red-500 @enderror" required>
                                <option value="">Selecciona un tipo</option>
                                <option value="supply" {{ old('type') === 'supply' ? 'selected' : '' }}>Insumo</option>
                                <option value="tool" {{ old('type') === 'tool' ? 'selected' : '' }}>Herramienta</option>
                            </select>
                            @error('type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="closeModals()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Guardar Categoría</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Categoría</h3>
                        <button @click="closeModals()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <!-- Formulario de edición de categoría -->
                    <form method="POST" :action="'{{ route('infrastock.admin.categories.update', '') }}/' + currentCategory.id">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="edit_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre de la Categoría:</label>
                            <input type="text" name="name" id="edit_name" :value="currentCategory.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-6">
                            <label for="edit_type" class="block text-gray-700 text-sm font-bold mb-2">Tipo:</label>
                            <select name="type" id="edit_type" :value="currentCategory.type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">Selecciona un tipo</option>
                                <option value="supply" :selected="currentCategory.type === 'supply'">Insumo</option>
                                <option value="tool" :selected="currentCategory.type === 'tool'">Herramienta</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="closeModals()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Actualizar Categoría</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
// Función para confirmar eliminación con SweetAlert2 (versión síncrona)
function confirmDeleteSync(categoryName) {
    let confirmed = false;
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Quieres eliminar la categoría "${categoryName}"?`,
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
            text: 'La categoría ha sido eliminada correctamente.',
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
            const typeCell = row.cells[2]; // Columna de tipo
            
            const nameText = nameCell.textContent.toLowerCase();
            const typeText = typeCell.textContent.toLowerCase();
            
            if (nameText.includes(searchTerm) || typeText.includes(searchTerm)) {
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

// Verificar si hay errores de validación y abrir modal automáticamente
@if($errors->hasAny(['name', 'type']) || session('error'))
    document.addEventListener('DOMContentLoaded', function() {
        // Buscar el componente Alpine.js y abrir el modal de creación
        const alpineComponent = document.querySelector('[x-data]');
        if (alpineComponent && alpineComponent._x_dataStack) {
            alpineComponent._x_dataStack[0].isCreateModalOpen = true;
        }
        console.log('Errores encontrados:', @json($errors->messages()));
    });
@endif
</script>
@endsection