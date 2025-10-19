<!--
    * @file index.blade.php
    * @brief Vista para la gestión (CRUD) de Herramientas en el módulo INFRASTOCK.
    *
    * Esta vista Blade permite al administrador visualizar, registrar, editar y eliminar
    * herramientas. Utiliza Tailwind CSS para un diseño moderno y responsive, y Alpine.js para
    * la interactividad de los modales de creación y edición. Estos modales manejan las
    * operaciones de forma asíncrona (AJAX) y muestran notificaciones con SweetAlert2.
    * Extiende la plantilla `master.blade.php` y define el título y los ítems de las migas de pan.
    *
    * @param Modules\INFRASTOCK\Entities\Tool[] $tools Colección de herramientas existentes.
    * @param Modules\INFRASTOCK\Entities\InfrastockCategory[] $categories Colección de categorías de herramientas.
    * @param Modules\INFRASTOCK\Entities\Labor[] $labors Colección de labores para asociar a las herramientas.
    * @param Modules\INFRASTOCK\Entities\Inventory[] $inventories Colección de inventarios disponibles.
    * @param Illuminate\Support\ViewErrorBag $errors Objeto que contiene los errores de validación de Laravel.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Gestión de Herramientas')

@section('breadcrumb-items')
    <!-- Ítem de migas de pan para "Herramientas" -->
    <li class="flex items-center">
        <a href="{{ route('infrastock.admin.tools.index') }}" class="text-green-600 hover:text-green-800">Herramientas</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
@endsection

