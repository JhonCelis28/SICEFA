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
 * @param Modules\INFRASTOCK\Entities\Tool[] $tools Colección de herramientas con info de disponibilidad para los modales.
 * @param App\Models\User[] $instructors Colección de usuarios con rol Instructor para el select en los modales.
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
    @php
        $toolsJson = $tools->map(function($t) {
            return [
                'id' => $t->id,
                'nombre' => $t->nombre,
                'estado' => $t->estado,
                'disponible' => $t->cantidad_disponible ?? 0,
                'total' => $t->cantidad_total ?? 0,
                'placa' => $t->placa,
            ];
        })->values();
    @endphp

    <!-- Contenedor principal de la vista de gestión de préstamos y devoluciones -->
    <div x-data="{
        isCreateModalOpen: false,
        isEditModalOpen: false,
        currentLoan: { id: null, item_type: 'tool', movement_id: '', user_id: '', role: '', productive_unit_warehouse_id: '', amount: 1, description: '' },
        validationErrors: {},
        createForm: { movement_id: '', user_mode: 'select', user_id: '', borrower_name: '', amount: 1, description: '' },
        selectedToolStock: 0,
        selectedToolName: '',

        // Datos de herramientas pasados desde el controller
        toolsData: {{ $toolsJson->toJson() }},

        init() {
            @if($errors->any() || session('error'))
                document.addEventListener('DOMContentLoaded', () => {
                    this.isCreateModalOpen = true;
                    this.validationErrors = @json($errors->messages());
                    const oldData = @json(old());
                    this.createForm.movement_id = oldData.movement_id || '';
                    this.createForm.user_id = oldData.user_id || '';
                    this.createForm.borrower_name = oldData.borrower_name || '';
                    this.createForm.user_mode = oldData.borrower_name ? 'manual' : 'select';
                    this.createForm.amount = oldData.amount || 1;
                    this.createForm.description = oldData.description || '';
                    this.updateToolStock();
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

        openEditModal(id, item_type, movement_id, user_id, role, productive_unit_warehouse_id, amount, description) {
            this.isEditModalOpen = true;
            this.currentLoan = { id: id, item_type: item_type, movement_id: movement_id, user_id: user_id, role: role, productive_unit_warehouse_id: productive_unit_warehouse_id, amount: amount || 1, description: description || '' };
            this.validationErrors = {};
        },

        closeModals() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
            this.validationErrors = {};
        },

        resetCreateForm() {
            this.createForm = { movement_id: '', user_mode: 'select', user_id: '', borrower_name: '', amount: 1, description: '' };
            this.selectedToolStock = 0;
            this.selectedToolName = '';
            this.validationErrors = {};
        },

        updateToolStock() {
            const toolId = parseInt(this.createForm.movement_id);
            const tool = this.toolsData.find(t => t.id === toolId);
            if (tool) {
                this.selectedToolStock = tool.disponible;
                this.selectedToolName = tool.nombre;
                // Ajustar cantidad si excede disponible
                if (this.createForm.amount > tool.disponible) {
                    this.createForm.amount = tool.disponible > 0 ? tool.disponible : 1;
                }
            } else {
                this.selectedToolStock = 0;
                this.selectedToolName = '';
            }
        },

        clampAmount() {
            let val = parseInt(this.createForm.amount);
            if (isNaN(val) || val < 1) {
                this.createForm.amount = 1;
                return;
            }
            if (this.selectedToolStock > 0 && val > this.selectedToolStock) {
                this.createForm.amount = this.selectedToolStock;
            }
        }
    }">
        <div class="container mx-auto px-4 py-6">
            <!-- Encabezado con botones de registro, filtros y exportación -->
            <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                <div class="flex items-center space-x-2">
                    <button @click="openCreateModal()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 flex items-center">
                        <i class="fas fa-hand-holding mr-2"></i> Registrar Préstamo
                    </button>
                </div>

                @if($availablePeriods['hasRecords'] ?? false)
                <div class="flex items-center space-x-2">
                    <!-- Script inline para definir el componente antes de Alpine -->
                    <script>
                        window.currentLoanExportParams = {};
                        window.loanPeriodSelector = function() {
                            return {
                                open: false,
                                selectedYear: null,
                                selectedType: null,
                                selectedLabel: 'Seleccionar período',
                                exportParams: {},
                                
                                selectPeriod(label, params) {
                                    this.selectedLabel = label;
                                    this.exportParams = params;
                                    window.currentLoanExportParams = params;
                                    this.open = false;
                                    this.selectedYear = null;
                                    this.selectedType = null;
                                },
                                
                                goBack(level) {
                                    if (level === 'year') {
                                        this.selectedYear = null;
                                        this.selectedType = null;
                                    } else if (level === 'type') {
                                        this.selectedType = null;
                                    }
                                }
                            }
                        }
                    </script>
                    <!-- Menú desplegable de período -->
                    <div class="relative" id="loanExportDropdown" x-data="loanPeriodSelector()" @click.away="open = false; selectedYear = null; selectedType = null">
                        <button @click="open = !open" 
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center min-w-[200px] justify-between">
                            <span class="flex items-center">
                                <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
                                <span x-text="selectedLabel"></span>
                            </span>
                            <i class="fas fa-chevron-down ml-2 text-gray-400 transition-transform" :class="{'rotate-180': open}"></i>
                        </button>

                        <!-- Menú desplegable -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden"
                             style="display: none;">
                            
                            <!-- Nivel 1: Años -->
                            <div x-show="!selectedYear" class="max-h-80 overflow-y-auto">
                                <div class="px-3 py-2 bg-gray-100 border-b border-gray-200">
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Seleccionar Año</span>
                                </div>
                                @foreach($availablePeriods['years'] ?? [] as $yearData)
                                <button @click="selectedYear = {{ $yearData['year'] }}" 
                                        class="w-full px-4 py-3 text-left hover:bg-blue-50 flex items-center justify-between border-b border-gray-100 transition-colors">
                                    <span class="flex items-center">
                                        <i class="fas fa-folder text-yellow-500 mr-3"></i>
                                        <span class="font-medium text-gray-800">{{ $yearData['year'] }}</span>
                                    </span>
                                    <span class="flex items-center">
                                        <span class="text-xs text-gray-500 mr-2">{{ $yearData['totalRecords'] }} registros</span>
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </span>
                                </button>
                                @endforeach
                            </div>

                            <!-- Nivel 2: Tipo de período -->
                            <div x-show="selectedYear && !selectedType" class="max-h-80 overflow-y-auto">
                                <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 flex items-center justify-between">
                                    <button @click="goBack('year')" class="text-blue-600 hover:text-blue-800 flex items-center">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        <span class="text-xs font-semibold uppercase">Año</span>
                                    </button>
                                    <span class="text-sm font-bold text-gray-700" x-text="selectedYear"></span>
                                </div>
                                
                                <!-- Opción Anual -->
                                <button @click="selectPeriod('Año ' + selectedYear, { type: 'yearly', year: selectedYear })" 
                                        class="w-full px-4 py-3 text-left hover:bg-blue-50 flex items-center justify-between border-b border-gray-100 transition-colors">
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar text-blue-500 mr-3"></i>
                                        <span class="font-medium text-gray-800">Todo el año</span>
                                    </span>
                                </button>
                                
                                <!-- Opción Trimestral -->
                                <button @click="selectedType = 'quarterly'" 
                                        class="w-full px-4 py-3 text-left hover:bg-blue-50 flex items-center justify-between border-b border-gray-100 transition-colors">
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar-week text-purple-500 mr-3"></i>
                                        <span class="font-medium text-gray-800">Trimestral</span>
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </button>
                                
                                <!-- Opción Mensual -->
                                <button @click="selectedType = 'monthly'" 
                                        class="w-full px-4 py-3 text-left hover:bg-blue-50 flex items-center justify-between border-b border-gray-100 transition-colors">
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar-day text-orange-500 mr-3"></i>
                                        <span class="font-medium text-gray-800">Mensual</span>
                                    </span>
                                    <i class="fas fa-chevron-right text-gray-400"></i>
                                </button>
                            </div>

                            <!-- Nivel 3: Trimestres -->
                            @foreach($availablePeriods['years'] ?? [] as $yearData)
                            <div x-show="selectedYear == {{ $yearData['year'] }} && selectedType == 'quarterly'" class="max-h-80 overflow-y-auto">
                                <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 flex items-center justify-between">
                                    <button @click="goBack('type')" class="text-blue-600 hover:text-blue-800 flex items-center">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        <span class="text-xs font-semibold uppercase">Tipo</span>
                                    </button>
                                    <span class="text-sm font-bold text-gray-700">{{ $yearData['year'] }} - Trimestres</span>
                                </div>
                                @foreach($yearData['quarters'] as $q)
                                <button @click="selectPeriod('{{ $q['name'] }} {{ $yearData['year'] }}', { type: 'quarterly', year: {{ $yearData['year'] }}, quarter: {{ $q['quarter'] }} })" 
                                        class="w-full px-4 py-3 text-left hover:bg-blue-50 flex items-center justify-between border-b border-gray-100 transition-colors">
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar-week text-purple-500 mr-3"></i>
                                        <span class="font-medium text-gray-800">Trimestre {{ $q['quarter'] }} ({{ $q['name'] }})</span>
                                    </span>
                                    <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">{{ $q['count'] }}</span>
                                </button>
                                @endforeach
                            </div>
                            @endforeach

                            <!-- Nivel 3: Meses -->
                            @foreach($availablePeriods['years'] ?? [] as $yearData)
                            <div x-show="selectedYear == {{ $yearData['year'] }} && selectedType == 'monthly'" class="max-h-80 overflow-y-auto">
                                <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 flex items-center justify-between">
                                    <button @click="goBack('type')" class="text-blue-600 hover:text-blue-800 flex items-center">
                                        <i class="fas fa-arrow-left mr-2"></i>
                                        <span class="text-xs font-semibold uppercase">Tipo</span>
                                    </button>
                                    <span class="text-sm font-bold text-gray-700">{{ $yearData['year'] }} - Meses</span>
                                </div>
                                @foreach($yearData['months'] as $m)
                                <button @click="selectPeriod('{{ $m['name'] }} {{ $yearData['year'] }}', { type: 'monthly', year: {{ $yearData['year'] }}, month: {{ $m['month'] }} })" 
                                        class="w-full px-4 py-3 text-left hover:bg-blue-50 flex items-center justify-between border-b border-gray-100 transition-colors">
                                    <span class="flex items-center">
                                        <i class="fas fa-calendar-day text-orange-500 mr-3"></i>
                                        <span class="font-medium text-gray-800">{{ $m['name'] }}</span>
                                    </span>
                                    <span class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded-full">{{ $m['count'] }}</span>
                                </button>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botón de exportación PDF -->
                    <button onclick="exportLoans('pdf')" 
                       class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200 flex items-center"
                       title="Exportar Préstamos a PDF">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                    <!-- Botón de exportación Excel -->
                    <button onclick="exportLoans('excel')" 
                       class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors duration-200 flex items-center"
                       title="Exportar Préstamos a Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>
                </div>
                @else
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500 italic flex items-center">
                        <i class="fas fa-info-circle mr-2 text-gray-400"></i>
                        No hay registros de préstamos para exportar
                    </span>
                </div>
                @endif
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Préstamos</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['totalPrestamos'] ?? 0 }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-hand-holding text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Devoluciones</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['totalDevoluciones'] ?? 0 }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-undo-alt text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pendientes de Devolución</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $pendingReturns ?? 0 }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3">
                            <i class="fas fa-clock text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Herramientas en Mantenimiento</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $stats['toolsEnMantenimiento'] ?? 0 }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <i class="fas fa-tools text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top herramientas más prestadas y en mantenimiento -->
            @if(($stats['topTools'] ?? collect())->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <!-- Top 5 Herramientas Más Prestadas -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i> Top Herramientas Más Prestadas
                    </h4>
                    <ul class="divide-y divide-gray-200">
                        @foreach($stats['topTools'] as $top)
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-wrench text-orange-400 mr-2"></i>
                                {{ $top->tool->nombre ?? 'N/A' }}
                                @if($top->tool->placa)
                                    <span class="text-xs text-gray-400 ml-1">[{{ $top->tool->placa }}]</span>
                                @endif
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $top->total_loans }} préstamo(s)
                            </span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Herramientas en Mantenimiento -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle text-orange-500 mr-2"></i> Herramientas en Mantenimiento
                    </h4>
                    @php
                        $toolsInMaintenance = $tools->where('estado', 'mantenimiento');
                    @endphp
                    @if($toolsInMaintenance->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($toolsInMaintenance as $mt)
                        <li class="py-3 flex justify-between items-center">
                            <span class="text-gray-700 flex items-center">
                                <i class="fas fa-tools text-red-400 mr-2"></i>
                                {{ $mt->nombre }}
                                @if($mt->placa)
                                    <span class="text-xs text-gray-400 ml-1">[{{ $mt->placa }}]</span>
                                @endif
                            </span>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                En mantenimiento
                            </span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-gray-500 italic py-3">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        No hay herramientas en mantenimiento actualmente.
                    </p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Filtro de búsqueda automático -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="searchInput"
                               value="{{ request('search') }}"
                               placeholder="Buscar por herramienta, usuario, descripción, estado..." 
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
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $loans->firstItem() ?? 0 }} - {{ $loans->lastItem() ?? 0 }} de {{ $loans->total() }} registros
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Elemento</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Usuario</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cantidad</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Área/Bodega</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Movimiento</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Descripción</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($loans as $loan)
                                    <tr class="hover:bg-gray-100 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($loan->item_type == 'tool')
                                                {{ $loan->tool->nombre ?? $loan->tool->name ?? 'N/A' }}
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($loan->amount)
                                                {{ $loan->amount }} {{ $loan->equipment->unit ?? 'unidades' }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }} ({{ $loan->productiveUnitWarehouse->warehouse->name ?? 'N/A' }})</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($loan->role == 'Préstamo')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Préstamo</span>
                                            @elseif($loan->role == 'Devolución')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Devolución</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($loan->status)
                                                @if($loan->status == 'pending')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                                @elseif($loan->status == 'approved')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aprobado</span>
                                                @elseif($loan->status == 'rejected')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rechazado</span>
                                                @endif
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">N/A</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @if($loan->description)
                                                <div class="max-w-xs">
                                                    <p class="text-sm text-gray-700 truncate" title="{{ $loan->description }}">
                                                        {{ Str::limit($loan->description, 50) }}
                                                    </p>
                                                    @if(strlen($loan->description) > 50)
                                                        <button onclick="showDescriptionModal('{{ addslashes($loan->description) }}', '{{ $loan->tool->nombre ?? $loan->tool->name ?? 'Herramienta' }}')" class="text-blue-600 hover:text-blue-800 text-xs mt-1">
                                                            Ver completa
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if($loan->status == 'pending')
                                                <!-- Botones para aprobar/rechazar préstamo o devolución pendiente -->
                                                @if($loan->role == 'Préstamo')
                                                    <button onclick="confirmApproveLoan({{ $loan->id }})" class="text-green-600 hover:text-green-900 mr-2" title="Aprobar préstamo">
                                                        <i class="fas fa-check-circle"></i> Aprobar
                                                    </button>
                                                    <button onclick="openRejectLoanModal({{ $loan->id }})" class="text-red-600 hover:text-red-900 mr-2" title="Rechazar préstamo">
                                                        <i class="fas fa-times-circle"></i> Rechazar
                                                    </button>
                                                @elseif($loan->role == 'Devolución')
                                                    @if($loan->description || $loan->imagen || $loan->return_image)
                                                        <button onclick="showReturnDescriptionModal({{ $loan->id }}, '{{ addslashes($loan->description ?? '') }}', '{{ addslashes($loan->tool->nombre ?? $loan->tool->name ?? 'Herramienta') }}', '{{ $loan->return_image ? asset('storage/' . $loan->return_image) : ($loan->imagen ? asset('storage/' . $loan->imagen) : '') }}')" class="text-blue-600 hover:text-blue-900 mr-2" title="Ver descripción de devolución">
                                                            <i class="fas fa-eye"></i> Ver Descripción
                                                        </button>
                                                    @endif
                                                    <button onclick="confirmApproveReturn({{ $loan->id }})" class="text-green-600 hover:text-green-900 mr-2" title="Aprobar devolución">
                                                        <i class="fas fa-check-circle"></i> Aprobar
                                                    </button>
                                                    <button onclick="openRejectModal({{ $loan->id }})" class="text-red-600 hover:text-red-900 mr-2" title="Rechazar devolución">
                                                        <i class="fas fa-times-circle"></i> Rechazar
                                                    </button>
                                                @endif
                                            @elseif($loan->role == 'Devolución')
                                                {{-- Registro de Devolución: BLOQUEADO --}}
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-600">
                                                    <i class="fas fa-lock mr-1"></i> Bloqueado
                                                </span>
                                            @else
                                                {{-- Préstamo activo: Devolver + Editar + Eliminar --}}
                                                <button onclick="confirmReturnLoan({{ $loan->id }}, '{{ addslashes($loan->tool->nombre ?? $loan->tool->name ?? 'Herramienta') }}')" class="text-green-600 hover:text-green-900 mr-2" title="Registrar Devolución">
                                                    <i class="fas fa-undo-alt"></i>
                                                </button>
                                                <button @click="openEditModal({{ $loan->id }}, '{{ $loan->item_type }}', {{ $loan->movement_id }}, {{ $loan->user_id }}, '{{ $loan->role }}', {{ $loan->productive_unit_warehouse_id }}, {{ $loan->amount ?? 'null' }}, '{{ addslashes($loan->description ?? '') }}')" class="text-yellow-600 hover:text-yellow-900 mr-2" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form method="POST" action="{{ route('infrastock.admin.loans.destroy', $loan->id) }}" style="display: inline;" onsubmit="return confirmDeleteSync('{{ addslashes($loan->role) }}', this)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
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

            <!-- Modal de Creación de Préstamo -->
            <div x-show="isCreateModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div @click.away="isCreateModalOpen = false; resetCreateForm();" class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Registrar Préstamo de Herramienta</h3>
                        <button @click="isCreateModalOpen = false; resetCreateForm();" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" action="{{ route('infrastock.admin.loans.store') }}">
                        @csrf
                        {{-- Herramienta a prestar --}}
                        <div class="mb-4">
                            <label for="create_movement_id" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-wrench mr-1 text-orange-500"></i> Herramienta:
                            </label>
                            <select name="movement_id" id="create_movement_id"
                                    x-model="createForm.movement_id"
                                    @change="updateToolStock()"
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('movement_id') border-red-500 @enderror" required>
                                <option value="">Selecciona una herramienta</option>
                                <template x-for="tool in toolsData" :key="tool.id">
                                    <option
                                        :value="tool.id"
                                        :disabled="tool.estado === 'mantenimiento' || tool.disponible <= 0"
                                        :class="(tool.estado === 'mantenimiento' || tool.disponible <= 0) ? 'text-gray-400' : ''"
                                        x-text="tool.nombre + (tool.placa ? ' [' + tool.placa + ']' : '') + ' — Stock: ' + tool.disponible + '/' + tool.total + (tool.estado === 'mantenimiento' ? ' (En mantenimiento)' : (tool.disponible <= 0 ? ' (Sin stock)' : ''))">
                                    </option>
                                </template>
                            </select>
                            @error('movement_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-xs mt-1" :class="selectedToolStock > 0 ? 'text-green-600' : 'text-red-500'" x-show="createForm.movement_id">
                                <i class="fas fa-boxes mr-1"></i> Disponible: <span x-text="selectedToolStock"></span> unidad(es)
                            </p>
                        </div>

                        {{-- Quién recibe la herramienta --}}
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-user mr-1 text-blue-500"></i> ¿Quién recibe la herramienta?
                            </label>
                            <div class="flex space-x-4 mb-3">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" value="select" x-model="createForm.user_mode" class="form-radio text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700">Instructor del sistema</span>
                                </label>
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="radio" value="manual" x-model="createForm.user_mode" class="form-radio text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700">Otra persona</span>
                                </label>
                            </div>

                            {{-- Selector de instructor --}}
                            <div x-show="createForm.user_mode === 'select'">
                                <select name="user_id" id="create_user_id"
                                        x-model="createForm.user_id"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('user_id') border-red-500 @enderror"
                                        :required="createForm.user_mode === 'select'">
                                    <option value="">Selecciona un instructor</option>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}" {{ old('user_id') == $instructor->id ? 'selected' : '' }}>
                                            {{ $instructor->person->first_name ?? 'N/A' }} {{ $instructor->person->first_last_name ?? '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Campo para nombre manual --}}
                            <div x-show="createForm.user_mode === 'manual'">
                                <input type="text" name="borrower_name" id="create_borrower_name"
                                       x-model="createForm.borrower_name"
                                       placeholder="Nombre completo de quien recibe"
                                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('borrower_name') border-red-500 @enderror"
                                       :required="createForm.user_mode === 'manual'">
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i> Para personas sin cuenta en el sistema (ej: personal de otra área)
                                </p>
                                @error('borrower_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Cantidad --}}
                        <div class="mb-4">
                            <label for="create_amount" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-sort-numeric-up mr-1 text-purple-500"></i> Cantidad:
                            </label>
                            <input type="number" name="amount" id="create_amount"
                                   x-model="createForm.amount"
                                   @input="clampAmount()"
                                   min="1"
                                   :max="selectedToolStock > 0 ? selectedToolStock : 1"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('amount') border-red-500 @enderror"
                                   required>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Descripción / Observaciones --}}
                        <div class="mb-6">
                            <label for="create_description" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-comment-alt mr-1 text-gray-500"></i> Observaciones (opcional):
                            </label>
                            <textarea name="description" id="create_description" rows="2"
                                      x-model="createForm.description"
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('description') border-red-500 @enderror"
                                      placeholder="Ej: Motivo del préstamo, área de trabajo, etc."></textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isCreateModalOpen = false; resetCreateForm();" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit"
                                    :disabled="!createForm.movement_id || selectedToolStock <= 0"
                                    :class="(!createForm.movement_id || selectedToolStock <= 0) ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                                    class="text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                <i class="fas fa-hand-holding mr-2"></i>Registrar Préstamo
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición de Movimiento -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div @click.away="isEditModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Préstamo</h3>
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" :action="`{{ route('infrastock.admin.loans.update', '') }}/${currentLoan.id}`">
                        @csrf
                        @method('PUT')
                        {{-- item_type siempre tool --}}
                        <input type="hidden" name="item_type" value="tool">

                        <div class="mb-4">
                            <label for="edit_movement_id" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-wrench mr-1 text-orange-500"></i> Herramienta:
                            </label>
                            <select name="movement_id" id="edit_movement_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" x-model="currentLoan.movement_id" required>
                                <option value="">Selecciona una herramienta</option>
                                @foreach($tools as $tool)
                                    <option value="{{ $tool->id }}">{{ $tool->nombre }} {{ $tool->placa ? '['.$tool->placa.']' : '' }} — Stock: {{ $tool->cantidad_disponible ?? 0 }}/{{ $tool->cantidad_total ?? 0 }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_user_id" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-user mr-1 text-blue-500"></i> Usuario:
                            </label>
                            <select name="user_id" id="edit_user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" x-model="currentLoan.user_id" required>
                                <option value="">Selecciona un usuario</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}">{{ $instructor->person->first_name ?? 'N/A' }} {{ $instructor->person->first_last_name ?? '' }}</option>
                                @endforeach
                                {{-- Incluir el admin logueado como opción (para préstamos de personas externas) --}}
                                @if(!$instructors->contains('id', auth()->id()))
                                    <option value="{{ auth()->id() }}">{{ auth()->user()->person->first_name ?? auth()->user()->nickname ?? 'Admin' }} {{ auth()->user()->person->first_last_name ?? '' }} (Admin)</option>
                                @endif
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="mb-4">
                                <label for="edit_role" class="block text-gray-700 text-sm font-bold mb-2">Tipo de Movimiento:</label>
                                <select name="role" id="edit_role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" x-model="currentLoan.role" required>
                                    <option value="Préstamo">Préstamo</option>
                                    <option value="Devolución">Devolución</option>
                                </select>
                                <p class="text-xs text-orange-600 mt-1" x-show="currentLoan.role === 'Devolución'">
                                    <i class="fas fa-exclamation-triangle"></i> Al cambiar a Devolución, se restaurará el stock y el registro quedará bloqueado.
                                </p>
                            </div>
                            <div class="mb-4">
                                <label for="edit_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad:</label>
                                <input type="number" name="amount" id="edit_amount" x-model="currentLoan.amount" min="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                        </div>
                        <div class="mb-6">
                            <label for="edit_description" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-comment-alt mr-1 text-gray-500"></i> Descripción / Observaciones:
                            </label>
                            <textarea name="description" id="edit_description" rows="2" x-model="currentLoan.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isEditModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">Actualizar Movimiento</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal para Rechazar Devolución -->
            <div id="rejectReturnModal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 hidden items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Rechazar Devolución</h3>
                        <button onclick="closeRejectModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form id="rejectReturnForm" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="rejection_reason" class="block text-gray-700 text-sm font-bold mb-2">
                                Motivo del Rechazo *
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="4" required
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                      placeholder="Describe el motivo por el cual se rechaza esta devolución..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres</p>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closeRejectModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                <i class="fas fa-times-circle mr-2"></i>
                                Rechazar Devolución
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal para Rechazar Préstamo -->
            <div id="rejectLoanModal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 hidden items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Rechazar Préstamo</h3>
                        <button onclick="closeRejectLoanModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form id="rejectLoanForm" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="rejection_reason_loan" class="block text-gray-700 text-sm font-bold mb-2">
                                Motivo del Rechazo *
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason_loan" rows="4" required
                                      class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                      placeholder="Describe el motivo por el cual se rechaza este préstamo..."></textarea>
                            <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres</p>
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" onclick="closeRejectLoanModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">
                                Cancelar
                            </button>
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                <i class="fas fa-times-circle mr-2"></i>
                                Rechazar Préstamo
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal para Ver Descripción de Devolución -->
            <div id="returnDescriptionModal" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 hidden items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto p-6 flex flex-col" style="max-height: 90vh;">
                    <div class="flex justify-between items-center mb-4 flex-shrink-0">
                        <h3 class="text-2xl font-bold text-gray-800">Descripción de Devolución</h3>
                        <button onclick="closeReturnDescriptionModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="mb-4 overflow-y-auto flex-grow" style="min-height: 0;">
                        <p class="text-sm text-gray-600 mb-2"><strong>Herramienta:</strong> <span id="modalToolName"></span></p>
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Descripción de Entrega:
                            </label>
                            <p id="modalDescription" class="text-gray-700 whitespace-pre-wrap"></p>
                        </div>
                        <!-- Imagen de la devolución -->
                        <div id="modalImageContainer" class="hidden">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Imagen de la Devolución:
                            </label>
                            <div class="mt-2 flex justify-center">
                                <img id="modalReturnImage" src="" alt="Imagen de devolución" class="rounded-lg border border-gray-300 cursor-pointer hover:opacity-80 transition-opacity" style="max-width: 100%; max-height: 40vh; object-fit: contain;" onclick="showImageFullscreen(this.src)">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Haz clic en la imagen para verla en tamaño completo</p>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 flex-shrink-0 pt-4 border-t border-gray-200">
                        <button onclick="closeReturnDescriptionModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">
                            Cerrar
                        </button>
                        <form id="approveReturnFromModalForm" method="POST" style="display: inline;" onsubmit="return confirmApproveReturnFromModal(event)">
                            @csrf
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                <i class="fas fa-check-circle mr-2"></i> Aprobar Devolución
                            </button>
                        </form>
                        <button onclick="openRejectModalFromDescription()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                            <i class="fas fa-times-circle mr-2"></i> Rechazar Devolución
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal para Ver Descripción Completa (genérico) -->
            <div id="descriptionModal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 hidden items-center justify-center p-4">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Descripción</h3>
                        <button onclick="closeDescriptionModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="mb-4">
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <p id="modalDescriptionText" class="text-gray-700 whitespace-pre-wrap"></p>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button onclick="closeDescriptionModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
// Función para confirmar devolución de un préstamo
function confirmReturnLoan(loanId, toolName) {
    Swal.fire({
        title: '¿Registrar Devolución?',
        html: `<p>¿Confirmas la devolución de <strong>${toolName}</strong>?</p>
               <p class="text-sm text-gray-500 mt-2">El stock de la herramienta será restaurado y el registro quedará <strong>bloqueado</strong>.</p>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: '<i class="fas fa-undo-alt mr-2"></i>Sí, registrar devolución',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Procesando devolución...',
                text: 'Por favor espera',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            const form = document.createElement('form');
            form.method = 'POST';
            const baseUrl = '{{ route("infrastock.admin.loans.return", 0) }}';
            form.action = baseUrl.replace('/0', '/' + loanId);

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);

            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Función para confirmar eliminación con SweetAlert2
function confirmDeleteSync(movementType, formElement) {
    // Prevenir el envío inmediato del formulario
    event.preventDefault();
    
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
            
            // Enviar el formulario directamente (sin pasar por onsubmit otra vez)
            formElement.submit();
        }
    });
    
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

// Función para abrir modal de rechazo de devolución
function openRejectModal(returnId) {
    const baseUrl = '{{ route("infrastock.admin.loans.reject-return", 0) }}';
    document.getElementById('rejectReturnForm').action = baseUrl.replace('/0/', '/' + returnId + '/').replace(/\/0$/, '/' + returnId);
    document.getElementById('rejectReturnModal').classList.remove('hidden');
    document.getElementById('rejectReturnModal').classList.add('flex');
}

// Función para cerrar modal de rechazo de devolución
function closeRejectModal() {
    document.getElementById('rejectReturnModal').classList.add('hidden');
    document.getElementById('rejectReturnModal').classList.remove('flex');
    document.getElementById('rejectReturnForm').reset();
}

// Función para confirmar aprobación de préstamo con SweetAlert2
function confirmApproveLoan(loanId) {
    Swal.fire({
        title: '¿Aprobar préstamo?',
        text: '¿Estás seguro de que deseas aprobar este préstamo?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario oculto para enviar la petición
            const form = document.createElement('form');
            form.method = 'POST';
            const baseUrl = '{{ route("infrastock.admin.loans.approve-loan", 0) }}';
            form.action = baseUrl.replace('/0', '/' + loanId);
            
            // Agregar token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Enviar formulario
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Función para confirmar aprobación de devolución con SweetAlert2
function confirmApproveReturn(returnId) {
    Swal.fire({
        title: '¿Aprobar devolución?',
        text: '¿Estás seguro de que deseas aprobar esta devolución?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario oculto para enviar la petición
            const form = document.createElement('form');
            form.method = 'POST';
            const baseUrl = '{{ route("infrastock.admin.loans.approve-return", 0) }}';
            form.action = baseUrl.replace('/0', '/' + returnId);
            
            // Agregar token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            form.appendChild(csrfToken);
            
            // Enviar formulario
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Función para confirmar aprobación de devolución desde el modal
function confirmApproveReturnFromModal(event) {
    event.preventDefault();
    const form = document.getElementById('approveReturnFromModalForm');
    const actionUrl = form.action;
    
    Swal.fire({
        title: '¿Aprobar devolución?',
        text: '¿Estás seguro de que deseas aprobar esta devolución?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10B981',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    
    return false;
}

// Función para abrir modal de rechazo de préstamo
function openRejectLoanModal(loanId) {
    const baseUrl = '{{ route("infrastock.admin.loans.reject-loan", 0) }}';
    document.getElementById('rejectLoanForm').action = baseUrl.replace('/0', '/' + loanId);
    document.getElementById('rejectLoanModal').classList.remove('hidden');
    document.getElementById('rejectLoanModal').classList.add('flex');
}

// Función para cerrar modal de rechazo de préstamo
function closeRejectLoanModal() {
    document.getElementById('rejectLoanModal').classList.add('hidden');
    document.getElementById('rejectLoanModal').classList.remove('flex');
    document.getElementById('rejectLoanForm').reset();
}

// Variables globales para el modal de descripción de devolución
let currentReturnLoanId = null;

// Función para mostrar el modal de descripción de devolución
function showReturnDescriptionModal(loanId, description, toolName, imagenUrl) {
    currentReturnLoanId = loanId;
    document.getElementById('modalToolName').textContent = toolName;
    document.getElementById('modalDescription').textContent = description || 'Sin descripción';
    const baseUrl = '{{ route("infrastock.admin.loans.approve-return", 0) }}';
    document.getElementById('approveReturnFromModalForm').action = baseUrl.replace('/0', '/' + loanId);
    
    // Mostrar u ocultar la imagen según si existe
    const imageContainer = document.getElementById('modalImageContainer');
    const modalImage = document.getElementById('modalReturnImage');
    if (imagenUrl && imagenUrl.trim() !== '') {
        modalImage.src = imagenUrl;
        imageContainer.classList.remove('hidden');
    } else {
        imageContainer.classList.add('hidden');
    }
    
    document.getElementById('returnDescriptionModal').classList.remove('hidden');
    document.getElementById('returnDescriptionModal').classList.add('flex');
}

// Función para cerrar el modal de descripción de devolución
function closeReturnDescriptionModal() {
    document.getElementById('returnDescriptionModal').classList.add('hidden');
    document.getElementById('returnDescriptionModal').classList.remove('flex');
    currentReturnLoanId = null;
}

// Función para abrir modal de rechazo desde el modal de descripción
function openRejectModalFromDescription() {
    // Guardar el ID antes de cerrar el modal (closeReturnDescriptionModal lo resetea a null)
    const loanId = currentReturnLoanId;
    closeReturnDescriptionModal();
    if (loanId) {
        openRejectModal(loanId);
    }
}

// Función para mostrar imagen en pantalla completa
function showImageFullscreen(imageSrc) {
    Swal.fire({
        imageUrl: imageSrc,
        imageAlt: 'Imagen de devolución',
        showConfirmButton: false,
        showCloseButton: true,
        width: '90%',
        padding: '0',
        customClass: {
            popup: 'image-popup',
            image: 'max-w-full h-auto'
        }
    });
}

// Función para mostrar descripción completa (genérico)
function showDescriptionModal(description, toolName) {
    document.getElementById('modalDescriptionText').textContent = description;
    document.getElementById('descriptionModal').classList.remove('hidden');
    document.getElementById('descriptionModal').classList.add('flex');
}

// Función para cerrar modal de descripción (genérico)
function closeDescriptionModal() {
    document.getElementById('descriptionModal').classList.add('hidden');
    document.getElementById('descriptionModal').classList.remove('flex');
}

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

// Función para exportar préstamos a PDF o Excel
function exportLoans(format) {
    const params = window.currentLoanExportParams || {};
    let url;
    if (format === 'pdf') {
        url = '{{ route("infrastock.admin.loans.export.pdf") }}';
    } else {
        url = '{{ route("infrastock.admin.loans.export.excel") }}';
    }

    const queryParams = new URLSearchParams();
    if (params.type) queryParams.set('type', params.type);
    if (params.year) queryParams.set('year', params.year);
    if (params.month) queryParams.set('month', params.month);
    if (params.quarter) queryParams.set('quarter', params.quarter);

    const queryString = queryParams.toString();
    if (queryString) {
        url += '?' + queryString;
    }

    window.open(url, '_blank');
}
</script>
@endsection