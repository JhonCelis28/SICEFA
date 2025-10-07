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
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Listado de Préstamos y Devoluciones</h2>
            <!-- Botón para abrir el modal de registro de un nuevo movimiento -->
            <button @click="isCreateModalOpen = true" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i> Registrar Movimiento
            </button>
        </div>

        <!-- Tabla de Préstamos y Devoluciones -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Detalles de Movimientos</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- Encabezados de la tabla -->
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
                            <!-- Iteración sobre cada movimiento para mostrar sus datos -->
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($loan->item_type) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->user->person->first_name ?? 'N/A' }} {{ $loan->user->person->first_last_name ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }} ({{ $loan->productiveUnitWarehouse->warehouse->name ?? 'N/A' }})</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <!-- Badge de estado de movimiento con colores condicionales -->
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($loan->role == 'Préstamo') bg-blue-100 text-blue-800
                                            @elseif($loan->role == 'Devolución') bg-green-100 text-green-800
                                            @endif">
                                            {{ $loan->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <!-- Botón para abrir el modal de edición del movimiento -->
                                        <button @click="openEditModal(
                                            {{ $loan->id }},
                                            @json($loan->item_type),
                                            @json($loan->movement_id),
                                            @json($loan->user_id),
                                            @json($loan->role),
                                            @json($loan->productive_unit_warehouse_id)
                                        )" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <!-- Formulario para eliminar un movimiento -->
                                        <form action="{{ route('infrastock.admin.loans.destroy', $loan->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este movimiento?');">
                                            @csrf {{-- Token CSRF para protección. --}}
                                            @method('DELETE') {{-- Método HTTP DELETE para eliminación. --}}
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
            </div>
        </div>

        <!-- Lógica Alpine.js y Modales para Creación y Edición de Movimientos -->
        <div x-data="loanCrudModals" x-init="
            // Si hay errores de validación del servidor al cargar la página, abre el modal de creación.
            @if($errors->hasAny(['item_type', 'movement_id', 'user_id', 'role']))
                isCreateModalOpen = true;
                validationErrors = @json($errors->messages());
            @endif
        ">
            <!-- Modal de Creación de Movimiento -->
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                <div @click.away="isCreateModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Registrar Nuevo Movimiento</h3>
                        <!-- Botón para cerrar el modal de creación -->
                        <button @click="isCreateModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <!-- Formulario de creación de movimiento -->
                    <form @submit.prevent="createLoan" x-ref="createForm">
                        @csrf
                        <div class="mb-4">
                            <label for="create_item_type" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Elemento:</label>
                            <select name="item_type" id="create_item_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.item_type}" x-model="newItem.item_type" required>
                                <option value="">Selecciona tipo</option>
                                <option value="equipment">Insumo</option>
                                <option value="tool">Herramienta</option>
                            </select>
                            <!-- Muestra el error de validación para el campo 'item_type' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.item_type ? validationErrors.item_type[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="create_movement_id" class="block text-gray-700 text-sm font-bold mb-2">ID del Elemento:</label>
                            <select name="movement_id" id="create_movement_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.movement_id}" x-model="newItem.movement_id" required>
                                <option value="">Selecciona un elemento</option>
                                <!-- Opciones condicionales para insumos o herramientas -->
                                <template x-if="newItem.item_type === 'equipment'">
                                    @foreach($equipments as $equipment)
                                        <option value="{{ $equipment->id }}">{{ $equipment->name }} (ID: {{ $equipment->id }})</option>
                                    @endforeach
                                </template>
                                <template x-if="newItem.item_type === 'tool'">
                                    @foreach($tools as $tool)
                                        <option value="{{ $tool->id }}">{{ $tool->name }} (ID: {{ $tool->id }})</option>
                                    @endforeach
                                </template>
                            </select>
                            <!-- Muestra el error de validación para el campo 'movement_id' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.movement_id ? validationErrors.movement_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="create_user_id" class="block text-gray-700 text-sm font-bold mb-2">Usuario (Prestador/Receptor):</label>
                            <select name="user_id" id="create_user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.user_id}" x-model="newItem.user_id" required>
                                <option value="">Selecciona un usuario</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->person->first_name ?? 'N/A' }} {{ $user->person->first_last_name ?? '' }}</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'user_id' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.user_id ? validationErrors.user_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="create_productive_unit_warehouse_id" class="block text-gray-700 text-sm font-bold mb-2">Área Productiva / Bodega:</label>
                            <select name="productive_unit_warehouse_id" id="create_productive_unit_warehouse_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.productive_unit_warehouse_id}" x-model="newItem.productive_unit_warehouse_id" required>
                                <option value="">Selecciona un área/bodega</option>
                                @foreach($productiveUnitWarehouses as $puw)
                                    <option value="{{ $puw->id }}">{{ $puw->productiveUnit->name ?? 'N/A' }} ({{ $puw->warehouse->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'productive_unit_warehouse_id' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.productive_unit_warehouse_id ? validationErrors.productive_unit_warehouse_id[0] : ''"></p>
                        </div>
                        <div class="mb-6">
                            <label for="create_role" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Movimiento:</label>
                            <select name="role" id="create_role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.role}" x-model="newItem.role" required>
                                <option value="">Seleccione tipo</option>
                                <option value="Préstamo">Préstamo</option>
                                <option value="Devolución">Devolución</option>
                            </select>
                            <!-- Muestra el error de validación para el campo 'role' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.role ? validationErrors.role[0] : ''"></p>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <!-- Botones de cancelar y guardar para el modal de creación -->
                            <button type="button" @click="isCreateModalOpen = false; validationErrors = {}; newItem = {}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Guardar Movimiento</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición de Movimiento -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                <div @click.away="isEditModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Movimiento</h3>
                        <!-- Botón para cerrar el modal de edición -->
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <!-- Formulario de edición de movimiento -->
                    <form @submit.prevent="updateLoan" x-ref="editForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="edit_item_type" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Elemento:</label>
                            <select name="item_type" id="edit_item_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.item_type}" x-model="currentLoan.item_type" required>
                                <option value="">Selecciona tipo</option>
                                <option value="equipment">Insumo</option>
                                <option value="tool">Herramienta</option>
                            </select>
                            <!-- Muestra el error de validación para el campo 'item_type' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.item_type ? validationErrors.item_type[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_movement_id" class="block text-gray-700 text-sm font-bold mb-2">ID del Elemento:</label>
                            <select name="movement_id" id="edit_movement_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.movement_id}" x-model="currentLoan.movement_id" required>
                                <option value="">Selecciona un elemento</option>
                                <!-- Opciones condicionales para insumos o herramientas -->
                                <template x-if="currentLoan.item_type === 'equipment'">
                                    @foreach($equipments as $equipment)
                                        <option value="{{ $equipment->id }}">{{ $equipment->name }} (ID: {{ $equipment->id }})</option>
                                    @endforeach
                                </template>
                                <template x-if="currentLoan.item_type === 'tool'">
                                    @foreach($tools as $tool)
                                        <option value="{{ $tool->id }}">{{ $tool->name }} (ID: {{ $tool->id }})</option>
                                    @endforeach
                                </template>
                            </select>
                            <!-- Muestra el error de validación para el campo 'movement_id' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.movement_id ? validationErrors.movement_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_user_id" class="block text-gray-700 text-sm font-bold mb-2">Usuario (Prestador/Receptor):</label>
                            <select name="user_id" id="edit_user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.user_id}" x-model="currentLoan.user_id" required>
                                <option value="">Selecciona un usuario</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->person->first_name ?? 'N/A' }} {{ $user->person->first_last_name ?? '' }}</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'user_id' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.user_id ? validationErrors.user_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_productive_unit_warehouse_id" class="block text-gray-700 text-sm font-bold mb-2">Área Productiva / Bodega:</label>
                            <select name="productive_unit_warehouse_id" id="edit_productive_unit_warehouse_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.productive_unit_warehouse_id}" x-model="currentLoan.productive_unit_warehouse_id" required>
                                <option value="">Selecciona un área/bodega</option>
                                @foreach($productiveUnitWarehouses as $puw)
                                    <option value="{{ $puw->id }}">{{ $puw->productiveUnit->name ?? 'N/A' }} ({{ $puw->warehouse->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'productive_unit_warehouse_id' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.productive_unit_warehouse_id ? validationErrors.productive_unit_warehouse_id[0] : ''"></p>
                        </div>
                        <div class="mb-6">
                            <label for="edit_role" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Movimiento:</label>
                            <select name="role" id="edit_role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.role}" x-model="currentLoan.role" required>
                                <option value="">Seleccione tipo</option>
                                <option value="Préstamo">Préstamo</option>
                                <option value="Devolución">Devolución</option>
                            </select>
                            <!-- Muestra el error de validación para el campo 'role' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.role ? validationErrors.role[0] : ''"></p>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <!-- Botones de cancelar y actualizar para el modal de edición -->
                            <button type="button" @click="isEditModalOpen = false; validationErrors = {}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
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
    document.addEventListener('alpine:init', () => {
        Alpine.data('loanCrudModals', () => ({
            isCreateModalOpen: false, // Estado del modal de creación (abierto/cerrado).
            isEditModalOpen: false,   // Estado del modal de edición (abierto/cerrado).
            newItem: { // Datos para el formulario de creación de nuevo movimiento.
                item_type: '',
                movement_id: '',
                user_id: '',
                productive_unit_warehouse_id: '',
                role: ''
            },
            currentLoan: { // Datos del movimiento actual que se está editando.
                id: null,
                item_type: '',
                movement_id: '',
                user_id: '',
                productive_unit_warehouse_id: '',
                role: ''
            },
            validationErrors: {}, // Almacena los errores de validación recibidos del servidor.

            /**
             * Abre el modal de edición y carga los datos del movimiento seleccionado.
             * @param int id ID del movimiento de almacén.
             * @param string item_type Tipo de elemento involucrado (e.g., 'tool', 'equipment').
             * @param int movement_id ID del elemento específico (tool_id o equipment_id).
             * @param int user_id ID del usuario asociado al movimiento.
             * @param string role Rol del movimiento (e.g., 'Préstamo', 'Devolución').
             * @param int productive_unit_warehouse_id ID de la unidad productiva/bodega asociada.
             */
            openEditModal(id, item_type, movement_id, user_id, role, productive_unit_warehouse_id) {
                this.isEditModalOpen = true;
                this.currentLoan.id = id;
                this.currentLoan.item_type = item_type;
                this.currentLoan.movement_id = movement_id;
                this.currentLoan.user_id = user_id;
                this.currentLoan.role = role;
                this.currentLoan.productive_unit_warehouse_id = productive_unit_warehouse_id;
                this.validationErrors = {}; // Limpia errores de validación previos.
            },

            /**
             * Envía el formulario de creación de movimiento de forma asíncrona (AJAX).
             * Maneja la respuesta del servidor, mostrando mensajes de éxito o errores de validación.
             */
            async createLoan() {
                try {
                    const formData = new FormData(this.$refs.createForm);
                    const response = await fetch('{{ route('infrastock.admin.loans.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Token CSRF para seguridad.
                        },
                        body: formData
                    });

                    if (response.ok) {
                        this.isCreateModalOpen = false;
                        this.validationErrors = {}; // Limpia errores al crear con éxito.
                        // Muestra una notificación de éxito y recarga la página.
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Movimiento registrado correctamente.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else if (response.status === 422) {
                        // Si hay errores de validación, los muestra en el formulario.
                        const errorData = await response.json();
                        this.validationErrors = errorData.errors;
                    } else {
                        // Muestra un mensaje de error general.
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al registrar el movimiento.'
                        });
                    }
                } catch (error) {
                    console.error('Error al enviar el formulario:', error);
                    // Muestra un mensaje de error de conexión.
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor.'
                    });
                }
            },

            /**
             * Envía el formulario de actualización de movimiento de forma asíncrona (AJAX).
             * Maneja la respuesta del servidor, mostrando mensajes de éxito o errores de validación.
             */
            async updateLoan() {
                try {
                    const formData = new FormData(this.$refs.editForm);
                    formData.append('_method', 'PUT'); // Simula el método PUT para Laravel.
                    const response = await fetch('{{ route('infrastock.admin.loans.update', '') }}' + '/' + this.currentLoan.id, {
                        method: 'POST', // Se usa POST para solicitudes PUT con simulación de método.
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Token CSRF.
                        },
                        body: formData
                    });

                    if (response.ok) {
                        this.isEditModalOpen = false;
                        this.validationErrors = {}; // Limpia errores al actualizar con éxito.
                        // Muestra una notificación de éxito y recarga la página.
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Movimiento actualizado correctamente.',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else if (response.status === 422) {
                        // Si hay errores de validación, los muestra en el formulario.
                        const errorData = await response.json();
                        this.validationErrors = errorData.errors;
                    } else {
                        // Muestra un mensaje de error general.
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un problema al actualizar el movimiento.'
                        });
                    }
                } catch (error) {
                    console.error('Error al enviar el formulario:', error);
                    // Muestra un mensaje de error de conexión.
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo conectar con el servidor.'
                    });
                }
            }
        }))
    });
</script>
@endsection