@section('content')
    <!-- Contenedor principal de la vista de gestión de herramientas -->
    <div x-data="{
        isCreateModalOpen: false,
        isEditModalOpen: false,
        currentTool: { id: null, inventory_id: '', labor_id: '', amount: '', price: '', category_id: '' },
        validationErrors: {},
        createForm: { inventory_id: '', labor_id: '', amount: '', price: '', category_id: '' },

        init() {
            @if($errors->any() || session('error'))
                document.addEventListener('DOMContentLoaded', () => {
                    this.isCreateModalOpen = true;
                    this.validationErrors = @json($errors->messages());
                    const oldData = @json(old());
                    this.createForm.inventory_id = oldData.inventory_id || '';
                    this.createForm.labor_id = oldData.labor_id || '';
                    this.createForm.amount = oldData.amount || '';
                    this.createForm.price = oldData.price || '';
                    this.createForm.category_id = oldData.category_id || '';
                    if ('{{ session('error') }}') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: '{{ session('error') }}',
                            confirmButtonText: 'Entendido'
                        });
                    }
                });
            @endif
        },

        openCreateModal() {
            this.isCreateModalOpen = true;
            this.resetCreateForm();
        },

        openEditModal(id, inventory_id, labor_id, amount, price, category_id) {
            this.isEditModalOpen = true;
            this.currentTool = { id: id, inventory_id: inventory_id, labor_id: labor_id, amount: amount, price: price, category_id: category_id };
            this.validationErrors = {};
        },

        closeModals() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
            this.validationErrors = {};
        },

        resetCreateForm() {
            this.createForm = { inventory_id: '', labor_id: '', amount: '', price: '', category_id: '' };
            this.validationErrors = {};
        }
    }">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Listado de Herramientas</h2>
                <button @click="openCreateModal()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    Registrar Nueva Herramienta
                </button>
            </div>

            <!-- Filtro de búsqueda automático -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Buscar por inventario, labor o categoría..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button onclick="clearSearch()" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>

            <!-- Tabla de Herramientas -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Detalles de las Herramientas</h3>
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $tools->firstItem() ?? 0 }} - {{ $tools->lastItem() ?? 0 }} de {{ $tools->total() }} registros
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inventario</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Labor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($tools as $tool)
                                    <tr class="hover:bg-gray-100 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tool->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                ID: {{ $tool->inventory->id }} - {{ $tool->inventory->description ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->labor->description ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $tool->amount }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                ${{ number_format($tool->price, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                {{ $tool->category->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openEditModal({{ $tool->id }}, {{ $tool->inventory->id }}, {{ $tool->labor->id }}, {{ $tool->amount }}, {{ $tool->price }}, {{ $tool->category->id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <form method="POST" action="{{ route('infrastock.admin.tools.destroy', $tool->id) }}" style="display: inline;" onsubmit="return confirmDeleteSync('{{ addslashes($tool->labor->description ?? 'Herramienta') }}')">
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
                        <p class="text-gray-500">No hay herramientas que coincidan con tu búsqueda</p>
                    </div>
                    
                    <!-- Paginación -->
                    @if($tools->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $tools->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Modal de Creación de Herramienta -->
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div @click.away="isCreateModalOpen = false; resetCreateForm();" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Registrar Nueva Herramienta</h3>
                        <button @click="isCreateModalOpen = false; resetCreateForm();" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" action="{{ route('infrastock.admin.tools.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="create_inventory_id" class="block text-gray-700 text-sm font-bold mb-2">Inventario:</label>
                            <select name="inventory_id" id="create_inventory_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('inventory_id') border-red-500 @enderror" required>
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
                            <label for="create_labor_id" class="block text-gray-700 text-sm font-bold mb-2">Labor:</label>
                            <select name="labor_id" id="create_labor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('labor_id') border-red-500 @enderror" required>
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
                            <label for="create_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad:</label>
                            <input type="number" name="amount" id="create_amount" value="{{ old('amount') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('amount') border-red-500 @enderror" required>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="create_price" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                            <input type="number" step="0.01" name="price" id="create_price" value="{{ old('price') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('price') border-red-500 @enderror" required>
                            @error('price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="create_category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                            <select name="category_id" id="create_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category_id') border-red-500 @enderror" required>
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
                            <button type="button" @click="isCreateModalOpen = false; resetCreateForm();" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Guardar Herramienta</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición de Herramienta -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div @click.away="isEditModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Herramienta</h3>
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" :action="`{{ route('infrastock.admin.tools.update', '') }}/${currentTool.id}`">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="edit_inventory_id" class="block text-gray-700 text-sm font-bold mb-2">Inventario:</label>
                            <select name="inventory_id" id="edit_inventory_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('inventory_id') border-red-500 @enderror" x-model="currentTool.inventory_id" required>
                                <option value="">Seleccione un inventario</option>
                                @foreach($inventories as $inventory)
                                    <option value="{{ $inventory->id }}">{{ $inventory->id }} - {{ $inventory->description ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                            @error('inventory_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_labor_id" class="block text-gray-700 text-sm font-bold mb-2">Labor:</label>
                            <select name="labor_id" id="edit_labor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('labor_id') border-red-500 @enderror" x-model="currentTool.labor_id" required>
                                <option value="">Seleccione una labor</option>
                                @foreach($labors as $labor)
                                    <option value="{{ $labor->id }}">{{ $labor->description ?? 'N/A' }}</option>
                                @endforeach
                            </select>
                            @error('labor_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad:</label>
                            <input type="number" name="amount" id="edit_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('amount') border-red-500 @enderror" x-model="currentTool.amount" required>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_price" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                            <input type="number" step="0.01" name="price" id="edit_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('price') border-red-500 @enderror" x-model="currentTool.price" required>
                            @error('price')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="edit_category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                            <select name="category_id" id="edit_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category_id') border-red-500 @enderror" x-model="currentTool.category_id" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isEditModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Actualizar Herramienta</button>
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
function confirmDeleteSync(toolName) {
    let confirmed = false;
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Quieres eliminar la herramienta "${toolName}"?`,
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
            text: 'La herramienta ha sido eliminada correctamente.',
            showConfirmButton: false,
            timer: 1500
        });
    @endif
    
    @if(session('error') && session('error') !== 'Ya existe una herramienta con estos datos. Por favor, verifica la información.')
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
            // Columnas a buscar: Inventario (col 1), Labor (col 2), Categoría (col 5)
            const inventoryCell = row.cells[1];
            const laborCell = row.cells[2];
            const categoryCell = row.cells[5];
            
            const inventoryText = inventoryCell.textContent.toLowerCase();
            const laborText = laborCell.textContent.toLowerCase();
            const categoryText = categoryCell.textContent.toLowerCase();
            
            if (inventoryText.includes(searchTerm) || laborText.includes(searchTerm) || categoryText.includes(searchTerm)) {
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