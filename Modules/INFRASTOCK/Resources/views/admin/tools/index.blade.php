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
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Listado de Herramientas</h2>
            <!-- Botón para abrir el modal de registro de nueva herramienta -->
            <button @click="isCreateModalOpen = true" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200 shadow-md">
                Registrar Nueva Herramienta
            </button>
        </div>

        <!-- Tabla de Herramientas -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Detalles de las Herramientas</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- Encabezados de la tabla -->
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
                            <!-- Iteración sobre cada herramienta para mostrar sus datos -->
                            @foreach($tools as $tool)
                                <tr class="hover:bg-gray-100 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $tool->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">ID: {{ $tool->inventory->id }} - {{ $tool->inventory->description ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->labor->description ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->amount }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->price }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->category->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <!-- Botón para abrir el modal de edición, pasando los datos de la herramienta actual -->
                                        <button @click="openEditModal({{ $tool->id }}, @json($tool->inventory->id), @json($tool->labor->id), @json($tool->amount), @json($tool->price), @json($tool->category->id))" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <!-- Formulario para eliminar una herramienta -->
                                        <form action="{{ route('infrastock.admin.tools.destroy', $tool->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta herramienta?');">
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

        <!-- Lógica Alpine.js para la gestión de modales de creación y edición -->
        <div x-data="toolCrudModals">
            <!-- Modal de Creación de Herramienta -->
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                <div @click.away="isCreateModalOpen = false; resetCreateForm();" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Registrar Nueva Herramienta</h3>
                        <!-- Botón para cerrar el modal de creación -->
                        <button @click="isCreateModalOpen = false; resetCreateForm();" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <!-- Formulario de creación de herramienta -->
                    <form @submit.prevent="createTool" x-ref="createForm">
                        @csrf
                        <div class="mb-4">
                            <label for="create_inventory_id" class="block text-gray-700 text-sm font-bold mb-2">Inventario:</label>
                            <select name="inventory_id" id="create_inventory_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.inventory_id}" x-model="createForm.inventory_id" required>
                                <option value="">Selecciona un inventario</option>
                                @foreach($inventories as $inventory)
                                    <option value="{{ $inventory->id }}">{{ $inventory->id }} - {{ $inventory->description }}</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'inventory_id' -->
                            <p class="text-red-500 text-xs italic mt-2" id="create_inventory_id_error" x-text="validationErrors.inventory_id ? validationErrors.inventory_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="create_labor_id" class="block text-gray-700 text-sm font-bold mb-2">Labor:</label>
                            <select name="labor_id" id="create_labor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.labor_id}" x-model="createForm.labor_id" required>
                                <option value="">Selecciona una labor</option>
                                @foreach($labors as $labor)
                                    <option value="{{ $labor->id }}">{{ $labor->description }}</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'labor_id' -->
                            <p class="text-red-500 text-xs italic mt-2" id="create_labor_id_error" x-text="validationErrors.labor_id ? validationErrors.labor_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="create_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad:</label>
                            <input type="number" name="amount" id="create_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.amount}" x-model="createForm.amount" required min="0">
                            <!-- Muestra el error de validación para el campo 'amount' -->
                            <p class="text-red-500 text-xs italic mt-2" id="create_amount_error" x-text="validationErrors.amount ? validationErrors.amount[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="create_price" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                            <input type="number" name="price" id="create_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.price}" x-model="createForm.price" required min="0" step="0.01">
                            <!-- Muestra el error de validación para el campo 'price' -->
                            <p class="text-red-500 text-xs italic mt-2" id="create_price_error" x-text="validationErrors.price ? validationErrors.price[0] : ''"></p>
                        </div>
                        <div class="mb-6">
                            <label for="create_category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                            <select name="category_id" id="create_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.category_id}" x-model="createForm.category_id" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'category_id' -->
                            <p class="text-red-500 text-xs italic mt-2" id="create_category_id_error" x-text="validationErrors.category_id ? validationErrors.category_id[0] : ''"></p>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <!-- Botones de cancelar y guardar para el modal de creación -->
                            <button type="button" @click="isCreateModalOpen = false; resetCreateForm();" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Guardar Herramienta</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición de Herramienta -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                <div @click.away="isEditModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Herramienta</h3>
                        <!-- Botón para cerrar el modal de edición -->
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <!-- Formulario de edición de herramienta -->
                    <form @submit.prevent="updateTool" x-ref="editForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="edit_inventory_id" class="block text-gray-700 text-sm font-bold mb-2">Inventario:</label>
                            <select name="inventory_id" id="edit_inventory_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.inventory_id}" x-model="currentTool.inventory_id" required>
                                <option value="">Selecciona un inventario</option>
                                @foreach($inventories as $inventory)
                                    <option value="{{ $inventory->id }}">{{ $inventory->id }} - {{ $inventory->description }}</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'inventory_id' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_inventory_id_error" x-text="validationErrors.inventory_id ? validationErrors.inventory_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_labor_id" class="block text-gray-700 text-sm font-bold mb-2">Labor:</label>
                            <select name="labor_id" id="edit_labor_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.labor_id}" x-model="currentTool.labor_id" required>
                                <option value="">Selecciona una labor</option>
                                @foreach($labors as $labor)
                                    <option value="{{ $labor->id }}">{{ $labor->description }}</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'labor_id' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_labor_id_error" x-text="validationErrors.labor_id ? validationErrors.labor_id[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad:</label>
                            <input type="number" name="amount" id="edit_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.amount}" x-model="currentTool.amount" required min="0">
                            <!-- Muestra el error de validación para el campo 'amount' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_amount_error" x-text="validationErrors.amount ? validationErrors.amount[0] : ''"></p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_price" class="block text-gray-700 text-sm font-bold mb-2">Precio:</label>
                            <input type="number" name="price" id="edit_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.price}" x-model="currentTool.price" required min="0" step="0.01">
                            <!-- Muestra el error de validación para el campo 'price' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_price_error" x-text="validationErrors.price ? validationErrors.price[0] : ''"></p>
                        </div>
                        <div class="mb-6">
                            <label for="edit_category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                            <select name="category_id" id="edit_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.category_id}" x-model="currentTool.category_id" required>
                                <option value="">Selecciona una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <!-- Muestra el error de validación para el campo 'category_id' -->
                            <p class="text-red-500 text-xs italic mt-2" id="edit_category_id_error" x-text="validationErrors.category_id ? validationErrors.category_id[0] : ''"></p>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <!-- Botones de cancelar y actualizar para el modal de edición -->
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
    document.addEventListener('alpine:init', () => {
        Alpine.data('toolCrudModals', () => ({
            isCreateModalOpen: false, // Estado del modal de creación (abierto/cerrado).
            isEditModalOpen: false,   // Estado del modal de edición (abierto/cerrado).
            currentTool: { id: null, inventory_id: '', labor_id: '', amount: '', price: '', category_id: '' }, // Datos de la herramienta que se está editando.
            validationErrors: {}, // Almacena los errores de validación recibidos del servidor.
            createForm: { inventory_id: '', labor_id: '', amount: '', price: '', category_id: '' }, // Datos del formulario de creación.

            init() {
                // Recupera los errores de validación del servidor y los datos `old()` para el formulario de creación.
                const serverErrors = @json($errors->toArray());
                const oldData = @json(old());

                // Si hay errores de validación, abre el modal de creación y precarga los datos.
                if (Object.keys(serverErrors).length > 0) {
                    this.isCreateModalOpen = true;
                    this.validationErrors = serverErrors.errors;
                    // Llena el formulario de creación con los datos antiguos para mantener la información.
                    this.createForm.inventory_id = oldData.inventory_id || '';
                    this.createForm.labor_id = oldData.labor_id || '';
                    this.createForm.amount = oldData.amount || '';
                    this.createForm.price = oldData.price || '';
                    this.createForm.category_id = oldData.category_id || '';
                }
            },

            /**
             * Abre el modal de edición y carga los datos de la herramienta seleccionada.
             * @param int id ID de la herramienta a editar.
             * @param int inventory_id ID del inventario asociado a la herramienta.
             * @param int labor_id ID de la labor asociada a la herramienta.
             * @param int amount Cantidad de la herramienta.
             * @param float price Precio de la herramienta.
             * @param int category_id ID de la categoría de la herramienta.
             */
            openEditModal(id, inventory_id, labor_id, amount, price, category_id) {
                this.isEditModalOpen = true;
                this.currentTool.id = id;
                this.currentTool.inventory_id = inventory_id;
                this.currentTool.labor_id = labor_id;
                this.currentTool.amount = amount;
                this.currentTool.price = price;
                this.currentTool.category_id = category_id;
                this.validationErrors = {}; // Limpia errores de validación previos.
            },

            /**
             * Reinicia el formulario de creación, limpiando todos los campos y los errores de validación.
             */
            resetCreateForm() {
                this.createForm = { inventory_id: '', labor_id: '', amount: '', price: '', category_id: '' };
                this.validationErrors = {}; // Limpia errores de validación de Alpine.
            },

            /**
             * Envía el formulario de creación de herramienta de forma asíncrona (AJAX).
             * Maneja la respuesta del servidor, mostrando mensajes de éxito o errores de validación.
             */
            async createTool() {
                try {
                    const formData = new FormData(this.$refs.createForm);
                    const response = await fetch('{{ route('infrastock.admin.tools.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Token CSRF para seguridad.
                        },
                        body: formData
                    });

                    if (response.ok) {
                        this.isCreateModalOpen = false;
                        this.resetCreateForm();
                        // Muestra una notificación de éxito y recarga la página.
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: 'Herramienta registrada correctamente.',
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
                            text: 'Hubo un problema al registrar la herramienta.'
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
             * Envía el formulario de actualización de herramienta de forma asíncrona (AJAX).
             * Maneja la respuesta del servidor, mostrando mensajes de éxito o errores de validación.
             */
            async updateTool() {
                try {
                    const formData = new FormData(this.$refs.editForm);
                    formData.append('_method', 'PUT'); // Simula el método PUT para Laravel.
                    const response = await fetch('{{ route('infrastock.admin.tools.update', '') }}' + this.currentTool.id, {
                        method: 'POST',
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
                            text: 'Herramienta actualizada correctamente.',
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
                            text: 'Hubo un problema al actualizar la herramienta.'
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
