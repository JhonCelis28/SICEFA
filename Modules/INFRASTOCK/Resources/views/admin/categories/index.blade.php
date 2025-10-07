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
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Listado de Categorías</h2>
            <!-- Botón para abrir el modal de creación de nueva categoría -->
            <button @click="isCreateModalOpen = true" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200 shadow-md">
                Crear Nueva Categoría
            </button>
        </div>

        <!-- Tabla de Categorías -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Detalles de las Categorías</h3>
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $category->type }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <!-- Botón para abrir el modal de edición, pasando los datos de la categoría actual -->
                                        <button @click="openEditModal({{ $category->id }}, @json($category->name), @json($category->type))" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                            <i class="fas fa-edit"></i> Editar
                                        </button>
                                        <!-- Formulario para eliminar una categoría -->
                                        <form action="{{ route('infrastock.admin.categories.destroy', $category->id) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta categoría?');">
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

    </div>
@endsection

@section('script')
<!-- Script Alpine.js para la gestión de modales de creación y edición -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('categoryCrudModals', () => ({
            isCreateModalOpen: false, // Estado del modal de creación (abierto/cerrado).
            isEditModalOpen: false,   // Estado del modal de edición (abierto/cerrado).
            currentCategory: { id: null, name: '', type: '' }, // Datos de la categoría que se está editando.
            validationErrors: {}, // Almacena los errores de validación recibidos del servidor.
            createForm: { name: '', type: '' }, // Datos del formulario de creación.

            init() {
                // Recupera los errores de validación del servidor y los datos `old()` para el formulario de creación.
                const serverErrors = @json($errors->toArray());
                const oldName = '{{ old('name') }}'; // Uso directo de Blade para old() en strings.
                const oldType = '{{ old('type') }}'; // Uso directo de Blade para old() en strings.

                // Si hay errores de validación, abre el modal de creación y precarga los datos.
                if (Object.keys(serverErrors).length > 0) {
                    this.isCreateModalOpen = true;
                    this.validationErrors = serverErrors;
                    this.createForm.name = oldName;
                    this.createForm.type = oldType;
                }
            },

            /**
             * Abre el modal de edición y carga los datos de la categoría seleccionada.
             * @param int id ID de la categoría a editar.
             * @param string name Nombre de la categoría a editar.
             * @param string type Tipo de la categoría a editar (supply/tool).
             */
            openEditModal(id, name, type) {
                this.isEditModalOpen = true;
                this.currentCategory.id = id;
                this.currentCategory.name = name;
                this.currentCategory.type = type;
                this.validationErrors = {}; // Limpia errores de validación previos.
            },

            /**
             * Reinicia el formulario de creación, limpiando los campos y los errores de validación.
             */
            resetCreateForm() {
                this.createForm.name = '';
                this.createForm.type = '';
                this.validationErrors = {}; // Limpia errores de validación de Alpine.
            },

            /**
             * Envía el formulario de creación de categoría de forma asíncrona (AJAX).
             * Maneja la respuesta del servidor, mostrando mensajes de éxito o errores de validación.
             */
            async createCategory() {
                try {
                    const formData = new FormData(this.$refs.createForm);
                    const response = await fetch('{{ route('infrastock.admin.categories.store') }}', {
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
                            text: 'Categoría creada correctamente.',
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
                            text: 'Hubo un problema al crear la categoría.'
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
             * Envía el formulario de actualización de categoría de forma asíncrona (AJAX).
             * Maneja la respuesta del servidor, mostrando mensajes de éxito o errores de validación.
             */
            async updateCategory() {
                try {
                    const formData = new FormData(this.$refs.editForm);
                    formData.append('_method', 'PUT'); // Simula el método PUT para Laravel.
                    const response = await fetch('{{ route('infrastock.admin.categories.update', '') }}' + this.currentCategory.id, {
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
                            text: 'Categoría actualizada correctamente.',
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
                            text: 'Hubo un problema al actualizar la categoría.'
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

<!-- Contenedor para el modal de creación (fuera del x-data principal) -->
<div x-data="categoryCrudModals">
    <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
        <div @click.away="isCreateModalOpen = false; resetCreateForm();" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800">Crear Nueva Categoría</h3>
                <!-- Botón para cerrar el modal de creación -->
                <button @click="isCreateModalOpen = false; resetCreateForm();" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
            </div>
            <!-- Formulario de creación de categoría -->
            <form @submit.prevent="createCategory" x-ref="createForm">
                @csrf
                <div class="mb-4">
                    <label for="create_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre de la Categoría:</label>
                    <input type="text" name="name" id="create_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.name}" x-model="createForm.name" required>
                    <!-- Muestra el error de validación para el campo 'name' -->
                    <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.name ? validationErrors.name[0] : ''"></p>
                </div>
                <div class="mb-6">
                    <label for="create_type" class="block text-gray-700 text-sm font-bold mb-2">Tipo:</label>
                    <select name="type" id="create_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.type}" x-model="createForm.type" required>
                        <option value="">Selecciona un tipo</option>
                        <option value="supply">Insumo</option>
                        <option value="tool">Herramienta</option>
                    </select>
                    <!-- Muestra el error de validación para el campo 'type' -->
                    <p class="text-red-500 text-xs italic mt-2" id="create_type_error" x-text="validationErrors.type ? validationErrors.type[0] : ''"></p>
                </div>
                <div class="flex justify-end space-x-4">
                    <!-- Botones de cancelar y guardar para el modal de creación -->
                    <button type="button" @click="isCreateModalOpen = false; resetCreateForm();" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Contenedor para el modal de edición (fuera del x-data principal) -->
    <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
        <div @click.away="isEditModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-2xl font-bold text-gray-800">Editar Categoría</h3>
                <!-- Botón para cerrar el modal de edición -->
                <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
            </div>
            <!-- Formulario de edición de categoría -->
            <form @submit.prevent="updateCategory" x-ref="editForm">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="edit_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre de la Categoría:</label>
                    <input type="text" name="name" id="edit_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.name}" x-model="currentCategory.name" required>
                    <!-- Muestra el error de validación para el campo 'name' -->
                    <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.name ? validationErrors.name[0] : ''"></p>
                </div>
                <div class="mb-6">
                    <label for="edit_type" class="block text-gray-700 text-sm font-bold mb-2">Tipo:</label>
                    <select name="type" id="edit_type" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="{'border-red-500': validationErrors.type}" x-model="currentCategory.type" required>
                        <option value="">Selecciona un tipo</option>
                        <option value="supply">Insumo</option>
                        <option value="tool">Herramienta</option>
                    </select>
                    <!-- Muestra el error de validación para el campo 'type' -->
                    <p class="text-red-500 text-xs italic mt-2" x-text="validationErrors.type ? validationErrors.type[0] : ''"></p>
                </div>
                <div class="flex justify-end space-x-4">
                    <!-- Botones de cancelar y actualizar para el modal de edición -->
                    <button type="button" @click="isEditModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Actualizar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
