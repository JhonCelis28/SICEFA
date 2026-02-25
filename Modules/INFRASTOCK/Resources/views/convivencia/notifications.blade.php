@extends('infrastock::layouts.usuarios-master')

@section('title', 'Notificaciones - Centro de Convivencia INFRASTOCK')

@section('content')
<!-- Breadcrumb -->
@section('breadcrumb-items')
<li class="text-gray-700">Notificaciones</li>
@endsection
        
<!-- Header -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-900">Notificaciones</h2>
    <p class="text-gray-600 mt-2">Mantente informado sobre el estado de tus solicitudes de insumos.</p>
</div>

<!-- Estadísticas -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-bell text-blue-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Total Notificaciones</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $notifications->total() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Aprobadas</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $notifications->where('type', 'request_approved')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Rechazadas</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $notifications->where('type', 'request_rejected')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-truck text-blue-600"></i>
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-500">Entregadas</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $notifications->filter(function($n) { return strpos($n->type ?? '', 'delivered') !== false; })->count() }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Notificaciones -->
<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Notificaciones Recientes</h3>
        <p class="text-sm text-gray-500 mt-1">Total: {{ $notifications->total() }} notificaciones</p>
    </div>
    
    @if($notifications->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($notifications as $notification)
                @php
                    $notificationData = $notification->data ?? [];
                    $requestId = $notificationData['request_id'] ?? null;
                    $request = isset($requests[$requestId]) ? $requests[$requestId] : null;
                    $notificationType = $notification->type ?? '';
                @endphp
                
                <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                    <div class="flex items-start space-x-4">
                        <!-- Icono de notificación -->
                        <div class="flex-shrink-0">
                            @if($notificationType == 'request_approved')
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                </div>
                            @elseif($notificationType == 'request_rejected')
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                                </div>
                            @elseif($notificationType == 'request_delivered' || strpos($notificationType, 'delivered') !== false)
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-truck text-blue-600 text-xl"></i>
                                </div>
                            @else
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-bell text-yellow-600 text-xl"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Contenido de la notificación -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-1">
                                        {{ $notificationData['title'] ?? 'Notificación' }}
                                    </h4>
                                    <p class="text-sm text-gray-600 mb-2">
                                        {{ $notificationData['message'] ?? 'No hay mensaje disponible' }}
                                    </p>
                                </div>
                                <div class="flex flex-col items-end ml-4">
                                    <span class="text-xs text-gray-400 mb-1">{{ $notification->created_at->diffForHumans() }}</span>
                                    <span class="text-xs text-gray-500">{{ $notification->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>

                            @if($request)
                                <!-- Detalles completos de la solicitud -->
                                <div class="mt-4 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <!-- Información básica -->
                                        <div>
                                            <h5 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                                Información de la Solicitud
                                            </h5>
                                            <div class="space-y-1 text-sm">
                                                <div class="flex items-center">
                                                    <span class="text-gray-600 font-medium w-32">ID Solicitud:</span>
                                                    <span class="text-gray-900 font-semibold">#{{ $request->id }}</span>
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="text-gray-600 font-medium w-32">Estado:</span>
                                                    @if($request->status == 'pending')
                                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-md text-xs font-medium">Pendiente</span>
                                                    @elseif($request->status == 'approved')
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-md text-xs font-medium">Aprobada</span>
                                                    @elseif($request->status == 'rejected')
                                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-md text-xs font-medium">Rechazada</span>
                                                    @elseif($request->status == 'delivered')
                                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-xs font-medium">Entregada</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center">
                                                    <span class="text-gray-600 font-medium w-32">Fecha Creación:</span>
                                                    <span class="text-gray-900">{{ $request->created_at->format('d/m/Y H:i') }}</span>
                                                </div>
                                                @if($request->approved_at)
                                                    <div class="flex items-center">
                                                        <span class="text-gray-600 font-medium w-32">Fecha Aprobación:</span>
                                                        <span class="text-gray-900">{{ $request->approved_at->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                @endif
                                                @if($request->rejected_at)
                                                    <div class="flex items-center">
                                                        <span class="text-gray-600 font-medium w-32">Fecha Rechazo:</span>
                                                        <span class="text-gray-900">{{ $request->rejected_at->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Unidad Productiva y Almacén -->
                                        @if($request->productiveUnitWarehouse)
                                            <div>
                                                <h5 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                    <i class="fas fa-warehouse mr-2 text-blue-500"></i>
                                                    Ubicación
                                                </h5>
                                                <div class="space-y-1 text-sm">
                                                    <div class="flex items-center">
                                                        <span class="text-gray-600 font-medium w-32">Unidad Productiva:</span>
                                                        <span class="text-gray-900">{{ $request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }}</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <span class="text-gray-600 font-medium w-32">Almacén:</span>
                                                        <span class="text-gray-900">{{ $request->productiveUnitWarehouse->warehouse->name ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Lista de Items -->
                                    @if($request->items && $request->items->count() > 0)
                                        <div class="mb-4">
                                            <h5 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                <i class="fas fa-list mr-2 text-blue-500"></i>
                                                Insumos Solicitados ({{ $request->items->count() }})
                                            </h5>
                                            <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
                                                <div class="divide-y divide-gray-200">
                                                    @foreach($request->items as $item)
                                                        <div class="p-3 hover:bg-gray-50 transition-colors">
                                                            <div class="flex items-center justify-between">
                                                                <div class="flex-1">
                                                                    <div class="flex items-center space-x-2">
                                                                        <span class="font-medium text-gray-900">{{ $item->equipment->name ?? 'Insumo no disponible' }}</span>
                                                                        @if($item->equipment && $item->equipment->category)
                                                                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">
                                                                                {{ $item->equipment->category->name }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                    @if($item->equipment && $item->equipment->description)
                                                                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($item->equipment->description, 60) }}</p>
                                                                    @endif
                                                                </div>
                                                                <div class="flex items-center space-x-4 ml-4">
                                                                    <div class="text-right">
                                                                        <div class="text-sm font-semibold text-gray-900">
                                                                            {{ number_format($item->requested_amount, 0) }} {{ $item->equipment->unit ?? 'unidades' }}
                                                                        </div>
                                                                        @if($item->status)
                                                                            <div class="text-xs mt-1">
                                                                                @if($item->status == 'approved')
                                                                                    <span class="text-green-600">✓ Aprobado</span>
                                                                                @elseif($item->status == 'rejected')
                                                                                    <span class="text-red-600">✗ Rechazado</span>
                                                                                @elseif($item->status == 'delivered')
                                                                                    <span class="text-blue-600">🚚 Entregado</span>
                                                                                @else
                                                                                    <span class="text-yellow-600">⏳ Pendiente</span>
                                                                                @endif
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Descripción -->
                                    @if($request->description)
                                        <div class="mb-4">
                                            <h5 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                                <i class="fas fa-comment mr-2 text-blue-500"></i>
                                                Descripción
                                            </h5>
                                            <p class="text-sm text-gray-700 bg-white p-3 rounded-md border border-gray-200">
                                                {{ $request->description }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Razón de Rechazo -->
                                    @if($request->status == 'rejected' && $request->rejection_reason)
                                        <div class="mb-4">
                                            <h5 class="text-sm font-semibold text-red-700 mb-2 flex items-center">
                                                <i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>
                                                Razón de Rechazo
                                            </h5>
                                            <p class="text-sm text-red-700 bg-red-50 p-3 rounded-md border border-red-200">
                                                {{ $request->rejection_reason }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Botón para ver detalles completos -->
                                    <div class="flex justify-end mt-4">
                                        <a href="{{ route('infrastock.convivencia.requests.show', $request->id) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-md hover:bg-blue-600 transition-colors duration-200">
                                            <i class="fas fa-eye mr-2"></i>
                                            Ver Detalles Completos
                                        </a>
                                    </div>
                                </div>
                            @elseif(isset($notificationData['request_id']))
                                <!-- Si no se encontró la solicitud pero hay un request_id -->
                                <div class="mt-4 bg-yellow-50 rounded-lg p-4 border border-yellow-200">
                                    <p class="text-sm text-yellow-800">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        La solicitud #{{ $notificationData['request_id'] }} no está disponible o fue eliminada.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-bell-slash text-gray-400 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay notificaciones</h3>
            <p class="text-gray-500 mb-6">No tienes notificaciones pendientes en este momento.</p>
            <a href="{{ route('infrastock.convivencia.requests.index') }}?open_modal=1" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>
                Crear nueva solicitud
            </a>
        </div>
    @endif
</div>
@endsection

@section('script')
<script>
</script>
@endsection


