<!--
    * @file index.blade.php
    * @brief Vista para la gestión de Solicitudes de Insumos en el módulo INFRASTOCK.
    *
    * Esta vista Blade permite al administrador visualizar y gestionar las solicitudes de insumos
    * realizadas por otros roles. Incluye una tabla para listar las solicitudes y un modal de edición
    * para cambiar el estado de una solicitud. Utiliza Tailwind CSS para el diseño y Alpine.js
    * para la interactividad, manejando las actualizaciones de forma asíncrona (AJAX).
    * Extiende la plantilla `master.blade.php` y define el título y los ítems de las migas de pan.
    *
    * @param Modules\INFRASTOCK\Entities\WarehouseMovement[] $supplyRequests Colección de solicitudes de insumos.
    * @param Modules\INFRASTOCK\Entities\Equipment[] $equipments Colección de insumos disponibles para el select en el modal.
    * @param App\Models\User[] $users Colección de usuarios para el select en el modal (solicitantes).
    * @param Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse[] $productiveUnitWarehouses Colección de áreas/bodegas.
    * @param Illuminate\Support\ViewErrorBag $errors Objeto que contiene los errores de validación de Laravel.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Gestión de Solicitudes de Insumos')

@section('breadcrumb-items')
    <!-- Ítem de migas de pan para "Solicitudes de Insumos" -->
    <li class="flex items-center">
        <a href="{{ route('infrastock.admin.supply-requests.index') }}" class="text-green-600 hover:text-green-800">Solicitudes de Insumos</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
@endsection

