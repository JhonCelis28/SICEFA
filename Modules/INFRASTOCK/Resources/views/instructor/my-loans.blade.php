<!--
    * @file my-loans.blade.php
    * @brief Vista para la gestión de Préstamos de Herramientas para Instructores en el módulo INFRASTOCK.
    *
    * Esta vista Blade permite al instructor visualizar y registrar préstamos de herramientas.
    * Utiliza Tailwind CSS para un diseño moderno y responsive, y Alpine.js para la interactividad de los modales.
    * Extiende la plantilla `instructor-master.blade.php`.
    *
    * @param Modules\INFRASTOCK\Entities\WarehouseMovement[] $loans Colección de préstamos del instructor.
    * @param Modules\INFRASTOCK\Entities\Tool[] $tools Colección de herramientas disponibles.
    * @param Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse[] $productiveUnitWarehouses Colección de áreas/bodegas.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.instructor-master')

@section('title', 'Mis Préstamos de Herramientas')

@section('breadcrumb-items')
    <li class="flex items-center">
        <a href="{{ route('infrastock.instructor.my-loans') }}" class="text-green-600 hover:text-green-800">Mis Préstamos</a>
    </li>
@endsection

@section('content')
    @php
        $toolsJson = $tools->map(function($t) {
            return [
                'id' => $t->id,
                'nombre' => $t->nombre,
                'estado' => $t->estado ?? 'disponible',
                'disponible' => $t->cantidad_disponible ?? 0,
                'total' => $t->cantidad_total ?? 0,
                'placa' => $t->placa,
            ];
        })->values();
    @endphp

    <script>
        window.formErrors = @json($errors->messages() ?? []);
        window.oldFormData = @json(old() ?? []);
        window.sessionError = @json(session('error') ?? null);
        window.sessionSuccess = @json(session('success') ?? null);
    </script>
    
    <div x-data="{
        isCreateModalOpen: false,
        isEditModalOpen: false,
        isReturnModalOpen: false,
        currentReturnLoan: null,
        currentEditLoan: null,
        createForm: { tools: [{ tool_id: '', amount: '' }], productive_unit_warehouse_id: '', purpose: '', required_date: '', return_date: '' },
        editForm: { tool_id: '', productive_unit_warehouse_id: '', purpose: '', required_date: '', return_date: '', amount: '' },
        returnForm: { description: '' },
        validationErrors: {},
        editToolState: null,
        editToolStock: 0,
        toolsData: {{ $toolsJson->toJson() }},

        init() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
            this.isReturnModalOpen = false;
            
            this.$nextTick(() => {
                const hasFormErrors = window.formErrors && 
                                     typeof window.formErrors === 'object' && 
                                     Object.keys(window.formErrors).length > 0;
                
                if (hasFormErrors) {
                    const oldData = window.oldFormData || {};
                    const formType = oldData._form_type || 'create';
                    this.validationErrors = window.formErrors;
                    
                    if (formType === 'return') {
                        this.isReturnModalOpen = true;
                        this.currentReturnLoan = oldData._return_loan_id || null;
                        this.returnForm.description = oldData.description || '';
                    } else if (formType === 'edit') {
                        this.isEditModalOpen = true;
                        this.currentEditLoan = oldData._edit_loan_id || null;
                        this.editForm.tool_id = oldData.tool_id || '';
                        this.editForm.productive_unit_warehouse_id = oldData.productive_unit_warehouse_id || '';
                        this.editForm.purpose = oldData.purpose || '';
                        this.editForm.required_date = oldData.required_date || '';
                        this.editForm.amount = oldData.amount || '';
                    } else {
                        this.isCreateModalOpen = true;
                        if (oldData.tools && Array.isArray(oldData.tools)) {
                            this.createForm.tools = oldData.tools.map(t => ({
                                tool_id: t.tool_id || '',
                                amount: t.amount || ''
                            }));
                        }
                        this.createForm.productive_unit_warehouse_id = oldData.productive_unit_warehouse_id || '';
                        this.createForm.purpose = oldData.purpose || '';
                        this.createForm.required_date = oldData.required_date || '';
                        this.createForm.return_date = oldData.return_date || '';
                    }
                }
            });
        },

        openCreateModal() {
            this.isCreateModalOpen = true;
            this.resetCreateForm();
        },

        openEditModal(loan) {
            this.currentEditLoan = loan.id;
            this.isEditModalOpen = true;
            this.editForm = {
                tool_id: loan.tool_id ? String(loan.tool_id) : '',
                productive_unit_warehouse_id: loan.productive_unit_warehouse_id ? String(loan.productive_unit_warehouse_id) : '',
                purpose: loan.purpose || '',
                required_date: loan.required_date || '',
                return_date: loan.return_date || '',
                amount: loan.amount ? String(loan.amount) : ''
            };
            this.validationErrors = {};
            this.$nextTick(() => { this.updateEditToolState(); });
        },

        openReturnModal(loanId, maxAmount) {
            this.currentReturnLoan = loanId;
            this.isReturnModalOpen = true;
            this.returnForm.description = '';
            this.returnForm.amount = maxAmount;
            this.returnForm.maxAmount = maxAmount;
        },

        closeModals() {
            this.isCreateModalOpen = false;
            this.isEditModalOpen = false;
            this.isReturnModalOpen = false;
            this.currentReturnLoan = null;
            this.currentEditLoan = null;
            this.validationErrors = {};
            this.returnForm = { description: '', amount: '', maxAmount: '' };
        },

        resetCreateForm() {
            this.createForm = { tools: [{ tool_id: '', amount: '' }], productive_unit_warehouse_id: '', purpose: '', required_date: '', return_date: '' };
            this.validationErrors = {};
        },

        getToolInfo(toolId) {
            return this.toolsData.find(t => t.id === parseInt(toolId)) || null;
        },

        addToolRow() {
            this.createForm.tools.push({ tool_id: '', amount: '' });
        },

        removeToolRow(index) {
            if (this.createForm.tools.length > 1) {
                this.createForm.tools.splice(index, 1);
            }
        },

        clampToolAmount(index) {
            const item = this.createForm.tools[index];
            const tool = this.getToolInfo(item.tool_id);
            if (!tool) {
                item.amount = '';
                return;
            }
            let val = parseInt(item.amount);
            if (isNaN(val) || val < 1) {
                item.amount = '';
                return;
            }
            if (tool.disponible > 0 && val > tool.disponible) {
                item.amount = tool.disponible;
            }
        },

        canSubmitCreate() {
            return this.createForm.tools.length > 0 &&
                   this.createForm.tools.every(t => {
                       const info = this.getToolInfo(t.tool_id);
                       const amount = parseInt(t.amount);
                       return t.tool_id &&
                              info &&
                              info.disponible > 0 &&
                              !isNaN(amount) &&
                              amount >= 1 &&
                              amount <= info.disponible;
                   });
        },

        updateEditToolState() {
            const toolId = parseInt(this.editForm.tool_id);
            const tool = this.toolsData.find(t => t.id === toolId);
            if (tool) {
                this.editToolState = tool.estado;
                this.editToolStock = tool.disponible;
                const currentAmount = parseInt(this.editForm.amount) || 0;
                if (currentAmount > tool.disponible) {
                    this.editForm.amount = tool.disponible > 0 ? tool.disponible : '';
                }
            } else {
                this.editToolState = null;
                this.editToolStock = 0;
            }
        },

        clampEditAmount() {
            let val = parseInt(this.editForm.amount);
            if (isNaN(val) || val < 1) { this.editForm.amount = ''; return; }
            if (this.editToolStock > 0 && val > this.editToolStock) {
                this.editForm.amount = this.editToolStock;
            }
        },

        getEstadoLabel(estado) {
            const estados = {
                'disponible': 'Disponible',
                'en_prestamo': 'En Préstamo',
                'mantenimiento': 'En Mantenimiento',
                'no_disponible': 'No Disponible'
            };
            return estados[estado] || estado;
        }
    }">
        <div class="container mx-auto px-4 py-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Mis Préstamos de Herramientas</h2>
                <button @click="openCreateModal()" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    <i class="fas fa-plus mr-2"></i> Registrar Préstamo
                </button>
            </div>

            <!-- Filtro de búsqueda -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <input type="text" 
                               id="searchInput"
                               value="{{ request('search') }}"
                               placeholder="Buscar por herramienta, placa, finalidad..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button onclick="clearSearch()" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                        <i class="fas fa-times"></i> Limpiar
                    </button>
                </div>
            </div>

            <!-- Tabla de Préstamos -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-700">Historial de Préstamos</h3>
                        <div class="text-sm text-gray-500">
                            Mostrando {{ $loans->firstItem() ?? 0 }} - {{ $loans->lastItem() ?? 0 }} de {{ $loans->total() }} registros
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Herramienta</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placa</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finalidad</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Requerida</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Devolución</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área/Bodega</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fotos</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($loans as $loan)
                                    <tr class="hover:bg-gray-100 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $loan->tool->nombre ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->tool->placa ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($loan->amount)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">{{ $loan->amount }}</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                            <div class="truncate" title="{{ $loan->purpose ?? 'N/A' }}">
                                                {{ $loan->purpose ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($loan->required_date)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    {{ \Carbon\Carbon::parse($loan->required_date)->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($loan->return_date)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                    {{ \Carbon\Carbon::parse($loan->return_date)->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $loan->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }} 
                                            @if($loan->productiveUnitWarehouse && $loan->productiveUnitWarehouse->warehouse)
                                                ({{ $loan->productiveUnitWarehouse->warehouse->name }})
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($loan->role == 'Préstamo')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Préstamo</span>
                                            @elseif($loan->role == 'Devolución')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Devolución</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($loan->status == 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>
                                            @elseif($loan->status == 'approved')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aprobado</span>
                                            @elseif($loan->status == 'rejected')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Rechazado</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $loan->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                @if($loan->delivery_image)
                                                    <a href="{{ asset('storage/' . $loan->delivery_image) }}" target="_blank" class="text-blue-600 hover:text-blue-900" title="Ver foto de entrega">
                                                        <i class="fas fa-image"></i>
                                                    </a>
                                                @else
                                                    <span class="text-gray-400" title="Sin foto de entrega">-</span>
                                                @endif
                                                @if($loan->return_image)
                                                    <a href="{{ asset('storage/' . $loan->return_image) }}" target="_blank" class="text-green-600 hover:text-green-900" title="Ver foto de devolución">
                                                        <i class="fas fa-image"></i>
                                                    </a>
                                                @elseif($loan->imagen)
                                                    <a href="{{ asset('storage/' . $loan->imagen) }}" target="_blank" class="text-green-600 hover:text-green-900" title="Ver foto de devolución">
                                                        <i class="fas fa-image"></i>
                                                    </a>
                                                @else
                                                    <span class="text-gray-400" title="Sin foto de devolución">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                @if($loan->role == 'Préstamo')
                                                    @php
                                                        // Verificar si ya existe una devolución (pendiente o aprobada) para este préstamo
                                                        $hasReturn = \Modules\INFRASTOCK\Entities\WarehouseMovement::where('user_id', $loan->user_id)
                                                            ->where('movement_id', $loan->movement_id)
                                                            ->where('item_type', 'tool')
                                                            ->where('role', 'Devolución')
                                                            ->whereIn('status', ['approved', 'pending'])
                                                            ->where('created_at', '>=', $loan->created_at)
                                                            ->exists();
                                                        
                                                        $loanStatus = trim(strtolower($loan->status ?? ''));
                                                        $isApproved = ($loanStatus === 'approved');
                                                    @endphp
                                                    
                                                    @if($loanStatus === 'pending')
                                                        <!-- Solo permitir editar y eliminar si está pendiente -->
                                                        <button type="button" @click="openEditModal({
                                                            id: {{ $loan->id }},
                                                            tool_id: {{ $loan->movement_id ?? 'null' }},
                                                            productive_unit_warehouse_id: {{ $loan->productive_unit_warehouse_id ?? 'null' }},
                                                            purpose: @js($loan->purpose ?? ''),
                                                            required_date: '{{ $loan->required_date ? \Carbon\Carbon::parse($loan->required_date)->format('Y-m-d') : '' }}',
                                                            return_date: '{{ $loan->return_date ? \Carbon\Carbon::parse($loan->return_date)->format('Y-m-d') : '' }}',
                                                            amount: {{ $loan->amount ?? 'null' }},
                                                            delivery_image: @js($loan->delivery_image ?? '')
                                                        })" class="text-yellow-600 hover:text-yellow-900 p-2 rounded hover:bg-yellow-50 transition-colors" title="Editar Préstamo">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" onclick="confirmDeleteLoan({{ $loan->id }}, '{{ addslashes($loan->tool->nombre ?? 'Herramienta') }}')" class="text-red-600 hover:text-red-900 p-2 rounded hover:bg-red-50 transition-colors" title="Eliminar Préstamo">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    @elseif($isApproved)
                                                        @if($hasReturn)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-600">
                                                                <i class="fas fa-check-circle mr-1"></i> Devolución en proceso
                                                            </span>
                                                        @else
                                                            <button type="button" @click="openReturnModal({{ $loan->id }}, {{ $loan->amount ?? 1 }})" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200 flex items-center" title="Registrar Devolución">
                                                                <i class="fas fa-undo mr-2"></i> Devolver
                                                            </button>
                                                        @endif
                                                    @else
                                                        <!-- Para otros estados (rejected, etc.) -->
                                                        <span class="text-gray-400 text-xs">Estado: {{ $loan->status ?? 'N/A' }}</span>
                                                    @endif
                                                @endif
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
                        <p class="text-gray-500">No hay préstamos que coincidan con tu búsqueda</p>
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
                <div @click.away="isCreateModalOpen = false; resetCreateForm();" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Registrar Préstamo de Herramienta</h3>
                        <button @click="isCreateModalOpen = false; resetCreateForm();" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" action="{{ route('infrastock.instructor.store-loan') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_form_type" value="create">

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-wrench mr-1 text-orange-500"></i> Herramientas: <span class="text-red-500">*</span>
                            </label>
                            <template x-for="(item, index) in createForm.tools" :key="index">
                                <div class="flex items-start space-x-2 mb-3 p-3 bg-gray-50 rounded-lg border">
                                    <div class="flex-1">
                                        <select :name="'tools[' + index + '][tool_id]'" x-model="item.tool_id" @change="clampToolAmount(index)"
                                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline" required>
                                            <option value="">Seleccione herramienta</option>
                                            <template x-for="tool in toolsData" :key="tool.id">
                                                <option :value="tool.id"
                                                    :disabled="tool.estado === 'mantenimiento' || tool.disponible <= 0"
                                                    x-text="tool.nombre + (tool.placa ? ' [' + tool.placa + ']' : '') + ' — Stock: ' + tool.disponible + '/' + tool.total + (tool.estado === 'mantenimiento' ? ' (Mantenimiento)' : (tool.disponible <= 0 ? ' (Sin stock)' : ''))">
                                                </option>
                                            </template>
                                        </select>
                                        <template x-if="item.tool_id && getToolInfo(item.tool_id)">
                                            <p class="text-xs mt-1" :class="getToolInfo(item.tool_id).disponible > 0 ? 'text-green-600' : 'text-red-500'">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                Estado: <span x-text="getEstadoLabel(getToolInfo(item.tool_id).estado)"></span> |
                                                Disponible: <span x-text="getToolInfo(item.tool_id).disponible"></span> unidad(es)
                                            </p>
                                        </template>
                                    </div>
                                    <div class="w-24">
                                        <input type="number"
                                               :name="'tools[' + index + '][amount]'"
                                               x-model="item.amount"
                                               @input="clampToolAmount(index)"
                                               min="1"
                                               :max="getToolInfo(item.tool_id)?.disponible || 1"
                                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-sm leading-tight focus:outline-none focus:shadow-outline"
                                               placeholder="Cant."
                                               required>
                                    </div>
                                    <button type="button" x-show="createForm.tools.length > 1" @click="removeToolRow(index)"
                                            class="text-red-500 hover:text-red-700 p-2 mt-1 flex-shrink-0" title="Quitar herramienta">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </div>
                            </template>
                            <button type="button" @click="addToolRow()" class="text-sm text-blue-600 hover:text-blue-800 font-semibold mt-1">
                                <i class="fas fa-plus-circle mr-1"></i> Agregar otra herramienta
                            </button>
                            @error('tools')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @error('tools.*.tool_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="productive_unit_warehouse_id" class="block text-gray-700 text-sm font-bold mb-2">Unidad Productiva/Bodega: <span class="text-red-500">*</span></label>
                            <select name="productive_unit_warehouse_id" id="productive_unit_warehouse_id" x-model="createForm.productive_unit_warehouse_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('productive_unit_warehouse_id') border-red-500 @enderror" required>
                                <option value="">Seleccione una unidad productiva</option>
                                @foreach($productiveUnitWarehouses as $puw)
                                    <option value="{{ $puw->id }}">
                                        {{ $puw->productiveUnit->name ?? 'N/A' }} - {{ $puw->warehouse->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('productive_unit_warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="purpose" class="block text-gray-700 text-sm font-bold mb-2">Finalidad: <span class="text-red-500">*</span></label>
                            <textarea name="purpose" id="purpose" rows="3" x-model="createForm.purpose" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('purpose') border-red-500 @enderror" placeholder="Describe para qué necesitas la herramienta..."></textarea>
                            @error('purpose')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="required_date" class="block text-gray-700 text-sm font-bold mb-2">Fecha Requerida: <span class="text-red-500">*</span></label>
                            <input type="date" name="required_date" id="required_date" x-model="createForm.required_date" min="{{ date('Y-m-d') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('required_date') border-red-500 @enderror">
                            @error('required_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="return_date" class="block text-gray-700 text-sm font-bold mb-2">Fecha Estimada de Devolución:</label>
                            <input type="date" name="return_date" id="return_date" x-model="createForm.return_date" :min="createForm.required_date || '{{ date('Y-m-d') }}'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('return_date') border-red-500 @enderror">
                            @error('return_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isCreateModalOpen = false; resetCreateForm();" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit"
                                    :disabled="!canSubmitCreate()"
                                    :class="!canSubmitCreate() ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                                    class="text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                Registrar Préstamo
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Edición de Préstamo -->
            <div x-show="isEditModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div @click.away="isEditModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-2xl font-bold text-gray-800">Editar Préstamo de Herramienta</h3>
                        <button @click="isEditModalOpen = false" class="text-gray-500 hover:text-gray-700"><i class="fas fa-times text-xl"></i></button>
                    </div>
                    <form method="POST" :action="`{{ route('infrastock.instructor.update-loan', '') }}/${currentEditLoan}`" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="_form_type" value="edit">
                        <input type="hidden" name="_edit_loan_id" :value="currentEditLoan">
                        <div class="mb-4">
                            <label for="edit_tool_id" class="block text-gray-700 text-sm font-bold mb-2">Herramienta: <span class="text-red-500">*</span></label>
                            <select name="tool_id" id="edit_tool_id" x-model="editForm.tool_id" @change="updateEditToolState()" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('tool_id') border-red-500 @enderror" required>
                                <option value="">Seleccione una herramienta</option>
                                <template x-for="tool in toolsData" :key="tool.id">
                                    <option
                                        :value="tool.id"
                                        :disabled="tool.estado === 'mantenimiento' || tool.disponible <= 0"
                                        :class="(tool.estado === 'mantenimiento' || tool.disponible <= 0) ? 'text-gray-400' : ''"
                                        :selected="editForm.tool_id == tool.id"
                                        x-text="tool.nombre + (tool.placa ? ' [' + tool.placa + ']' : '') + ' — Stock: ' + tool.disponible + '/' + tool.total + (tool.estado === 'mantenimiento' ? ' (En mantenimiento)' : (tool.disponible <= 0 ? ' (Sin stock)' : ''))">
                                    </option>
                                </template>
                            </select>
                            @error('tool_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <template x-if="editToolState">
                                <div class="mt-2 p-3 rounded-md" :class="{
                                    'bg-green-100 border border-green-300': editToolState === 'disponible' && editToolStock > 0,
                                    'bg-yellow-100 border border-yellow-300': editToolState === 'en_prestamo',
                                    'bg-orange-100 border border-orange-300': editToolState === 'mantenimiento',
                                    'bg-red-100 border border-red-300': editToolState === 'no_disponible' || editToolStock <= 0
                                }">
                                    <p class="text-sm font-semibold" :class="{
                                        'text-green-800': editToolState === 'disponible' && editToolStock > 0,
                                        'text-yellow-800': editToolState === 'en_prestamo',
                                        'text-orange-800': editToolState === 'mantenimiento',
                                        'text-red-800': editToolState === 'no_disponible' || editToolStock <= 0
                                    }">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Estado: <span x-text="getEstadoLabel(editToolState)"></span>
                                        <span class="ml-2">|</span>
                                        <i class="fas fa-boxes ml-2 mr-1"></i>
                                        Disponible: <span x-text="editToolStock"></span> unidad(es)
                                    </p>
                                </div>
                            </template>
                        </div>
                        <div class="mb-4">
                            <label for="edit_productive_unit_warehouse_id" class="block text-gray-700 text-sm font-bold mb-2">Unidad Productiva/Bodega: <span class="text-red-500">*</span></label>
                            <select name="productive_unit_warehouse_id" id="edit_productive_unit_warehouse_id" x-model="editForm.productive_unit_warehouse_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('productive_unit_warehouse_id') border-red-500 @enderror" required>
                                <option value="">Seleccione una unidad productiva</option>
                                @foreach($productiveUnitWarehouses as $puw)
                                    <option value="{{ $puw->id }}" :selected="editForm.productive_unit_warehouse_id == {{ $puw->id }}">
                                        {{ $puw->productiveUnit->name ?? 'N/A' }} - {{ $puw->warehouse->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('productive_unit_warehouse_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_amount" class="block text-gray-700 text-sm font-bold mb-2">Cantidad: <span class="text-gray-500 text-xs">(Opcional)</span></label>
                            <input type="number" name="amount" id="edit_amount"
                                   x-model="editForm.amount"
                                   @input="clampEditAmount()"
                                   min="1"
                                   :max="editToolStock > 0 ? editToolStock : 1"
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('amount') border-red-500 @enderror" placeholder="Ej: 1">
                            <p class="text-xs mt-1" :class="editToolStock > 0 ? 'text-green-600' : 'text-red-500'" x-show="editForm.tool_id">
                                <i class="fas fa-boxes mr-1"></i> Máximo disponible: <span x-text="editToolStock"></span> unidad(es)
                            </p>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_purpose" class="block text-gray-700 text-sm font-bold mb-2">Finalidad: <span class="text-red-500">*</span></label>
                            <textarea name="purpose" id="edit_purpose" rows="3" x-model="editForm.purpose" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('purpose') border-red-500 @enderror" placeholder="Describe para qué necesitas la herramienta..."></textarea>
                            @error('purpose')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_required_date" class="block text-gray-700 text-sm font-bold mb-2">Fecha Requerida: <span class="text-red-500">*</span></label>
                            <input type="date" name="required_date" id="edit_required_date" x-model="editForm.required_date" min="{{ date('Y-m-d') }}" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('required_date') border-red-500 @enderror">
                            @error('required_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit_return_date" class="block text-gray-700 text-sm font-bold mb-2">Fecha Estimada de Devolución:</label>
                            <input type="date" name="return_date" id="edit_return_date" x-model="editForm.return_date" :min="editForm.required_date || '{{ date('Y-m-d') }}'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('return_date') border-red-500 @enderror">
                            @error('return_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end space-x-4">
                            <button type="button" @click="isEditModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded transition-colors duration-200">Cancelar</button>
                            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i> Actualizar Préstamo
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal de Devolución -->
            <div x-show="isReturnModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50 flex items-center justify-center p-4" style="display: none;">
                <div @click.away="isReturnModalOpen = false" class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-auto p-6 max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6 border-b pb-4">
                        <h3 class="text-2xl font-bold text-gray-800">
                            <i class="fas fa-undo text-green-600 mr-2"></i>Registrar Devolución de Herramienta
                        </h3>
                        <button @click="isReturnModalOpen = false" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full p-2 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form method="POST" :action="`{{ route('infrastock.instructor.return-loan', '') }}/${currentReturnLoan}`" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_form_type" value="return">
                        <input type="hidden" name="_return_loan_id" :value="currentReturnLoan">
                        
                        <!-- Cantidad -->
                        <div class="mb-6">
                            <label for="return_amount" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-hashtag text-green-500 mr-1"></i>Cantidad a Devolver: <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="amount" id="return_amount" x-model="returnForm.amount" required min="1" :max="returnForm.maxAmount"
                                   class="shadow appearance-none border-2 border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('amount') border-red-500 @enderror"
                                   placeholder="Cantidad a devolver">
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>Puede realizar devoluciones parciales (Máximo disponible: <span x-text="returnForm.maxAmount"></span>)
                            </p>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="mb-6">
                            <label for="return_description" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-file-alt text-blue-500 mr-1"></i>Descripción del Estado de la Herramienta: <span class="text-red-500">*</span>
                            </label>
                            <textarea name="description" id="return_description" rows="5" required
                                      class="shadow appearance-none border-2 border-gray-300 rounded-lg w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                      placeholder="Describe detalladamente el estado en que se devuelve la herramienta: condiciones físicas, funcionamiento, daños (si los hay), observaciones importantes, etc."></textarea>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>Describe cómo se encuentra la herramienta al momento de la devolución (estado, condiciones, observaciones)
                            </p>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Separador -->
                        <div class="border-t border-gray-200 my-6"></div>

                        <!-- Foto de Devolución -->
                        <div class="mb-6">
                            <label for="return_image" class="block text-gray-700 text-sm font-bold mb-2">
                                <i class="fas fa-camera text-green-500 mr-1"></i>Foto de Devolución: <span class="text-red-500">*</span>
                            </label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-green-400 transition-colors">
                                <input type="file" name="return_image" id="return_image" accept="image/*" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 @error('return_image') border-red-500 @enderror"
                                       required>
                                <p class="text-xs text-gray-500 mt-2 text-center">
                                    <i class="fas fa-info-circle mr-1"></i>Sube una foto clara de cómo se devuelve la herramienta (obligatoria)
                                </p>
                            </div>
                            @error('return_image')
                                <p class="text-red-500 text-xs mt-1"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                            @enderror
                            <!-- Vista previa de la imagen de devolución -->
                            <div id="returnImagePreview" class="mt-4 hidden">
                                <p class="text-xs font-semibold text-gray-600 mb-2">Vista previa:</p>
                                <div class="relative">
                                    <img id="previewReturnImg" src="" alt="Vista previa devolución" class="w-full h-64 object-contain rounded-lg border-2 border-gray-300 bg-gray-50">
                                    <button type="button" onclick="document.getElementById('return_image').value=''; document.getElementById('returnImagePreview').classList.add('hidden');" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-2 hover:bg-red-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Campo Legacy (oculto pero funcional para compatibilidad) -->
                        <div class="mb-4 hidden">
                            <label for="return_imagen" class="block text-gray-700 text-sm font-bold mb-2">
                                Imagen Legacy:
                            </label>
                            <input type="file" name="imagen" id="return_imagen" accept="image/*" 
                                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('imagen') border-red-500 @enderror">
                            @error('imagen')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <div id="imagePreview" class="mt-2 hidden">
                                <img id="previewImg" src="" alt="Vista previa" class="max-w-full h-48 object-cover rounded-md border border-gray-300">
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="flex justify-end space-x-4 pt-4 border-t border-gray-200">
                            <button type="button" @click="isReturnModalOpen = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center">
                                <i class="fas fa-times mr-2"></i> Cancelar
                            </button>
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200 flex items-center shadow-lg">
                                <i class="fas fa-undo mr-2"></i> Registrar Devolución
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
// Manejar mensajes de sesión ANTES de que Alpine.js se inicialice
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: @js(session('success')),
            confirmButtonText: 'Entendido',
            timer: 3000,
            timerProgressBar: true
        });
    @endif
    
    @if(session('error') && !$errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: @js(session('error')),
            confirmButtonText: 'Entendido'
        });
    @endif
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

// Función para confirmar eliminación de préstamo
function confirmDeleteLoan(loanId, toolName) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas eliminar el préstamo de "${toolName}"?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear formulario para eliminar
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ route('infrastock.instructor.delete-loan', '') }}/${loanId}`;
            
            // Agregar token CSRF
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Agregar método DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);
            
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Vista previa de imágenes en los modales
document.addEventListener('DOMContentLoaded', function() {
    setupAutoFilter();
    
    // Vista previa para imagen de devolución (modal de devolución)
    const returnImageInput = document.getElementById('return_image');
    const returnImagePreview = document.getElementById('returnImagePreview');
    const previewReturnImg = document.getElementById('previewReturnImg');
    
    if (returnImageInput) {
        returnImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewReturnImg.src = e.target.result;
                    returnImagePreview.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else {
                returnImagePreview.classList.add('hidden');
            }
        });
    }
});
</script>
@endsection

