<!--
    * @file index.blade.php
    * @brief Vista para la gestión de Préstamos y Devoluciones en el módulo INFRASTOCK.
    *
    * Esta vista Blade permite al administrador visualizar, registrar y editar movimientos
    * de préstamos y devoluciones de insumos y herramientas. Utiliza Tailwind CSS para
    * un diseño moderno y responsive, y Alpine.js para la interactividad de los modales
    * de creación y edición, los cuales manejan las operaciones de forma asíncrona (AJAX)
    * y muestran notificaciones con SweetAlert2.
    * Extiende la plantilla `master.blade.php` y define el título y los ítems de las migas de pan.
    *
    * @param Modules\INFRASTOCK\Entities\WarehouseMovement[] $loans Colección de movimientos de almacén (préstamos/devoluciones).
    * @param Modules\INFRASTOCK\Entities\Tool[] $tools Colección de herramientas disponibles para el select en los modales.
    * @param Modules\INFRASTOCK\Entities\Equipment[] $equipments Colección de insumos disponibles para el select en los modales.
    * @param App\Models\User[] $users Colección de usuarios para el select en los modales (prestadores/receptores).
    * @param Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse[] $productiveUnitWarehouses Colección de áreas/bodegas.
    * @param Illuminate\Support\ViewErrorBag $errors Objeto que contiene los errores de validación de Laravel.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Gestión de Préstamos y Devoluciones')

@section('breadcrumb-items')
    <!-- Ítem de migas de pan para "Préstamos y Devoluciones" -->
    <li class="flex items-center">
        <a href="{{ route('infrastock.admin.loans.index') }}" class="text-green-600 hover:text-green-800">Préstamos y Devoluciones</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
@endsection