@section('content')
    <!-- Contenedor principal de la vista de gestión de solicitudes de insumos -->
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Listado de Solicitudes de Insumos</h2>
            {{-- No hay botón de "Crear" directamente aquí, ya que las solicitudes se originan desde otros roles --}}
        </div>

        <!-- Tabla de Solicitudes de Insumos -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Detalles de las Solicitudes</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- Encabezados de la tabla -->
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insumo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área/Bodega</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Solicitud</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Iteración sobre cada solicitud de insumo para mostrar sus datos -->
                            @foreach($supplyRequests as $request)
                                <tr class="hover:bg-gray-100 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $request->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->equipment->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->user->person->first_name ?? 'N/A' }} {{ $request->user->person->first_last_name ?? '' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->amount }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->description ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }} ({{ $request->productiveUnitWarehouse->warehouse->name ?? 'N/A' }})</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <!-- Badge de estado con colores condicionales -->
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($request->role == 'Solicitud') bg-yellow-100 text-yellow-800
                                            @elseif($request->role == 'approved') bg-green-100 text-green-800
                                            @elseif($request->role == 'rejected') bg-red-100 text-red-800
                                            @elseif($request->role == 'delivered') bg-blue-100 text-blue-800
                                            @endif">
                                            {{ $request->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <!-- Botón para abrir el modal de gestión/edición de la solicitud -->
                                        <button @click="openEditModal(
                                            {{ $request->id }},
                                            @json($request->movement_id),
                                            @json($request->user_id),
                                            @json($request->productive_unit_warehouse_id),
                                            @json($request->amount),
                                            @json($request->description),
                                            @json($request->role)
                                        )" class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i> Gestionar
                                        </button>
                                        <!-- Formulario para eliminar una solicitud -->
                                        <form action="{{ route('infrastock.admin.supply-requests.destroy', $request->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta solicitud?');">
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

        <!-- Lógica Alpine.js para la gestión del modal de edición/gestión de solicitudes -->
        <div x-data="supplyRequestCrudModals">
            <!-- Modal de Edición/Gestión de Solicitud de Insumo -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                <div @click.away="isEditModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Gestionar Solicitud de Insumo</h3>
                        <!-- Botón para cerrar el modal de edición -->
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <!-- Formulario de edición/gestión de solicitud -->
                    <form @submit.prevent="updateSupplyRequest" x-ref="editForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="edit_equipment_id" class="block text-gray-700 text-sm font-bold mb-2">Insumo:</label>
                            <select name="movement_id" id="edit_equipment_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.movement_id}" x-model="currentRequest.movement_id" required disabled>
                                <option value="">Selecciona un insumo</option>
                                @foreach($equipments as $equipment)
                                    <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'movement_id' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_equipment_id_error" x-text="validationErrors.movement_id ? validationErrors.movement_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_user_id" class="block text-gray-700 text-sm font-bold mb-2">Solicitante:</label>
                            <select name="user_id" id="edit_user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.user_id}" x-model="currentRequest.user_id" required disabled>
                                <option value="">Selecciona un usuario</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->person->first_name ?? 'N/A' }} {{ $user->person->first_last_name ?? '' }}</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'user_id' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_user_id_error" x-text="validationErrors.user_id ? validationErrors.user_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_productive_unit_warehouse_id" class="block text-gray-700 text-sm font-bold mb-2">Área Productiva / Bodega:</label>
                            <select name="productive_unit_warehouse_id" id="edit_productive_unit_warehouse_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.productive_unit_warehouse_id}" x-model="currentRequest.productive_unit_warehouse_id" required disabled>
                                <option value="">Selecciona un área/bodega</option>
                                @foreach($productiveUnitWarehouses as $puw)
                                    <option value="{{ $puw->id }}">{{ $puw->productiveUnit->name ?? 'N/A' }} ({{ $puw->warehouse->name ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'productive_unit_warehouse_id' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_productive_unit_warehouse_id_error" x-text="validationErrors.productive_unit_warehouse_id ? validationErrors.productive_unit_warehouse_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad:</label>
                            <input type="number" name="amount" id="edit_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.amount}" x-model="currentRequest.amount" required min="1" disabled>
                            <!-- Muestra el error de validación para el campo 'amount' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_amount_error" x-text="validationErrors.amount ? validationErrors.amount[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_description" class="block text-gray-700 text-sm font-bold mb-2">Descripción/Razón:</label>
                            <textarea name="description" id="edit_description" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.description}" x-model="currentRequest.description" disabled></textarea>
                            <!-- Muestra el error de validación para el campo 'description' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_description_error" x-text="validationErrors.description ? validationErrors.description[0] : ''"></p>
                        </div>
                        <div class="mb-6">
                            <label for="edit_role" class="block text-gray-700 text-sm font-bold mb-2">Cambiar Estado:</label>
                            <select name="role" id="edit_role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.role}" x-model="currentRequest.role" required>
                                <option value="">Selecciona un estado</option>
                                <option value="Solicitud">Solicitado</option>
                                <option value="approved">Aprobado</option>
                                <option value="rejected">Rechazado</option>
                                <option value="delivered">Entregado</option>
                            </select>
                            <!-- Muestra el error de validación para el campo 'role' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_role_error" x-text="validationErrors.role ? validationErrors.role[0] : ''"></p>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <!-- Botones de cancelar y actualizar estado para el modal -->
                            <button type="button" @click="isEditModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Actualizar Estado</button>
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
        Alpine.data('supplyRequestCrudModals', () => ({
            isEditModalOpen: false, // Estado del modal de edición (abierto/cerrado).
            currentRequest: { id: null, movement_id: '', user_id: '', productive_unit_warehouse_id: '', amount: '', description: '', role: '' }, // Datos de la solicitud que se está gestionando.
            validationErrors: {}, // Almacena los errores de validación recibidos del servidor.

            /**
             * Abre el modal de edición/gestión y carga los datos de la solicitud seleccionada.
             * @param int id ID del movimiento de almacén (solicitud).
             * @param int movement_id ID del insumo (equipment_id) asociado a la solicitud.
             * @param int user_id ID del usuario que realizó la solicitud.
             * @param int productive_unit_warehouse_id ID de la unidad productiva/bodega.
             * @param int amount Cantidad solicitada.
             * @param string description Descripción de la solicitud.
             * @param string role Estado actual de la solicitud (e.g., 'Solicitud', 'approved', 'rejected', 'delivered').
             */
            openEditModal(id, movement_id, user_id, productive_unit_warehouse_id, amount, description, role) {
                this.isEditModalOpen = true;
                this.currentRequest.id = id;
                this.currentRequest.movement_id = movement_id;
                this.currentRequest.user_id = user_id;
                this.currentRequest.productive_unit_warehouse_id = productive_unit_warehouse_id;
                this.currentRequest.amount = amount;
                this.currentRequest.description = description;
                this.currentRequest.role = role;
                this.validationErrors = {}; // Limpia errores de validación previos.
            },

            /**
             * Envía el formulario de actualización de la solicitud de insumo de forma asíncrona (AJAX).
             * Maneja la respuesta del servidor, mostrando mensajes de éxito o errores de validación.
             */
            async updateSupplyRequest() {
                try {
                    const formData = new FormData(this.$refs.editForm);
                    formData.append('_method', 'PUT'); // Simula el método PUT para Laravel.
                    const response = await fetch('{{ route('infrastock.admin.supply-requests.update', '') }}' + this.currentRequest.id, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Token CSRF para seguridad.
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
                            text: 'Estado de la solicitud actualizado correctamente.',
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
                            text: 'Hubo un problema al actualizar el estado de la solicitud.'
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
