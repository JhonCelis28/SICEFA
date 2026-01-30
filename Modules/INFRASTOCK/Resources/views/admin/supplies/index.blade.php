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
    @php
        $filteredEquipmentId = request('filter_equipment_id');
        $filterExpiring = request('filter_expiring', false);
        $isFiltered = !empty($filteredEquipmentId) || $filterExpiring;
    @endphp
    <div x-data="{
        isCreateModalOpen: false,
        isEditModalOpen: false,
        isLoanModalOpen: false,
        currentSupply: { id: null, name: '', category_id: '', characteristics: '', initial_amount: '', minimum_stock: '', unit_measure: '', unit_measure_other: '', observations: '', expiration_date: '' },
        currentLoan: { equipment_id: null, equipment_name: '', borrower_name: '', amount: '', loan_location: '', loan_date: '' },
        createUnitMeasure: '',
        createUnitMeasureOther: '',
        isFiltered: {{ $isFiltered ? 'true' : 'false' }},
        filteredEquipmentId: {{ $filteredEquipmentId ?? 'null' }},
        
        init() {
            console.log('Alpine.js inicializado correctamente');
        },
        
        openCreateModal() {
            console.log('Abriendo modal de creación');
            this.isCreateModalOpen = true;
            this.createUnitMeasure = '';
            this.createUnitMeasureOther = '';
        },
        
        openEditModal(id, name, category_id, characteristics, initial_amount, minimum_stock, unit_measure, observations, expiration_date) {
            console.log('Abriendo modal de edición:', { id, name, category_id, characteristics, initial_amount, minimum_stock, unit_measure, observations, expiration_date });
            this.isEditModalOpen = true;
            
            // Determinar si la unidad de medida es una de las predefinidas o personalizada
            const predefinedUnits = ['Galón', 'Bulto', 'Caja', 'Kilos', 'Paquete', 'Pliego', 'Rollo', 'Unidad'];
            const isPredefined = predefinedUnits.includes(unit_measure);
            
            this.currentSupply = { 
                id: id, 
                name: name || '', 
                category_id: category_id || '', 
                characteristics: characteristics || '', 
                initial_amount: initial_amount || '', 
                minimum_stock: minimum_stock || '',
                unit_measure: isPredefined ? (unit_measure || '') : 'otro',
                unit_measure_other: isPredefined ? '' : (unit_measure || ''),
                observations: observations || '',
                expiration_date: expiration_date || ''
            };
        },
        
        openLoanModal(id, name, stock) {
            console.log('Abriendo modal de préstamo:', { id, name, stock });
            this.isLoanModalOpen = true;
            this.currentLoan = {
                equipment_id: id,
                equipment_name: name,
                borrower_name: '',
                amount: '',
                loan_location: '',
                loan_date: new Date().toISOString().split('T')[0]
            };
        },
        
        closeModals() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
            this.isLoanModalOpen = false;
            this.createUnitMeasure = '';
            this.createUnitMeasureOther = '';
        },
        
        getUnitMeasureValue() {
            if (this.createUnitMeasure === 'otro') {
                return this.createUnitMeasureOther;
            }
            return this.createUnitMeasure;
        },
        
        getEditUnitMeasureValue() {
            if (this.currentSupply.unit_measure === 'otro') {
                return this.currentSupply.unit_measure_other;
            }
            return this.currentSupply.unit_measure;
        }
    }">
    <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    @if($filterExpiring)
                        <div class="mt-2 flex items-center space-x-2">
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                <i class="fas fa-calendar-times mr-1"></i>Mostrando solo insumos próximos a vencer (30 días)
                            </span>
                            <a href="{{ route('infrastock.admin.supplies.index') }}" class="text-sm text-blue-600 hover:text-blue-800 underline">
                                <i class="fas fa-times mr-1"></i>Quitar filtro
                            </a>
                        </div>
                    @endif
                </div>
                <div class="flex space-x-2">
                    <button @click="openCreateModal()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                        <i class="fas fa-plus mr-2"></i>Registrar Nuevo Insumo
                    </button>
                </div>
            </div>

            <!-- Filtro de búsqueda automático -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="searchInput"
                               placeholder="Buscar por nombre, categoría, características..." 
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
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $supplies->firstItem() ?? 0 }} - {{ $supplies->lastItem() ?? 0 }} de {{ $supplies->total() }} registros
                        </div>
                    </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- Encabezados de la tabla -->
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nombre</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Categoría</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Características</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cantidad Inicial</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Consumos</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cantidad Restante</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Unidad Medida</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha de Vencimiento</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Observaciones</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Iteración sobre cada insumo para mostrar sus datos -->
                            @foreach($supplies as $supply)
                                <tr class="hover:bg-gray-100 transition-colors duration-150" data-equipment-id="{{ $supply->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-semibold">{{ $supply->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @php
                                            $categoryName = strtolower($supply->category->name ?? '');
                                            $badgeClass = 'px-2 py-1 text-xs font-semibold rounded-full ';
                                            if (strpos($categoryName, 'ferretería') !== false || strpos($categoryName, 'ferreteria') !== false) {
                                                $badgeClass .= 'bg-orange-100 text-orange-600';
                                            } elseif (strpos($categoryName, 'aseo') !== false) {
                                                $badgeClass .= 'bg-sky-100 text-sky-600';
                                            } else {
                                                $badgeClass .= 'bg-purple-100 text-purple-800';
                                            }
                                        @endphp
                                        <span class="{{ $badgeClass }}">
                                            {{ $supply->category->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                        <div class="truncate" title="{{ $supply->characteristics ?? 'N/A' }}">
                                            {{ $supply->characteristics ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $supply->initial_amount }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                            {{ $supply->used_amount }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $supply->stock }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $supply->status_color }}">
                                            <i class="fas 
                                                @if($supply->status == 'disponible') fa-check-circle 
                                                @elseif($supply->status == 'agotado') fa-times-circle 
                                                @elseif($supply->status == 'vencido') fa-exclamation-triangle 
                                                @elseif($supply->status == 'bajo_stock') fa-exclamation-circle 
                                                @elseif($supply->status == 'critico') fa-exclamation-triangle 
                                                @endif mr-1"></i>
                                            {{ $supply->status_text }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                            {{ $supply->unit_measure ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($supply->expiration_date)
                                            @php
                                                $daysUntilExpiration = now()->diffInDays($supply->expiration_date, false);
                                                $isExpired = $supply->expiration_date->isPast();
                                                $isExpiringSoon = !$isExpired && $daysUntilExpiration <= 30;
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $isExpired ? 'bg-red-100 text-red-800' : ($isExpiringSoon ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800') }}">
                                                {{ $supply->expiration_date->format('d/m/Y') }}
                                            </span>
                                            @if($isExpired)
                                                <div class="text-xs text-red-600 mt-1">Vencido</div>
                                            @elseif($isExpiringSoon)
                                                <div class="text-xs text-orange-600 mt-1">Por vencer ({{ $daysUntilExpiration }} días)</div>
                                            @endif
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                                N/A
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                        <div class="truncate" title="{{ $supply->observations ?? 'N/A' }}">
                                            {{ $supply->observations ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- Botón para abrir el modal de préstamo -->
                                            <button @click="openLoanModal({{ $supply->id }}, '{{ addslashes($supply->name) }}', {{ $supply->stock }})" class="text-blue-600 hover:text-blue-900 p-2 rounded hover:bg-blue-50 transition-colors" title="Registrar Préstamo">
                                                <i class="fas fa-hand-holding"></i>
                                            </button>
                                            <!-- Botón para abrir el modal de edición, pasando los datos del insumo actual -->
                                            <button @click="openEditModal({{ $supply->id }}, '{{ addslashes($supply->name) }}', {{ $supply->category_id ?? 'null' }}, '{{ addslashes($supply->characteristics ?? '') }}', {{ $supply->initial_amount }}, {{ $supply->minimum_stock ?? 0 }}, '{{ addslashes($supply->unit_measure ?? '') }}', '{{ addslashes($supply->observations ?? '') }}', '{{ $supply->expiration_date ? $supply->expiration_date->format('Y-m-d') : '' }}')" class="text-yellow-600 hover:text-yellow-900 p-2 rounded hover:bg-yellow-50 transition-colors" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <!-- Botón para eliminar un insumo -->
                                            <button type="button" onclick="confirmDeleteSync('{{ $supply->id }}', '{{ addslashes($supply->name) }}')" class="text-red-600 hover:text-red-900 p-2 rounded hover:bg-red-50 transition-colors" title="Eliminar">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
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
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6 my-8 max-h-[90vh] overflow-y-auto">
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
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Insumo: <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') border-red-500 @enderror" required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría: <span class="text-red-500">*</span></label>
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
                        <div class="mb-4">
                            <label for="characteristics" class="block text-gray-700 text-sm font-bold mb-2">Características:</label>
                            <textarea name="characteristics" id="characteristics" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('characteristics') border-red-500 @enderror">{{ old('characteristics') }}</textarea>
                            @error('characteristics')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="initial_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad Inicial: <span class="text-red-500">*</span></label>
                            <input type="number" name="initial_amount" id="initial_amount" value="{{ old('initial_amount') }}" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('initial_amount') border-red-500 @enderror" required>
                            @error('initial_amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="minimum_stock" class="block text-gray-700 text-sm font-bold mb-2">Valor Mínimo Permitido:</label>
                            <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock') }}" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('minimum_stock') border-red-500 @enderror" placeholder="Ej: 10">
                            <p class="text-gray-500 text-xs mt-1">Cantidad mínima de stock permitida para este insumo</p>
                            @error('minimum_stock')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="unit_measure" class="block text-gray-700 text-sm font-bold mb-2">Unidad de Medida:</label>
                            <select name="unit_measure" id="unit_measure" x-model="createUnitMeasure" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('unit_measure') border-red-500 @enderror">
                                <option value="">Seleccione una unidad</option>
                                <option value="Galón">Galón</option>
                                <option value="Bulto">Bulto</option>
                                <option value="Caja">Caja</option>
                                <option value="Kilos">Kilos</option>
                                <option value="Paquete">Paquete</option>
                                <option value="Pliego">Pliego</option>
                                <option value="Rollo">Rollo</option>
                                <option value="Unidad">Unidad</option>
                                <option value="otro">Otro</option>
                            </select>
                            <div x-show="createUnitMeasure === 'otro'" x-transition class="mt-2">
                                <input type="text" name="unit_measure_other" id="unit_measure_other" x-model="createUnitMeasureOther" placeholder="Ingrese la unidad de medida" maxlength="50" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <input type="hidden" name="unit_measure_final" :value="getUnitMeasureValue()">
                            @error('unit_measure')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="expiration_date" class="block text-gray-700 text-sm font-bold mb-2">Fecha de Vencimiento:</label>
                            <input type="date" name="expiration_date" id="expiration_date" value="{{ old('expiration_date') }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('expiration_date') border-red-500 @enderror">
                            <p class="text-xs text-gray-500 mt-1">Opcional. El sistema notificará cuando el insumo esté por vencer.</p>
                            @error('expiration_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label for="observations" class="block text-gray-700 text-sm font-bold mb-2">Observaciones:</label>
                            <textarea name="observations" id="observations" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('observations') border-red-500 @enderror">{{ old('observations') }}</textarea>
                            @error('observations')
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
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6 my-8 max-h-[90vh] overflow-y-auto">
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
                            <label for="edit_name" class="block text-gray-700 text-sm font-bold mb-2">Nombre del Insumo: <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="edit_name" x-model="currentSupply.name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_category_id" class="block text-gray-700 text-sm font-bold mb-2">Categoría: <span class="text-red-500">*</span></label>
                            <select name="category_id" id="edit_category_id" x-model="currentSupply.category_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                <option value="">Seleccione una categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_characteristics" class="block text-gray-700 text-sm font-bold mb-2">Características:</label>
                            <textarea name="characteristics" id="edit_characteristics" rows="3" x-model="currentSupply.characteristics" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="edit_initial_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad Inicial: <span class="text-red-500">*</span></label>
                            <input type="number" name="initial_amount" id="edit_initial_amount" x-model="currentSupply.initial_amount" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_minimum_stock" class="block text-gray-700 text-sm font-bold mb-2">Valor Mínimo Permitido:</label>
                            <input type="number" name="minimum_stock" id="edit_minimum_stock" x-model="currentSupply.minimum_stock" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Ej: 10">
                            <p class="text-gray-500 text-xs mt-1">Cantidad mínima de stock permitida para este insumo</p>
                        </div>
                        <div class="mb-4">
                            <label for="edit_unit_measure" class="block text-gray-700 text-sm font-bold mb-2">Unidad de Medida:</label>
                            <select name="unit_measure" id="edit_unit_measure" x-model="currentSupply.unit_measure" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Seleccione una unidad</option>
                                <option value="Galón">Galón</option>
                                <option value="Bulto">Bulto</option>
                                <option value="Caja">Caja</option>
                                <option value="Kilos">Kilos</option>
                                <option value="Paquete">Paquete</option>
                                <option value="Pliego">Pliego</option>
                                <option value="Rollo">Rollo</option>
                                <option value="Unidad">Unidad</option>
                                <option value="otro">Otro</option>
                            </select>
                            <div x-show="currentSupply.unit_measure === 'otro'" x-transition class="mt-2">
                                <input type="text" name="unit_measure_other" id="edit_unit_measure_other" x-model="currentSupply.unit_measure_other" placeholder="Ingrese la unidad de medida" maxlength="50" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            <input type="hidden" name="unit_measure_final" :value="getEditUnitMeasureValue()">
                        </div>
                        <div class="mb-4">
                            <label for="edit_expiration_date" class="block text-gray-700 text-sm font-bold mb-2">Fecha de Vencimiento:</label>
                            <input type="date" name="expiration_date" id="edit_expiration_date" x-model="currentSupply.expiration_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <p class="text-xs text-gray-500 mt-1">Opcional. El sistema notificará cuando el insumo esté por vencer.</p>
                        </div>
                        <div class="mb-6">
                            <label for="edit_observations" class="block text-gray-700 text-sm font-bold mb-2">Observaciones:</label>
                            <textarea name="observations" id="edit_observations" rows="3" x-model="currentSupply.observations" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="closeModals()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Actualizar Insumo</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Préstamo -->
            <div x-show="isLoanModalOpen" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto" style="display: none;">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6 my-8 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Registrar Préstamo</h3>
                        <button @click="closeModals()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Nota visible sobre préstamos -->
                    <div class="mb-4 p-3 bg-blue-50 border-l-4 border-blue-400 rounded">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                            <p class="text-sm text-blue-700 font-semibold">
                                <strong>Nota:</strong> Estos no cuentan como consumo ya que estos regresan a bodega.
                            </p>
                        </div>
                    </div>
                    
                    <!-- Formulario de préstamo -->
                    <form method="POST" action="{{ route('infrastock.admin.supplies.store-loan') }}">
                        @csrf
                        <input type="hidden" name="equipment_id" :value="currentLoan.equipment_id">
                        
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Insumo:</label>
                            <input type="text" :value="currentLoan.equipment_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100" readonly>
                        </div>
                        
                        <div class="mb-4">
                            <label for="loan_borrower_name" class="block text-gray-700 text-sm font-bold mb-2">Prestatario: <span class="text-red-500">*</span></label>
                            <input type="text" name="borrower_name" id="loan_borrower_name" x-model="currentLoan.borrower_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('borrower_name') border-red-500 @enderror" placeholder="Ingrese el nombre del prestatario" required>
                            @error('borrower_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="loan_location" class="block text-gray-700 text-sm font-bold mb-2">Lugar de Préstamo: <span class="text-red-500">*</span></label>
                            <input type="text" name="loan_location" id="loan_location" x-model="currentLoan.loan_location" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('loan_location') border-red-500 @enderror" placeholder="Ej: Área de producción, Oficina administrativa, etc." required>
                            @error('loan_location')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <input type="hidden" name="productive_unit_warehouse_id" value="{{ $productiveUnitWarehouses->first()->id ?? '' }}">
                        
                        <div class="mb-4">
                            <label for="loan_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad: <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" id="loan_amount" x-model="currentLoan.amount" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('amount') border-red-500 @enderror" required>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="mb-6">
                            <label for="loan_date" class="block text-gray-700 text-sm font-bold mb-2">Fecha: <span class="text-red-500">*</span></label>
                            <input type="date" name="loan_date" id="loan_date" x-model="currentLoan.loan_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('loan_date') border-red-500 @enderror" required>
                            @error('loan_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="closeModals()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                <i class="fas fa-hand-holding mr-2"></i>Registrar Préstamo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
// Función para confirmar eliminación con SweetAlert2 (versión AJAX)
function confirmDeleteSync(supplyId, supplyName) {
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
            
            // Enviar petición AJAX para eliminar
            fetch(`/infrastock/admin/supplies/${supplyId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (response.ok) {
                    return response.json();
                }
                throw new Error('Error al eliminar el insumo');
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: data.message || 'El insumo ha sido eliminado correctamente.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Recargar la página para actualizar la tabla
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo eliminar el insumo.'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al eliminar el insumo. Por favor, intenta nuevamente.'
                });
            });
        }
    });
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
    
    @if(session('success') && session('success') !== 'deleted')
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 2000
        });
    @endif
    
    // Configurar filtro automático
    setupAutoFilter();
    
    @if($filteredEquipmentId)
    // Si hay un filtro activo por equipment_id, hacer scroll a la fila
    setTimeout(function() {
        var row = document.querySelector('tr[data-equipment-id="{{ $filteredEquipmentId }}"]');
        if (row) {
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            row.classList.add('bg-yellow-50');
        }
    }, 100);
    @endif
});

// Función para configurar el filtro automático
function setupAutoFilter() {
    const searchInput = document.getElementById('searchInput');
    const table = document.querySelector('table tbody');
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        rows.forEach(row => {
            const nameCell = row.cells[0]; // Columna de nombre
            const categoryCell = row.cells[1]; // Columna de categoría
            const characteristicsCell = row.cells[2]; // Columna de características
            
            const nameText = nameCell ? nameCell.textContent.toLowerCase() : '';
            const categoryText = categoryCell ? categoryCell.textContent.toLowerCase() : '';
            const characteristicsText = characteristicsCell ? characteristicsCell.textContent.toLowerCase() : '';
            
            if (nameText.includes(searchTerm) || categoryText.includes(searchTerm) || characteristicsText.includes(searchTerm)) {
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
            counterElement.textContent = `Mostrando {{ $supplies->firstItem() ?? 0 }} - {{ $supplies->lastItem() ?? 0 }} de {{ $supplies->total() }} registros`;
        }
    }
    
    // Mostrar/ocultar mensaje de "no hay resultados"
    if (visibleRows.length === 0 && document.getElementById('searchInput').value) {
        if (noResultsMessage) {
            noResultsMessage.style.display = 'block';
        }
    } else {
        if (noResultsMessage) {
            noResultsMessage.style.display = 'none';
        }
    }
}

// Verificar si hay errores de validación y abrir modal automáticamente
@if($errors->hasAny(['name', 'category_id', 'initial_amount', 'characteristics', 'unit_measure', 'observations']) || session('error'))
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