@section('content')
    <!-- Contenedor principal de la vista de gestión de préstamos y devoluciones -->
    <div x-data="{
        isCreateModalOpen: false,
        isEditModalOpen: false,
        currentLoan: { id: null, item_type: '', movement_id: '', user_id: '', role: '', productive_unit_warehouse_id: '' },
        validationErrors: {},
        createForm: { item_type: '', movement_id: '', user_id: '', role: '', productive_unit_warehouse_id: '' },

        init() {
            @if($errors->any() || session('error'))
                document.addEventListener('DOMContentLoaded', () => {
                    this.isCreateModalOpen = true;
                    this.validationErrors = @json($errors->messages());
                    const oldData = @json(old());
                    this.createForm.item_type = oldData.item_type || '';
                    this.createForm.movement_id = oldData.movement_id || '';
                    this.createForm.user_id = oldData.user_id || '';
                    this.createForm.role = oldData.role || '';
                    this.createForm.productive_unit_warehouse_id = oldData.productive_unit_warehouse_id || '';
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

        openEditModal(id, item_type, movement_id, user_id, role, productive_unit_warehouse_id) {
            this.isEditModalOpen = true;
            this.currentLoan = { id: id, item_type: item_type, movement_id: movement_id, user_id: user_id, role: role, productive_unit_warehouse_id: productive_unit_warehouse_id };
            this.validationErrors = {};
        },

        closeModals() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
            this.validationErrors = {};
        },

        resetCreateForm() {
            this.createForm = { item_type: '', movement_id: '', user_id: '', role: '', productive_unit_warehouse_id: '' };
            this.validationErrors = {};
        }
    }">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Listado de Préstamos y Devoluciones</h2>
                <button @click="openCreateModal()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    Registrar Movimiento
                </button>
            </div>

            <!-- Filtro de búsqueda automático -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Buscar por elemento, usuario, área o tipo de movimiento..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button onclick="clearSearch()" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>

            <!-- Tabla de Préstamos y Devoluciones -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Detalles de Movimientos</h3>
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $loans->firstItem() ?? 0 }} - {{ $loans->lastItem() ?? 0 }} de {{ $loans->total() }} registros
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Elemento</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área/Bodega</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Movimiento</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($loans as $loan)
                                    <tr class="hover:bg-gray-100 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loan->id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($loan->item_type == 'tool')
                                                {{ $loan->tool->name ?? 'N/A' }}
                                            @else
                                                {{ $loan->equipment->name ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($loan->item_type == 'tool')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">Herramienta</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Insumo</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->user->person->first_name ?? 'N/A' }} {{ $loan->user->person->first_last_name ?? '' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }} ({{ $loan->productiveUnitWarehouse->warehouse->name ?? 'N/A' }})</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($loan->role == 'Préstamo')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Préstamo</span>
                                            @elseif($loan->role == 'Devolución')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Devolución</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button @click="openEditModal({{ $loan->id }}, '{{ $loan->item_type }}', {{ $loan->movement_id }}, {{ $loan->user_id }}, '{{ $loan->role }}', {{ $loan->productive_unit_warehouse_id }})" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                <i class="fas fa-edit"></i> Editar
                                            </button>
                                            <form method="POST" action="{{ route('infrastock.admin.loans.destroy', $loan->id) }}" style="display: inline;" onsubmit="return confirmDeleteSync('{{ addslashes($loan->role) }}')">
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
                        <p class="text-gray-500">No hay movimientos que coincidan con tu búsqueda</p>
                    </div>
                    
                    <!-- Paginación -->
                    @if($loans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $loans->appends(request()->query())->links() }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Modal de Creación de Movimiento -->
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div @click.away="isCreateModalOpen = false; resetCreateForm();" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Registrar Nuevo Movimiento</h3>
                        <button @click="isCreateModalOpen = false; resetCreateForm();" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" action="{{ route('infrastock.admin.loans.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="create_item_type" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Elemento:</label>
                            <select name="item_type" id="create_item_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('item_type') border-red-500 @enderror" required>
                                <option value="">Selecciona tipo</option>
                                <option value="equipment" {{ old('item_type') == 'equipment' ? 'selected' : '' }}>Insumo</option>
                                <option value="tool" {{ old('item_type') == 'tool' ? 'selected' : '' }}>Herramienta</option>
                            </select>
                            @error('item_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="create_movement_id" class="block text-gray-700 text-sm font-bold mb-2">ID del Elemento:</label>
                            <select name="movement_id" id="create_movement_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('movement_id') border-red-500 @enderror" required>
                                <option value="">Selecciona un elemento</option>
                                @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}" {{ old('movement_id') == $equipment->id ? 'selected' : '' }}>{{ $equipment->name }} (ID: {{ $equipment->id }})</option>
                                @endforeach
                                @foreach($tools as $tool)
                                    <option value="{{ $tool->id }}" {{ old('movement_id') == $tool->id ? 'selected' : '' }}>{{ $tool->name }} (ID: {{ $tool->id }})</option>
                                @endforeach
                            </select>
                            @error('movement_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="create_user_id" class="block text-gray-700 text-sm font-bold mb-2">Usuario (Prestador/Receptor):</label>
                            <select name="user_id" id="create_user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('user_id') border-red-500 @enderror" required>
                                <option value="">Selecciona un usuario</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->person->first_name ?? 'N/A' }} {{ $user->person->first_last_name ?? '' }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="create_productive_unit_warehouse_id" class="block text-gray-700 text-sm font-bold mb-2">Área Productiva / Bodega:</label>
                            <select name="productive_unit_warehouse_id" id="create_productive_unit_warehouse_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('productive_unit_warehouse_id') border-red-500 @enderror" required>
                                <option value="">Selecciona un área/bodega</option>
                                @foreach($productiveUnitWarehouses as $puw)
                                    <option value="{{ $puw->id }}" {{ old('productive_unit_warehouse_id') == $puw->id ? 'selected' : '' }}>{{ $puw->productiveUnit->name ?? 'N/A' }} ({{ $puw->warehouse->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            @error('productive_unit_warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="create_role" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Movimiento:</label>
                            <select name="role" id="create_role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('role') border-red-500 @enderror" required>
                                <option value="">Seleccione tipo</option>
                                <option value="Préstamo" {{ old('role') == 'Préstamo' ? 'selected' : '' }}>Préstamo</option>
                                <option value="Devolución" {{ old('role') == 'Devolución' ? 'selected' : '' }}>Devolución</option>
                            </select>
                            @error('role')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isCreateModalOpen = false; resetCreateForm();" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Guardar Movimiento</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición de Movimiento -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div @click.away="isEditModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Movimiento</h3>
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" :action="`{{ route('infrastock.admin.loans.update', '') }}/${currentLoan.id}`">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="edit_item_type" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Elemento:</label>
                            <select name="item_type" id="edit_item_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('item_type') border-red-500 @enderror" x-model="currentLoan.item_type" required>
                                <option value="">Selecciona tipo</option>
                                <option value="equipment">Insumo</option>
                                <option value="tool">Herramienta</option>
                            </select>
                            @error('item_type')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_movement_id" class="block text-gray-700 text-sm font-bold mb-2">ID del Elemento:</label>
                            <select name="movement_id" id="edit_movement_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('movement_id') border-red-500 @enderror" x-model="currentLoan.movement_id" required>
                                <option value="">Selecciona un elemento</option>
                                @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ $equipment->name }} (ID: {{ $equipment->id }})</option>
                                @endforeach
                                @foreach($tools as $tool)
                                    <option value="{{ $tool->id }}">{{ $tool->name }} (ID: {{ $tool->id }})</option>
                                @endforeach
                            </select>
                            @error('movement_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_user_id" class="block text-gray-700 text-sm font-bold mb-2">Usuario (Prestador/Receptor):</label>
                            <select name="user_id" id="edit_user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('user_id') border-red-500 @enderror" x-model="currentLoan.user_id" required>
                                <option value="">Selecciona un usuario</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->person->first_name ?? 'N/A' }} {{ $user->person->first_last_name ?? '' }}</option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_productive_unit_warehouse_id" class="block text-gray-700 text-sm font-bold mb-2">Área Productiva / Bodega:</label>
                            <select name="productive_unit_warehouse_id" id="edit_productive_unit_warehouse_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('productive_unit_warehouse_id') border-red-500 @enderror" x-model="currentLoan.productive_unit_warehouse_id" required>
                                <option value="">Selecciona un área/bodega</option>
                                @foreach($productiveUnitWarehouses as $puw)
                                    <option value="{{ $puw->id }}">{{ $puw->productiveUnit->name ?? 'N/A' }} ({{ $puw->warehouse->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            @error('productive_unit_warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="edit_role" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Movimiento:</label>
                            <select name="role" id="edit_role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('role') border-red-500 @enderror" x-model="currentLoan.role" required>
                                <option value="">Seleccione tipo</option>
                                <option value="Préstamo">Préstamo</option>
                                <option value="Devolución">Devolución</option>
                            </select>
                            @error('role')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isEditModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Actualizar Movimiento</button>
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
function confirmDeleteSync(movementType) {
    let confirmed = false;
    
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Quieres eliminar este movimiento de "${movementType}"?`,
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
            text: 'El movimiento ha sido eliminado correctamente.',
            showConfirmButton: false,
            timer: 1500
        });
    @endif
    
    @if(session('error') && session('error') !== 'Ya existe un movimiento con estos datos. Por favor, verifica la información.')
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
            // Columnas a buscar: Elemento (col 1), Tipo (col 2), Usuario (col 3), Área/Bodega (col 4), Movimiento (col 5)
            const elementCell = row.cells[1];
            const typeCell = row.cells[2];
            const userCell = row.cells[3];
            const areaCell = row.cells[4];
            const movementCell = row.cells[5];
            
            const elementText = elementCell.textContent.toLowerCase();
            const typeText = typeCell.textContent.toLowerCase();
            const userText = userCell.textContent.toLowerCase();
            const areaText = areaCell.textContent.toLowerCase();
            const movementText = movementCell.textContent.toLowerCase();
            
            if (elementText.includes(searchTerm) || typeText.includes(searchTerm) || userText.includes(searchTerm) || areaText.includes(searchTerm) || movementText.includes(searchTerm)) {
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