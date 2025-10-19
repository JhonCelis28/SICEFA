<!--
    * @file index.blade.php
    * @brief Vista para la gestión (CRUD) de Insumos en el módulo INFRASTOCK.
    *
    * Esta vista Blade permite al administrador visualizar, registrar, editar y eliminar
    * insumos. Utiliza Tailwind CSS para un diseño moderno y responsive, y Alpine.js para
    * la interactividad de los modales de creación y edición. Estos modales manejan las
    * operaciones de forma asíncrona (AJAX) y muestran notificaciones con SweetAlert2.
    * Extiende la plantilla `master.blade.php` y define el título y los ítems de las migas de pan.
    *
    * @param Modules\INFRASTOCK\Entities\Equipment[] $supplies Colección de insumos existentes.
    * @param Modules\INFRASTOCK\Entities\InfrastockCategory[] $categories Colección de categorías de insumos.
    * @param Modules\INFRASTOCK\Entities\Labor[] $labors Colección de labores para asociar a los insumos.
    * @param Modules\INFRASTOCK\Entities\Inventory[] $inventories Colección de inventarios disponibles.
    * @param Illuminate\Support\ViewErrorBag $errors Objeto que contiene los errores de validación de Laravel.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Gestión de Insumos')

@section('breadcrumb-items')
    <!-- Ítem de migas de pan para "Insumos" -->
    <li class="flex items-center">
        <a href="{{ route('infrastock.admin.supplies.index') }}" class="text-green-600 hover:text-green-800">Insumos</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
@endsection

