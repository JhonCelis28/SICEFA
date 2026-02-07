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
        currentTool: { id: null, nombre: '', imagen: '', placa: '', descripcion: '', marca: '', modelo: '', categoria_id: '', category_id: '', estado: 'disponible', cantidad_total: '', cantidad_disponible: '', fecha_mantenimiento: '', proximo_mantenimiento: '', fecha_adquisicion: '', descripcion_mantenimiento: '', amount: '' },
        validationErrors: {},
        createForm: { nombre: '', imagen: '', placa: '', descripcion: '', descripcion_actual: '', marca: '', modelo: '', categoria_id: '', category_id: '', estado: 'disponible', cantidad_total: '', cantidad_disponible: '', fecha_mantenimiento: '', proximo_mantenimiento: '', fecha_adquisicion: '', atributos: '', descripcion_mantenimiento: '', inventory_id: '', labor_id: '', amount: '', price: '' },

        init() {
            // Solo abrir modal si hay errores de validación del servidor
            @if($errors->any() || session('error'))
                this.isCreateModalOpen = true;
                this.validationErrors = @json($errors->messages());
                const oldData = @json(old());
                this.createForm.nombre = oldData.nombre || '';
                this.createForm.imagen = oldData.imagen || '';
                this.createForm.placa = oldData.placa || '';
                this.createForm.descripcion = oldData.descripcion || '';
                this.createForm.descripcion_actual = oldData.descripcion_actual || '';
                this.createForm.marca = oldData.marca || '';
                this.createForm.modelo = oldData.modelo || '';
                this.createForm.categoria_id = oldData.categoria_id || oldData.category_id || '';
                this.createForm.category_id = oldData.category_id || '';
                this.createForm.estado = oldData.estado || 'disponible';
                this.createForm.cantidad_total = oldData.cantidad_total || '';
                this.createForm.cantidad_disponible = oldData.cantidad_disponible || '';
                this.createForm.fecha_mantenimiento = oldData.fecha_mantenimiento || '';
                this.createForm.proximo_mantenimiento = oldData.proximo_mantenimiento || '';
                this.createForm.fecha_adquisicion = oldData.fecha_adquisicion || '';
                this.createForm.atributos = oldData.atributos || '';
                this.createForm.descripcion_mantenimiento = oldData.descripcion_mantenimiento || '';
                this.createForm.inventory_id = oldData.inventory_id || '';
                this.createForm.labor_id = oldData.labor_id || '';
                this.createForm.amount = oldData.amount || '';
                this.createForm.price = oldData.price || '';
                if ('{{ session('error') }}') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '{{ session('error') }}',
                        confirmButtonText: 'Entendido'
                    });
                }
            @endif
        },

        openCreateModal() {
            this.isCreateModalOpen = true;
            this.resetCreateForm();
            // Esperar a que el modal se renderice y luego agregar el listener
            this.$nextTick(() => {
                const form = document.getElementById('createToolForm');
                if (form) {
                    // Remover listeners previos si existen
                    const newForm = form.cloneNode(true);
                    form.parentNode.replaceChild(newForm, form);
                    
                    // Agregar nuestro listener
                    newForm.addEventListener('submit', function(e) {
                        console.log('Submit interceptado desde Alpine.js');
                        e.preventDefault();
                        e.stopPropagation();
                        e.stopImmediatePropagation();
                        handleFormSubmit(e);
                        return false;
                    }, true);
                }
            });
        },

        openEditModal(id, nombre, imagen, placa, descripcion, marca, modelo, categoria_id, category_id, estado, cantidad_total, cantidad_disponible, fecha_mantenimiento, proximo_mantenimiento, fecha_adquisicion, descripcion_mantenimiento, amount) {
            this.isEditModalOpen = true;
            this.currentTool = { 
                id: id, 
                nombre: nombre || '', 
                imagen: imagen || '', 
                placa: placa || '', 
                descripcion: descripcion || '', 
                marca: marca || '', 
                modelo: modelo || '', 
                categoria_id: categoria_id || category_id || '', 
                category_id: category_id || '', 
                estado: estado || 'disponible', 
                cantidad_total: cantidad_total || '', 
                cantidad_disponible: cantidad_disponible || '', 
                fecha_mantenimiento: fecha_mantenimiento || '', 
                proximo_mantenimiento: proximo_mantenimiento || '', 
                fecha_adquisicion: fecha_adquisicion || '', 
                descripcion_mantenimiento: descripcion_mantenimiento || '', 
                amount: amount || '' 
            };
            this.validationErrors = {};
        },

        closeModals() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
            this.validationErrors = {};
        },

        resetCreateForm() {
            this.createForm = { nombre: '', imagen: '', placa: '', descripcion: '', descripcion_actual: '', marca: '', modelo: '', categoria_id: '', category_id: '', estado: 'disponible', cantidad_total: '', cantidad_disponible: '', fecha_mantenimiento: '', proximo_mantenimiento: '', fecha_adquisicion: '', atributos: '', descripcion_mantenimiento: '', inventory_id: '', labor_id: '', amount: '', price: '' };
            this.validationErrors = {};
        },

    }">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-6">
                <div class="flex items-center space-x-2">
                    <button @click="openCreateModal()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 flex items-center">
                        <i class="fas fa-plus mr-2"></i> Registrar Nueva Herramienta
                    </button>
                </div>
                <div class="flex items-center space-x-2">
                    <!-- Botón de exportación PDF -->
                    <a href="{{ route('infrastock.admin.tools.export.pdf') }}" 
                       class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200 flex items-center"
                       title="Exportar Herramientas a PDF">
                        <i class="fas fa-file-pdf"></i>
                    </a>
                    <!-- Botón de exportación Excel -->
                    <a href="{{ route('infrastock.admin.tools.export.excel') }}" 
                       class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors duration-200 flex items-center"
                       title="Exportar Herramientas a Excel">
                        <i class="fas fa-file-excel"></i>
                    </a>
                </div>
            </div>

            <!-- Filtro de búsqueda automático -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="searchInput"
                               value="{{ request('search') }}"
                               placeholder="Buscar por nombre, placa, descripción, marca, modelo, categoría, estado..." 
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
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $tools->firstItem() ?? 0 }} - {{ $tools->lastItem() ?? 0 }} de {{ $tools->total() }} registros
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Imagen</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nombre</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Placa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Descripción</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Marca</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Modelo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Categoría</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cant. Total</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cant. Disponible</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha Adquisición</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($tools as $tool)
                                    <tr class="hover:bg-gray-100 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            @if($tool->imagen && \Storage::disk('public')->exists($tool->imagen))
                                                <img src="{{ asset('storage/' . $tool->imagen) }}" 
                                                     alt="{{ $tool->nombre ?? 'Herramienta' }}" 
                                                     class="h-10 w-10 object-cover rounded"
                                                     onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'40\' height=\'40\'%3E%3Crect width=\'40\' height=\'40\' fill=\'%23e5e7eb\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-size=\'12\'%3EN/A%3C/text%3E%3C/svg%3E';">
                                            @else
                                                <div class="h-10 w-10 bg-gray-200 rounded flex items-center justify-center">
                                                    <span class="text-gray-400 text-xs">N/A</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $tool->nombre ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->placa ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ Str::limit($tool->descripcion ?? 'N/A', 30) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->marca ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->modelo ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->category->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php
                                                $estadoColors = [
                                                    'disponible' => 'bg-green-100 text-green-800',
                                                    'en_prestamo' => 'bg-yellow-100 text-yellow-800',
                                                    'mantenimiento' => 'bg-orange-100 text-orange-800',
                                                    'no_disponible' => 'bg-red-100 text-red-800'
                                                ];
                                                $estadoLabels = [
                                                    'disponible' => 'Disponible',
                                                    'en_prestamo' => 'En Préstamo',
                                                    'mantenimiento' => 'Mantenimiento',
                                                    'no_disponible' => 'No Disponible'
                                                ];
                                                $color = $estadoColors[$tool->estado ?? 'disponible'] ?? 'bg-gray-100 text-gray-800';
                                                $label = $estadoLabels[$tool->estado ?? 'disponible'] ?? 'N/A';
                                            @endphp
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $color }}">{{ $label }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->cantidad_total ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->cantidad_disponible ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $tool->fecha_adquisicion ? \Carbon\Carbon::parse($tool->fecha_adquisicion)->format('d/m/Y') : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <!-- Botón para editar -->
                                                <button @click="openEditModal({{ $tool->id }}, '{{ addslashes($tool->nombre ?? '') }}', '{{ addslashes($tool->imagen ?? '') }}', '{{ addslashes($tool->placa ?? '') }}', '{{ addslashes($tool->descripcion ?? '') }}', '{{ addslashes($tool->marca ?? '') }}', '{{ addslashes($tool->modelo ?? '') }}', {{ $tool->categoria_id ?? $tool->category_id ?? 'null' }}, {{ $tool->category_id ?? 'null' }}, '{{ $tool->estado ?? 'disponible' }}', {{ $tool->cantidad_total ?? 'null' }}, {{ $tool->cantidad_disponible ?? 'null' }}, '{{ $tool->fecha_mantenimiento ?? '' }}', '{{ $tool->proximo_mantenimiento ?? '' }}', '{{ $tool->fecha_adquisicion ?? '' }}', '{{ addslashes($tool->descripcion_mantenimiento ?? '') }}', {{ $tool->amount ?? 'null' }})" class="text-yellow-600 hover:text-yellow-900 p-2 rounded hover:bg-yellow-50 transition-colors" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <!-- Botón para eliminar -->
                                                <form method="POST" action="{{ route('infrastock.admin.tools.destroy', $tool->id) }}" style="display: inline;" onsubmit="return confirmDeleteSync('{{ addslashes($tool->nombre ?? 'Herramienta') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 p-2 rounded hover:bg-red-50 transition-colors" title="Eliminar">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
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
            <div x-show="isCreateModalOpen" 
                 x-cloak 
                 @keydown.escape.window="isCreateModalOpen = false; resetCreateForm();"
                 class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" 
                 style="display: none;"
                 @click.self="isCreateModalOpen = false; resetCreateForm();">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-auto p-6 max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Registrar Nueva Herramienta</h3>
                        <button @click="isCreateModalOpen = false; resetCreateForm();" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form id="createToolForm" onsubmit="return false;" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="create_nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre: <span class="text-red-500">*</span></label>
                                <input type="text" name="nombre" id="create_nombre" x-model="createForm.nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" :class="validationErrors.nombre ? 'border-red-500' : ''" required>
                                <template x-if="validationErrors.nombre">
                                    <p class="text-red-500 text-xs mt-1" x-text="validationErrors.nombre[0]"></p>
                                </template>
                            </div>
                            <div class="mb-4">
                                <label for="create_placa" class="block text-gray-700 text-sm font-bold mb-2">Placa:</label>
                                <input type="text" name="placa" id="create_placa" value="{{ old('placa') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('placa') border-red-500 @enderror">
                                @error('placa')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="create_imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen:</label>
                            <input type="file" name="imagen" id="create_imagen" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('imagen') border-red-500 @enderror">
                            @error('imagen')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="create_descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                            <textarea name="descripcion" id="create_descripcion" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descripcion') border-red-500 @enderror">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="create_marca" class="block text-gray-700 text-sm font-bold mb-2">Marca:</label>
                                <input type="text" name="marca" id="create_marca" value="{{ old('marca') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('marca') border-red-500 @enderror">
                                @error('marca')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="create_modelo" class="block text-gray-700 text-sm font-bold mb-2">Modelo:</label>
                                <input type="text" name="modelo" id="create_modelo" value="{{ old('modelo') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('modelo') border-red-500 @enderror">
                                @error('modelo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="create_category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                                <select name="category_id" id="create_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category_id') border-red-500 @enderror">
                                    <option value="">Seleccione una categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="create_estado" class="block text-gray-700 text-sm font-bold mb-2">Estado:</label>
                                <select name="estado" id="create_estado" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('estado') border-red-500 @enderror">
                                    <option value="disponible" {{ old('estado', 'disponible') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                    <option value="en_prestamo" {{ old('estado') == 'en_prestamo' ? 'selected' : '' }}>En Préstamo</option>
                                    <option value="mantenimiento" {{ old('estado') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                                    <option value="no_disponible" {{ old('estado') == 'no_disponible' ? 'selected' : '' }}>No Disponible</option>
                                </select>
                                @error('estado')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="create_cantidad_total" class="block text-gray-700 text-sm font-bold mb-2">Cantidad Total:</label>
                                <input type="number" name="cantidad_total" id="create_cantidad_total" value="{{ old('cantidad_total') }}" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cantidad_total') border-red-500 @enderror">
                                @error('cantidad_total')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="create_cantidad_disponible" class="block text-gray-700 text-sm font-bold mb-2">Cantidad Disponible:</label>
                                <input type="number" name="cantidad_disponible" id="create_cantidad_disponible" value="{{ old('cantidad_disponible') }}" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cantidad_disponible') border-red-500 @enderror">
                                @error('cantidad_disponible')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="create_fecha_adquisicion" class="block text-gray-700 text-sm font-bold mb-2">Fecha de Adquisición:</label>
                                <input type="date" name="fecha_adquisicion" id="create_fecha_adquisicion" value="{{ old('fecha_adquisicion') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('fecha_adquisicion') border-red-500 @enderror">
                                @error('fecha_adquisicion')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="create_fecha_mantenimiento" class="block text-gray-700 text-sm font-bold mb-2">Fecha Mantenimiento:</label>
                                <input type="date" name="fecha_mantenimiento" id="create_fecha_mantenimiento" value="{{ old('fecha_mantenimiento') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('fecha_mantenimiento') border-red-500 @enderror">
                                @error('fecha_mantenimiento')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="create_proximo_mantenimiento" class="block text-gray-700 text-sm font-bold mb-2">Próximo Mantenimiento:</label>
                            <input type="date" name="proximo_mantenimiento" id="create_proximo_mantenimiento" value="{{ old('proximo_mantenimiento') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('proximo_mantenimiento') border-red-500 @enderror">
                            @error('proximo_mantenimiento')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="create_descripcion_mantenimiento" class="block text-gray-700 text-sm font-bold mb-2">Descripción de Mantenimiento:</label>
                            <textarea name="descripcion_mantenimiento" id="create_descripcion_mantenimiento" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descripcion_mantenimiento') border-red-500 @enderror">{{ old('descripcion_mantenimiento') }}</textarea>
                            @error('descripcion_mantenimiento')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="create_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad:</label>
                                <input type="number" name="amount" id="create_amount" value="{{ old('amount') }}" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('amount') border-red-500 @enderror">
                                @error('amount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        
                        </div>
                        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                            <button type="button" @click="isCreateModalOpen = false; resetCreateForm();" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="button" id="submitToolBtn" onclick="submitToolForm()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Guardar Herramienta</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición de Herramienta -->
            <div x-show="isEditModalOpen" 
                 x-cloak 
                 @keydown.escape.window="isEditModalOpen = false"
                 class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" 
                 style="display: none;"
                 @click.self="isEditModalOpen = false">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-auto p-6 max-h-[90vh] overflow-y-auto" @click.stop>
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Herramienta</h3>
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" :action="`{{ route('infrastock.admin.tools.update', '') }}/${currentTool.id}`" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="edit_nombre" class="block text-gray-700 text-sm font-bold mb-2">Nombre: <span class="text-red-500">*</span></label>
                                <input type="text" name="nombre" id="edit_nombre" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('nombre') border-red-500 @enderror" x-model="currentTool.nombre" required>
                                @error('nombre')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="edit_placa" class="block text-gray-700 text-sm font-bold mb-2">Placa:</label>
                                <input type="text" name="placa" id="edit_placa" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('placa') border-red-500 @enderror" x-model="currentTool.placa">
                                @error('placa')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="edit_imagen" class="block text-gray-700 text-sm font-bold mb-2">Imagen:</label>
                            <input type="file" name="imagen" id="edit_imagen" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('imagen') border-red-500 @enderror">
                            <template x-if="currentTool.imagen">
                                <div class="mt-2">
                                    <img :src="`{{ asset('storage/') }}/${currentTool.imagen}`" alt="Imagen actual" class="h-20 w-20 object-cover rounded">
                                </div>
                            </template>
                            @error('imagen')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_descripcion" class="block text-gray-700 text-sm font-bold mb-2">Descripción:</label>
                            <textarea name="descripcion" id="edit_descripcion" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descripcion') border-red-500 @enderror" x-model="currentTool.descripcion"></textarea>
                            @error('descripcion')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="edit_marca" class="block text-gray-700 text-sm font-bold mb-2">Marca:</label>
                                <input type="text" name="marca" id="edit_marca" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('marca') border-red-500 @enderror" x-model="currentTool.marca">
                                @error('marca')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="edit_modelo" class="block text-gray-700 text-sm font-bold mb-2">Modelo:</label>
                                <input type="text" name="modelo" id="edit_modelo" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('modelo') border-red-500 @enderror" x-model="currentTool.modelo">
                                @error('modelo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="edit_category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría:</label>
                                <select name="category_id" id="edit_category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('category_id') border-red-500 @enderror" x-model="currentTool.category_id">
                                    <option value="">Seleccione una categoría</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="edit_estado" class="block text-gray-700 text-sm font-bold mb-2">Estado:</label>
                                <select name="estado" id="edit_estado" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('estado') border-red-500 @enderror" x-model="currentTool.estado">
                                    <option value="disponible">Disponible</option>
                                    <option value="en_prestamo">En Préstamo</option>
                                    <option value="mantenimiento">Mantenimiento</option>
                                    <option value="no_disponible">No Disponible</option>
                                </select>
                                @error('estado')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="edit_cantidad_total" class="block text-gray-700 text-sm font-bold mb-2">Cantidad Total:</label>
                                <input type="number" name="cantidad_total" id="edit_cantidad_total" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cantidad_total') border-red-500 @enderror" x-model="currentTool.cantidad_total">
                                @error('cantidad_total')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="edit_cantidad_disponible" class="block text-gray-700 text-sm font-bold mb-2">Cantidad Disponible:</label>
                                <input type="number" name="cantidad_disponible" id="edit_cantidad_disponible" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('cantidad_disponible') border-red-500 @enderror" x-model="currentTool.cantidad_disponible">
                                @error('cantidad_disponible')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="edit_fecha_adquisicion" class="block text-gray-700 text-sm font-bold mb-2">Fecha de Adquisición:</label>
                                <input type="date" name="fecha_adquisicion" id="edit_fecha_adquisicion" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('fecha_adquisicion') border-red-500 @enderror" x-model="currentTool.fecha_adquisicion">
                                @error('fecha_adquisicion')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="edit_fecha_mantenimiento" class="block text-gray-700 text-sm font-bold mb-2">Fecha Mantenimiento:</label>
                                <input type="date" name="fecha_mantenimiento" id="edit_fecha_mantenimiento" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('fecha_mantenimiento') border-red-500 @enderror" x-model="currentTool.fecha_mantenimiento">
                                @error('fecha_mantenimiento')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="edit_proximo_mantenimiento" class="block text-gray-700 text-sm font-bold mb-2">Próximo Mantenimiento:</label>
                            <input type="date" name="proximo_mantenimiento" id="edit_proximo_mantenimiento" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('proximo_mantenimiento') border-red-500 @enderror" x-model="currentTool.proximo_mantenimiento">
                            @error('proximo_mantenimiento')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_descripcion_mantenimiento" class="block text-gray-700 text-sm font-bold mb-2">Descripción de Mantenimiento:</label>
                            <textarea name="descripcion_mantenimiento" id="edit_descripcion_mantenimiento" rows="2" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('descripcion_mantenimiento') border-red-500 @enderror" x-model="currentTool.descripcion_mantenimiento"></textarea>
                            @error('descripcion_mantenimiento')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad (Amount):</label>
                            <input type="number" name="amount" id="edit_amount" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('amount') border-red-500 @enderror" x-model="currentTool.amount">
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
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
// Función para enviar el formulario directamente (sin evento submit)
async function submitToolForm() {
    console.log('submitToolForm llamado directamente');
    
    const form = document.getElementById('createToolForm');
    if (!form) {
        console.error('Formulario no encontrado');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo encontrar el formulario.',
            confirmButtonText: 'Entendido'
        });
        return false;
    }
    
    // Validar campos requeridos antes de enviar
    const nombreField = form.querySelector('[name="nombre"]');
    if (nombreField && !nombreField.value.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Campo requerido',
            text: 'El campo Nombre es obligatorio.',
            confirmButtonText: 'Entendido'
        });
        nombreField.focus();
        return false;
    }
    
    const formData = new FormData(form);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                           document.querySelector('input[name="_token"]')?.value);
    
    // Mostrar loading
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espera',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const actionUrl = '{{ route('infrastock.admin.tools.store') }}';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value;
        
        console.log('Enviando petición AJAX a:', actionUrl);
        
        const response = await fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        });
        
        console.log('Respuesta recibida:', response.status, response.statusText);
        
        // Verificar si la respuesta es JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            console.error('El servidor no devolvió JSON. Content-Type:', contentType);
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'El servidor no devolvió una respuesta JSON válida. La página podría recargarse.',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Éxito
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: data.message || 'Herramienta registrada exitosamente.',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                // Recargar página para ver el nuevo registro
                window.location.reload();
            });
        } else {
            // Errores de validación
            Swal.close();
            if (data.errors) {
                // Mostrar primer error
                const firstErrorKey = Object.keys(data.errors)[0];
                const firstError = data.errors[firstErrorKey][0];
                Swal.fire({
                    icon: 'error',
                    title: 'Error de validación',
                    text: firstError,
                    confirmButtonText: 'Entendido'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Hubo un error al registrar la herramienta.',
                    confirmButtonText: 'Entendido'
                });
            }
        }
        } catch (error) {
            console.error('Error en handleFormSubmit:', error);
            Swal.close();
            
            // Si el error es porque la respuesta no es JSON, podría ser una redirección
            if (error.message && error.message.includes('JSON')) {
                console.error('El servidor no devolvió JSON. Posible redirección.');
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'El servidor redirigió la página. Esto no debería pasar con AJAX.',
                    confirmButtonText: 'Entendido'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un error al procesar la solicitud. Por favor, intenta nuevamente.',
                    confirmButtonText: 'Entendido'
                });
            }
        }
    
    return false;
}

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
    
    // Usar MutationObserver para detectar cuando el formulario se agrega al DOM
    const observer = new MutationObserver(function(mutations) {
        const createForm = document.getElementById('createToolForm');
        if (createForm && !createForm.hasAttribute('data-listener-added')) {
            createForm.setAttribute('data-listener-added', 'true');
            
            // Interceptar el submit en la fase de captura
            createForm.addEventListener('submit', function(e) {
                console.log('Submit interceptado por MutationObserver');
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                handleFormSubmit(e);
                return false;
            }, true);
            
            // Prevenir envío por Enter
            createForm.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA' && e.target.tagName !== 'BUTTON') {
                    e.preventDefault();
                    console.log('Enter presionado - previniendo submit automático');
                }
            });
        }
    });
    
    // Observar cambios en el DOM
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
    
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