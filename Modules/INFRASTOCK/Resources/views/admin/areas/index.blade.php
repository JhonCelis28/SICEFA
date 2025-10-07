<!--
    * @file index.blade.php
    * @brief Vista para la gestión (CRUD) de Áreas Productivas en el módulo INFRASTOCK.
    *
    * Esta vista Blade permite al administrador visualizar, crear, editar y eliminar
    * áreas productivas. Utiliza Tailwind CSS para un diseño moderno y responsive,
    * y Alpine.js para la interactividad de los modales de creación y edición, los cuales
    * manejan las operaciones de forma asíncrona (AJAX) y muestran notificaciones con SweetAlert2.
    * Extiende la plantilla `master.blade.php` y define el título y los ítems de las migas de pan.
    *
    * @param Modules\SICA\Entities\ProductiveUnit[] $areas Colección de unidades productivas existentes.
    * @param Illuminate\Support\ViewErrorBag $errors Objeto que contiene los errores de validación de Laravel.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Gestión de Áreas Productivas')

@section('breadcrumb-items')
    <!-- Ítem de migas de pan para "Áreas Productivas" -->
    <li class="flex items-center">
        <a href="{{ route('infrastock.admin.areas.index') }}" class="text-green-600 hover:text-green-800">Áreas Productivas</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
@endsection

@section('content')
    <!-- Contenedor principal de la vista de gestión de áreas -->
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Listado de Áreas Productivas</h2>
            <!-- Botón para abrir el modal de creación de nueva área -->
            <button @click="isCreateModalOpen = true" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200 shadow-md">
                Crear Nueva Área
            </button>
        </div>

        <!-- Tabla de Áreas Productivas -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Detalles de las Áreas</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- Encabezados de la tabla -->
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Iteración sobre cada área para mostrar sus datos -->
                            @foreach($areas as $area)
                                <tr class="hover:bg-gray-100 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $area->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $area->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $area->description }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <!-- Botón para abrir el modal de edición, pasando los datos del área actual -->
                                        <button @click="openEditModal({{ $area->id }}, @json($area->name), @json($area->description))" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <!-- Formulario para eliminar un área -->
                                        <form action="{{ route('infrastock.admin.areas.destroy', $area->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta área?');">
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
        <div x-data="{
            isCreateModalOpen: false, // Estado del modal de creación (abierto/cerrado).
            isEditModalOpen: false,   // Estado del modal de edición (abierto/cerrado).
            currentArea: { id: null, name: '', description: '' }, // Datos del área que se está editando.
            validationErrors: {}, // Almacena los errores de validación recibidos del servidor.
            createForm: { name: '', description: '' }, // Datos del formulario de creación.

            init() {
                // Si hay errores de validación del servidor al cargar la página, abre el modal de creación
                // y precarga los datos `old()` si existen.
                @if($errors->hasAny(['name', 'description']))
                    this.isCreateModalOpen = true;
                    this.validationErrors = @json($errors->messages());
                    this.createForm.name = '{{ old('name') }}';
                    this.createForm.description = '{{ old('description') }}';
                @endif
            },

            /**
             * Abre el modal de edición y carga los datos del área seleccionada.
             * @param int id ID del área a editar.
             * @param string name Nombre del área a editar.
             * @param string description Descripción del área a editar.
             */
            openEditModal(id, name, description) {
                this.isEditModalOpen = true;
                this.currentArea.id = id;
                this.currentArea.name = name;
                this.currentArea.description = description;
                this.validationErrors = {}; // Limpia errores de validación previos.
            },

            /**
             * Reinicia el formulario de creación, limpiando los campos y los errores de validación.
             */
            resetCreateForm() {
                this.createForm.name = '';
                this.createForm.description = '';
                this.validationErrors = {}; // Limpia errores de validación de Alpine.
            },

            /**
             * Envía el formulario de creación de área de forma asíncrona (AJAX).
             * Maneja la respuesta del servidor, mostrando mensajes de éxito o errores de validación.
             */
            async createArea() {
                try {
                    const formData = new FormData(this.$refs.createForm);
                    const response = await fetch('{{ route('infrastock.admin.areas.store') }}', {
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
                            text: 'Área creada correctamente.',
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
                            text: 'Hubo un problema al crear el área.'
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
             * Envía el formulario de actualización de área de forma asíncrona (AJAX).
             * Maneja la respuesta del servidor, mostrando mensajes de éxito o errores de validación.
             */
            async updateArea() {
                try {
                    const formData = new FormData(this.$refs.editForm);
                    formData.append('_method', 'PUT'); // Simula el método PUT para Laravel.
                    const response = await fetch('{{ route('infrastock.admin.areas.update', '') }}' + this.currentArea.id, {
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
                            text: 'Área actualizada correctamente.',
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
                            text: 'Hubo un problema al actualizar el área.'
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
        }">
            <!-- Modal de Creación de Área -->
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                <div @click.away="isCreateModalOpen = false; resetCreateForm();" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Crear Nueva Área Productiva</h3>
                        <!-- Botón para cerrar el modal de creación -->
                        <button @click="isCreateModalOpen = false; resetCreateForm();" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <!-- Formulario de creación de área -->
                    <form @submit.prevent="createArea" x-ref="createForm">
                        @csrf
                        <div class="mb-4">
                            <label for="create_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Área:</label>
                            <input type="text" name="name" id="create_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.name}" x-model="createForm.name" required>
                            <!-- Muestra el error de validación para el campo 'name' -->
                            <p class="text-red-500 text-xs italic mt-2" id="create_name_error" x-text="validationErrors.name ? validationErrors.name[0] : ''"></p>
                        </div>
                        <div class="mb-6">
                            <label for="create_description" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                            <textarea name="description" id="create_description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.description}" x-model="createForm.description"></textarea>
                            <!-- Muestra el error de validación para el campo 'description' -->
                            <p class="text-red-500 text-xs italic mt-2" id="create_description_error" x-text="validationErrors.description ? validationErrors.description[0] : ''"></p>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <!-- Botones de cancelar y guardar para el modal de creación -->
                            <button type="button" @click="isCreateModalOpen = false; resetCreateForm();" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Guardar Área</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición de Área -->
            <div x-show="isEditModalOpen" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
                <div @click.away="isEditModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Área Productiva</h3>
                        <!-- Botón para cerrar el modal de edición -->
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <!-- Formulario de edición de área -->
                    <form @submit.prevent="updateArea" x-ref="editForm">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="edit_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Área:</label>
                            <input type="text" name="name" id="edit_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.name}" x-model="currentArea.name" required>
                            <!-- Muestra el error de validación para el campo 'name' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.name ? validationErrors.name[0] : ''"></p>
                        </div>
                        <div class="mb-6">
                            <label for="edit_description" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                            <textarea name="description" id="edit_description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.description}" x-model="currentArea.description"></textarea>
                            <!-- Muestra el error de validación para el campo 'description' -->
                            <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.description ? validationErrors.description[0] : ''"></p>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <!-- Botones de cancelar y actualizar para el modal de edición -->
                            <button type="button" @click="isEditModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Actualizar Área</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    // Este bloque de script puede usarse para inicializaciones globales o scripts que no son parte de un componente Alpine.js.
    // La lógica de `areaCrudModals` está definida directamente en el atributo `x-data` del div principal.
</script>
@endsection