@section('content')
    <!-- Contenedor principal de la vista de gestión de insumos -->
    <div x-data="{
        isCreateModalOpen: false,
        isEditModalOpen: false,
        currentSupply: { id: null, inventory_id: '', name: '', labor_id: '', amount: '', price: '', category_id: '' },
        
        init() {
            console.log('Alpine.js inicializado correctamente');
        },
        
        openCreateModal() {
            console.log('Abriendo modal de creación');
            this.isCreateModalOpen = true;
        },
        
        openEditModal(id, inventory_id, name, labor_id, amount, price, category_id) {
            console.log('Abriendo modal de edición:', { id, inventory_id, name, labor_id, amount, price, category_id });
            this.isEditModalOpen = true;
            this.currentSupply = { id: id, inventory_id: inventory_id, name: name, labor_id: labor_id, amount: amount, price: price, category_id: category_id };
        },
        
        closeModals() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
        }
    }">
    <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Listado de Insumos</h2>
                <div class="flex space-x-2">
                    <button @click="openCreateModal()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        Registrar Nuevo Insumo
                    </button>
                </div>
            </div>

            <!-- Filtro de búsqueda automático -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Buscar por nombre, inventario, labor o categoría..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button onclick="clearSearch()" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>

        <!-- Tabla de Insumos -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Detalles de los Insumos</h3>
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $supplies->firstItem() ?? 0 }} - {{ $supplies->lastItem() ?? 0 }} de {{ $supplies->total() }} registros
                        </div>
                    </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- Encabezados de la tabla -->
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inventario</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Labor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Iteración sobre cada insumo para mostrar sus datos -->
                            @foreach($supplies as $supply)
                                <tr class="hover:bg-gray-100 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $supply->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            ID: {{ $supply->inventory->id }}
                                        </span>
                                        <div class="text-xs text-gray-400 mt-1">{{ $supply->inventory->description ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supply->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $supply->labor->description ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $supply->amount }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            ${{ number_format($supply->price, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                            {{ $supply->category->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <!-- Botón para abrir el modal de edición, pasando los datos del insumo actual -->
                                        <button @click="openEditModal({{ $supply->id }}, {{ $supply->inventory->id }}, '{{ addslashes($supply->name) }}', {{ $supply->labor->id }}, {{ $supply->amount }}, {{ $supply->price }}, {{ $supply->category->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <!-- Formulario para eliminar un insumo -->
                                        <form method="POST" action="{{ route('infrastock.admin.supplies.destroy', $supply->id) }}" style="display: inline;" onsubmit="return confirmDeleteSync('{{ addslashes($supply->name) }}')">
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
                        <p class="text-gray-500">No hay insumos que coincidan con tu búsqueda</p>
                    </div>
                    
                    <!-- Paginación -->
                    @if($supplies->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $supplies->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Modal de Creación -->
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Registrar Nuevo Insumo</h3>
                        <button @click="closeModals()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <!-- Formulario de creación de insumo -->
                    <form method="POST" action="{{ route('infrastock.admin.supplies.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="inventory_id" class="block text-gray-700 text-sm font-bold mb-2">Inventario:</label>
                            <select name="inventory_id" id="inventory_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('inventory_id') border-red-500 @enderror" required>
                                <option value="">Seleccione un inventario</option>
                                @foreach($inventories as $inventory)
                                    <option value="{{ $inventory->id }}" {{ old('inventory_id') == $inventory->id ? 'selected' : '' }}>ID: {{ $inventory->id }} - {{ $inventory->description ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                            @error('inventory_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Insumo:</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="labor_id" class="block text-gray-700 text-sm font-bold mb-2">Labor:</label>
                            <select name="labor_id" id="labor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('labor_id') border-red-500 @enderror" required>
                                <option value="">Seleccione una labor</option>
                                @foreach($labors as $labor)
                                    <option value="{{ $labor->id }}" {{ old('labor_id') == $labor->id ? 'selected' : '' }}>{{ $labor->description ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                            @error('labor_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad:</label>
                            <input type="number" name="amount" id="amount" value="{{ old('amount') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('amount') border-red-500 @enderror" required>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="price" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('price') border-red-500 @enderror" required>
                            @error('price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                            <select name="category_id" id="category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category_id') border-red-500 @enderror" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="closeModals()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Guardar Insumo</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Insumo</h3>
                        <button @click="closeModals()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <!-- Formulario de edición de insumo -->
                    <form method="POST" :action="'{{ route('infrastock.admin.supplies.update', '') }}/' + currentSupply.id">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="edit_inventory_id" class="block text-gray-700 text-sm font-bold mb-2">Inventario:</label>
                            <select name="inventory_id" id="edit_inventory_id" :value="currentSupply.inventory_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">Seleccione un inventario</option>
                                @foreach($inventories as $inventory)
                                    <option value="{{ $inventory->id }}" :selected="currentSupply.inventory_id == {{ $inventory->id }}">ID: {{ $inventory->id }} - {{ $inventory->description ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Insumo:</label>
                            <input type="text" name="name" id="edit_name" :value="currentSupply.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_labor_id" class="block text-gray-700 text-sm font-bold mb-2">Labor:</label>
                            <select name="labor_id" id="edit_labor_id" :value="currentSupply.labor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">Seleccione una labor</option>
                                @foreach($labors as $labor)
                                    <option value="{{ $labor->id }}" :selected="currentSupply.labor_id == {{ $labor->id }}">{{ $labor->description ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad:</label>
                            <input type="number" name="amount" id="edit_amount" :value="currentSupply.amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_price" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                            <input type="number" step="0.01" name="price" id="edit_price" :value="currentSupply.price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-6">
                            <label for="edit_category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                            <select name="category_id" id="edit_category_id" :value="currentSupply.category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" :selected="currentSupply.category_id == {{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="closeModals()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Actualizar Insumo</button>
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
function confirmDeleteSync(supplyName) {
    let confirmed = false;
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Quieres eliminar el insumo "${supplyName}"?`,
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
            text: 'El insumo ha sido eliminado correctamente.',
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
            const inventoryCell = row.cells[1]; // Columna de inventario
            const nameCell = row.cells[2]; // Columna de nombre
            const laborCell = row.cells[3]; // Columna de labor
            const categoryCell = row.cells[6]; // Columna de categoría
            
            const inventoryText = inventoryCell.textContent.toLowerCase();
            const nameText = nameCell.textContent.toLowerCase();
            const laborText = laborCell.textContent.toLowerCase();
            const categoryText = categoryCell.textContent.toLowerCase();
            
            if (inventoryText.includes(searchTerm) || nameText.includes(searchTerm) || laborText.includes(searchTerm) || categoryText.includes(searchTerm)) {
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
@if($errors->hasAny(['inventory_id', 'name', 'labor_id', 'amount', 'price', 'category_id']) || session('error'))
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