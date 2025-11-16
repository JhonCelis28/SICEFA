@extends('infrastock::layouts.master')

@section('title', 'Devoluciones de Insumos')

@section('breadcrumb-items')
    <li class="flex items-center">
        <a href="{{ route('infrastock.admin.supply-returns.index') }}" class="text-green-600 hover:text-green-800">Devoluciones de Insumos</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
@endsection

@section('content')
    
    <div class="container mx-auto px-4 py-6">
        <!-- Encabezado -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Devoluciones de Insumos</h1>
            <p class="text-gray-600">Gestiona las devoluciones de insumos registradas por otros usuarios - Historial completo</p>
        </div>

        <!-- Tarjetas de Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingCount }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Aprobadas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $approvedCount }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Rechazadas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $rejectedCount }}</p>
                    </div>
                    <div class="bg-red-100 rounded-full p-3">
                        <i class="fas fa-times-circle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalCount }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-list text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-medium text-gray-700">Filtrar por estado:</span>
                <a href="{{ route('infrastock.admin.supply-returns.index', ['status' => 'all']) }}" 
                   class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ !request('status') || request('status') === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Todas
                </a>
                <a href="{{ route('infrastock.admin.supply-returns.index', ['status' => 'pending']) }}" 
                   class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request('status') === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Pendientes
                </a>
                <a href="{{ route('infrastock.admin.supply-returns.index', ['status' => 'approved']) }}" 
                   class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request('status') === 'approved' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Aprobadas
                </a>
                <a href="{{ route('infrastock.admin.supply-returns.index', ['status' => 'rejected']) }}" 
                   class="px-3 py-1.5 rounded-md text-sm font-medium transition-colors {{ request('status') === 'rejected' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Rechazadas
                </a>
            </div>
        </div>

        <!-- Tabla de Devoluciones -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                    <i class="fas fa-undo-alt mr-2 text-green-600"></i>
                    Lista de Devoluciones
                </h3>
                @if($surpluses->total() > 0)
                    <span class="text-sm text-gray-500">
                        Mostrando {{ $surpluses->firstItem() ?? 0 }} - {{ $surpluses->lastItem() ?? 0 }} de {{ $surpluses->total() }} devoluciones
                    </span>
                @endif
            </div>
                
            @if($surpluses->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insumo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($surpluses as $surplus)
                                <tr class="hover:bg-gray-50 transition-colors duration-150 {{ $surplus->status !== 'pending' ? 'opacity-75' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $surplus->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8">
                                                <img class="h-8 w-8 rounded-full" src="{{ asset('AdminLTE/dist/img/user2-160x160.jpg') }}" alt="">
                                            </div>
                                            <div class="ml-3">
                                                @php
                                                    $roleName = 'Usuario';
                                                    if ($surplus->user && $surplus->user->relationLoaded('roles')) {
                                                        $userRoles = $surplus->user->roles->pluck('name')->toArray();
                                                    } else {
                                                        $user = $surplus->user;
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
                                                            $roleName = $userRoles[0] ?? 'Usuario';
                                                        }
                                                    }
                                                @endphp
                                                <div class="text-sm font-medium text-gray-900">{{ $surplus->user->name ?? 'Usuario' }}</div>
                                                <div class="text-sm text-gray-500">{{ $surplus->user->email ?? 'N/A' }}</div>
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">{{ $roleName }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <div class="font-medium">{{ $surplus->equipment->name ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-500">{{ $surplus->equipment->category->name ?? 'Sin categoría' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $surplus->surplus_amount }} {{ $surplus->equipment->unit_measure ?? 'unidades' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            {{ \Illuminate\Support\Str::limit($surplus->reason ?? 'Sin motivo especificado', 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $surplus->surplus_date->format('d/m/Y') }}</div>
                                        <div class="text-xs">{{ $surplus->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($surplus->status === 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pendiente
                                            </span>
                                        @elseif($surplus->status === 'approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Aprobada
                                            </span>
                                            @if($surplus->processed_at)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Por: {{ $surplus->processed_by ?? 'Admin' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $surplus->processed_at->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        @elseif($surplus->status === 'rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <i class="fas fa-times-circle mr-1"></i>
                                                Rechazada
                                            </span>
                                            @if($surplus->processed_at)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Por: {{ $surplus->processed_by ?? 'Admin' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $surplus->processed_at->format('d/m/Y H:i') }}
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @if($surplus->status === 'pending')
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors" 
                                                        onclick="approveReturn({{ $surplus->id }})"
                                                        title="Aprobar devolución">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Aprobar
                                                </button>
                                                <button type="button" 
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors" 
                                                        onclick="showRejectModal({{ $surplus->id }})"
                                                        title="Rechazar devolución">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Rechazar
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Procesada</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($surpluses->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $surpluses->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i class="fas fa-undo-alt text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay devoluciones</h3>
                    <p class="text-gray-500">
                        @if(request('status') && request('status') !== 'all')
                            No se han encontrado devoluciones con estado "{{ request('status') }}".
                        @else
                            No se han encontrado devoluciones de insumos.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Función para aprobar devolución
    function approveReturn(surplusId) {
        Swal.fire({
            title: '¿Aprobar devolución?',
            text: '¿Estás seguro de que deseas aprobar esta devolución? El stock será incrementado.',
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
                form.action = `/infrastock/admin/supply-returns/${surplusId}/approve`;
                
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

    // Función para mostrar el modal de rechazo
    function showRejectModal(surplusId) {
        Swal.fire({
            title: 'Rechazar devolución',
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
                form.action = `/infrastock/admin/supply-returns/${surplusId}/reject`;
                
                // Agregar token CSRF
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
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

