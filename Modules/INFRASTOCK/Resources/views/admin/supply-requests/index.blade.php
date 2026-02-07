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
    
    <div class="container mx-auto px-4 py-6">
        <!-- Encabezado con botones de exportación -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
            <div>
                <p class="text-gray-600">Administra las solicitudes de todos los usuarios</p>
            </div>
            
            @if($availablePeriods['hasRecords'])
            <div class="flex items-center space-x-2">
                <!-- Script inline para definir el componente antes de Alpine -->
                <script>
                    window.currentExportParams = {};
                    window.exportPeriodSelector = function() {
                        return {
                            open: false,
                            selectedYear: null,
                            selectedType: null,
                            selectedLabel: 'Seleccionar período',
                            exportParams: {},
                            
                            selectPeriod(label, params) {
                                this.selectedLabel = label;
                                this.exportParams = params;
                                window.currentExportParams = params;
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
                <!-- Menú desplegable de exportación -->
                <div class="relative" id="exportDropdown" x-data="exportPeriodSelector()" @click.away="open = false; selectedYear = null; selectedType = null">
                    <!-- Botón principal -->
                    <button @click="open = !open" 
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 flex items-center min-w-[200px] justify-between">
                        <span class="flex items-center">
                            <i class="fas fa-calendar-alt mr-2 text-green-600"></i>
                            <span x-text="selectedLabel"></span>
                        </span>
                        <i class="fas fa-chevron-down ml-2 text-gray-400 transition-transform" :class="{'rotate-180': open}"></i>
                    </button>

                    <!-- Menú desplegable -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-72 bg-white rounded-lg shadow-xl border border-gray-200 z-50 overflow-hidden"
                         style="display: none;">
                        
                        <!-- Nivel 1: Años -->
                        <div x-show="!selectedYear" class="max-h-80 overflow-y-auto">
                            <div class="px-3 py-2 bg-gray-100 border-b border-gray-200">
                                <span class="text-xs font-semibold text-gray-500 uppercase">Seleccionar Año</span>
                            </div>
                            @foreach($availablePeriods['years'] as $yearData)
                            <button @click="selectedYear = {{ $yearData['year'] }}" 
                                    class="w-full px-4 py-3 text-left hover:bg-green-50 flex items-center justify-between border-b border-gray-100 transition-colors">
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
                                <button @click="goBack('year')" class="text-green-600 hover:text-green-800 flex items-center">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    <span class="text-xs font-semibold uppercase">Año</span>
                                </button>
                                <span class="text-sm font-bold text-gray-700" x-text="selectedYear"></span>
                            </div>
                            
                            <!-- Opción Anual -->
                            <button @click="selectPeriod('Año ' + selectedYear, { type: 'yearly', year: selectedYear })" 
                                    class="w-full px-4 py-3 text-left hover:bg-green-50 flex items-center justify-between border-b border-gray-100 transition-colors">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar text-blue-500 mr-3"></i>
                                    <span class="font-medium text-gray-800">Todo el año</span>
                                </span>
                                <i class="fas fa-check text-green-500 opacity-0 group-hover:opacity-100"></i>
                            </button>
                            
                            <!-- Opción Trimestral -->
                            <button @click="selectedType = 'quarterly'" 
                                    class="w-full px-4 py-3 text-left hover:bg-green-50 flex items-center justify-between border-b border-gray-100 transition-colors">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar-week text-purple-500 mr-3"></i>
                                    <span class="font-medium text-gray-800">Por Trimestre</span>
                                </span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </button>
                            
                            <!-- Opción Mensual -->
                            <button @click="selectedType = 'monthly'" 
                                    class="w-full px-4 py-3 text-left hover:bg-green-50 flex items-center justify-between border-b border-gray-100 transition-colors">
                                <span class="flex items-center">
                                    <i class="fas fa-calendar-day text-orange-500 mr-3"></i>
                                    <span class="font-medium text-gray-800">Por Mes</span>
                                </span>
                                <i class="fas fa-chevron-right text-gray-400"></i>
                            </button>
                        </div>

                        <!-- Nivel 3: Trimestres -->
                        @foreach($availablePeriods['years'] as $yearData)
                        <div x-show="selectedYear == {{ $yearData['year'] }} && selectedType == 'quarterly'" class="max-h-80 overflow-y-auto">
                            <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 flex items-center justify-between">
                                <button @click="goBack('type')" class="text-green-600 hover:text-green-800 flex items-center">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    <span class="text-xs font-semibold uppercase">Tipo</span>
                                </button>
                                <span class="text-sm font-bold text-gray-700">{{ $yearData['year'] }} - Trimestres</span>
                            </div>
                            @foreach($yearData['quarters'] as $q)
                            <button @click="selectPeriod('Q{{ $q['quarter'] }} {{ $yearData['year'] }}', { type: 'quarterly', year: {{ $yearData['year'] }}, quarter: {{ $q['quarter'] }} })" 
                                    class="w-full px-4 py-3 text-left hover:bg-green-50 flex items-center justify-between border-b border-gray-100 transition-colors">
                                <span class="flex items-center">
                                    <i class="fas fa-layer-group text-purple-500 mr-3"></i>
                                    <span class="font-medium text-gray-800">Trimestre {{ $q['quarter'] }}</span>
                                    <span class="text-xs text-gray-400 ml-2">({{ implode(', ', array_map(function($m) { 
                                        $names = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
                                        return $names[$m-1];
                                    }, $q['months'])) }})</span>
                                </span>
                                <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded-full">{{ $q['count'] }}</span>
                            </button>
                            @endforeach
                        </div>
                        @endforeach

                        <!-- Nivel 3: Meses -->
                        @foreach($availablePeriods['years'] as $yearData)
                        <div x-show="selectedYear == {{ $yearData['year'] }} && selectedType == 'monthly'" class="max-h-80 overflow-y-auto">
                            <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 flex items-center justify-between">
                                <button @click="goBack('type')" class="text-green-600 hover:text-green-800 flex items-center">
                                    <i class="fas fa-arrow-left mr-2"></i>
                                    <span class="text-xs font-semibold uppercase">Tipo</span>
                                </button>
                                <span class="text-sm font-bold text-gray-700">{{ $yearData['year'] }} - Meses</span>
                            </div>
                            @foreach($yearData['months'] as $m)
                            <button @click="selectPeriod('{{ $m['name'] }} {{ $yearData['year'] }}', { type: 'monthly', year: {{ $yearData['year'] }}, month: {{ $m['month'] }} })" 
                                    class="w-full px-4 py-3 text-left hover:bg-green-50 flex items-center justify-between border-b border-gray-100 transition-colors">
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
                <button onclick="exportConsumption('pdf')" 
                   class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                   title="Exportar Consumos a PDF"
                   id="btnExportPdf">
                    <i class="fas fa-file-pdf"></i>
                </button>
                <!-- Botón de exportación Excel -->
                <button onclick="exportConsumption('excel')" 
                   class="px-4 py-2 bg-emerald-600 text-white rounded-md hover:bg-emerald-700 transition-colors duration-200 flex items-center disabled:opacity-50 disabled:cursor-not-allowed"
                   title="Exportar Consumos a Excel"
                   id="btnExportExcel">
                    <i class="fas fa-file-excel"></i>
                </button>
            </div>
            @else
            <div class="flex items-center">
                <span class="text-sm text-gray-500 italic flex items-center">
                    <i class="fas fa-info-circle mr-2 text-gray-400"></i>
                    No hay registros de consumo para exportar
                </span>
            </div>
            @endif
        </div>

        <!-- Resumen de estados -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pendientes</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $supplyRequests->where('status', 'pending')->count() }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Aprobadas</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $supplyRequests->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rechazadas</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $supplyRequests->where('status', 'rejected')->count() }}</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <i class="fas fa-times text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Solicitudes de Insumos -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-clipboard-list mr-2 text-green-600"></i>
                    Lista de Solicitudes
                </h3>
            </div>
                
                @if($supplyRequests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Solicitante</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Insumos</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cantidad Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Unidad Productiva</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Almacén</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha Solicitud</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($supplyRequests as $request)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <img class="h-8 w-8 rounded-full" src="{{ asset('AdminLTE/dist/img/user2-160x160.jpg') }}" alt="">
                                            </div>
                                            <div class="ml-3">
                                                @php
                                                    $roleName = 'Usuario';
                                                    if ($request->user && $request->user->relationLoaded('roles')) {
                                                        $userRoles = $request->user->roles->pluck('name')->toArray();
                                                    } else {
                                                        // Si los roles no están cargados, cargarlos manualmente
                                                        $user = $request->user;
                                                        if ($user) {
                                                            $user->load('roles');
                                                            $userRoles = $user->roles->pluck('name')->toArray();
                                                        } else {
                                                            $userRoles = [];
                                                        }
                                                    }
                                                    
                                                    if (!empty($userRoles)) {
                                                        if (in_array('Operario', $userRoles)) {
                                                            $roleName = 'Operario';
                                                        } elseif (in_array('Aseo', $userRoles)) {
                                                            $roleName = 'Personal de Aseo';
                                                        } elseif (in_array('Centro de Convivencia', $userRoles)) {
                                                            $roleName = 'Centro de Convivencia';
                                                        } elseif (in_array('Ganadería', $userRoles)) {
                                                            $roleName = 'Ganadería';
                                                        } else {
                                                            // Si tiene roles pero no es uno de los esperados, mostrar el primero
                                                            $roleName = $userRoles[0] ?? 'Usuario';
                                                        }
                                                    }
                                                @endphp
                                                <div class="text-sm font-medium text-gray-900">{{ $request->user->name ?? 'Usuario' }}</div>
                                                <div class="text-sm text-gray-500">{{ $request->user->email ?? 'N/A' }}</div>
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ $roleName }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            @foreach($request->items->take(3) as $item)
                                                <div class="font-medium">{{ $item->equipment->name ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->equipment->category->name ?? 'Sin categoría' }}</div>
                                                @if(!$loop->last)<br>@endif
                                            @endforeach
                                            @if($request->items->count() > 3)
                                                <div class="text-xs text-blue-600 mt-1">y {{ $request->items->count() - 3 }} más...</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $request->items->sum('requested_amount') }} unidades
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->productiveUnitWarehouse->warehouse->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $request->created_at->format('d/m/Y') }}</div>
                                        <div class="text-xs">{{ $request->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @switch($request->status)
                                            @case('pending')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Pendiente
                                                </span>
                                                @break
                                            @case('approved')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Aprobada
                                                </span>
                                                @break
                                            @case('rejected')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Rechazada
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $request->status }}</span>
                                        @endswitch
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($request->status === 'pending')
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" 
                                                        onclick="approveRequest({{ $request->id }})"
                                                        title="Aprobar solicitud">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Aprobar
                                                </button>
                                                <button type="button" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" 
                                                        onclick="showRejectModal({{ $request->id }})"
                                                        title="Rechazar solicitud">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Rechazar
                                                </button>
                                            </div>
                                        @elseif($request->status === 'approved')
                                            <div class="flex items-center justify-end text-green-600">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                <span class="text-sm font-medium">Procesada</span>
                                            </div>
                                        @elseif($request->status === 'rejected')
                                            <div class="flex items-center justify-end text-red-600">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                <span class="text-sm font-medium">Rechazada</span>
                                            </div>
                                        @else
                                            <span class="text-gray-400">
                                                <i class="fas fa-info-circle" title="Estado desconocido"></i>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($supplyRequests->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $supplyRequests->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay solicitudes</h3>
                    <p class="text-gray-500">No se han encontrado solicitudes de insumos.</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Función para exportar consumos
    function exportConsumption(format) {
        const exportParams = window.currentExportParams || {};
        
        if (!exportParams || Object.keys(exportParams).length === 0) {
            Swal.fire({
                title: 'Selecciona un período',
                text: 'Por favor, selecciona primero un período para exportar.',
                icon: 'warning',
                confirmButtonColor: '#10B981',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Construir URL con parámetros
        let baseUrl = format === 'pdf' 
            ? "{{ route('infrastock.admin.supply-requests.export.pdf') }}"
            : "{{ route('infrastock.admin.supply-requests.export.excel') }}";
        
        const params = new URLSearchParams();
        if (exportParams.type) params.append('type', exportParams.type);
        if (exportParams.year) params.append('year', exportParams.year);
        if (exportParams.month) params.append('month', exportParams.month);
        if (exportParams.quarter) params.append('quarter', exportParams.quarter);
        
        window.open(baseUrl + '?' + params.toString(), '_blank');
    }

    // Función para aprobar solicitud
    function approveRequest(requestId) {
        Swal.fire({
            title: '¿Aprobar solicitud?',
            text: '¿Estás seguro de que deseas aprobar esta solicitud?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#10B981',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Sí, aprobar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario oculto para enviar la petición
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/infrastock/admin/supply-requests/${requestId}`;
                
                // Agregar token CSRF
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
                // Agregar método PUT
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                form.appendChild(methodField);
                
                // Agregar estado
                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'status';
                statusField.value = 'approved';
                form.appendChild(statusField);
                
                // Enviar formulario
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Función para mostrar el modal de rechazo
    function showRejectModal(requestId) {
        Swal.fire({
            title: 'Rechazar solicitud',
            text: 'Por favor, proporciona el motivo del rechazo:',
            input: 'textarea',
            inputPlaceholder: 'Motivo del rechazo...',
            inputAttributes: {
                'aria-label': 'Motivo del rechazo',
                'rows': 4
            },
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Rechazar',
            cancelButtonText: 'Cancelar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            inputValidator: (value) => {
                if (!value || value.trim().length === 0) {
                    return 'Debes proporcionar un motivo para el rechazo';
                }
                if (value.trim().length < 10) {
                    return 'El motivo debe tener al menos 10 caracteres';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Crear formulario oculto para enviar la petición
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/infrastock/admin/supply-requests/${requestId}`;
                
                // Agregar token CSRF
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
                // Agregar método PUT
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';
                form.appendChild(methodField);
                
                // Agregar estado
                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'status';
                statusField.value = 'rejected';
                form.appendChild(statusField);
                
                // Agregar motivo de rechazo
                const reasonField = document.createElement('input');
                reasonField.type = 'hidden';
                reasonField.name = 'rejection_reason';
                reasonField.value = result.value.trim();
                form.appendChild(reasonField);
                
                // Enviar formulario
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Mostrar mensajes de éxito/error de Laravel
    @if(session('success'))
        Swal.fire({
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonColor: '#10B981',
            confirmButtonText: 'Entendido'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Error',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Entendido'
        });
    @endif
</script>
@endsection