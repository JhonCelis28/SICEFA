<!--
    * @file notifications.blade.php
    * @brief Vista para mostrar las notificaciones del Personal de Aseo.
    *
    * Esta vista presenta todas las notificaciones relacionadas con el estado
    * de las solicitudes del usuario, incluyendo cambios de estado, aprobaciones,
    * rechazos y entregas. Permite al usuario mantenerse informado sobre el
    * progreso de sus solicitudes.
    * Utiliza Tailwind CSS para un diseño responsive y moderno.
    *
    * @param Collection $notifications Notificaciones paginadas del usuario actual.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.usuarios-master')

@section('title', 'Notificaciones - Personal de Aseo INFRASTOCK')

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
                                        <!-- Detalles de la solicitud -->
                                        <div class="mt-4 bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <h5 class="text-sm font-semibold text-gray-700 mb-2">ID Solicitud: #{{ $request->id }}</h5>
                                                    <p class="text-sm text-gray-600">
                                                        <span class="font-medium">Estado:</span>
                                                        @if($request->status == 'pending')
                                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pendiente</span>
                                                        @elseif($request->status == 'approved')
                                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Aprobada</span>
                                                        @elseif($request->status == 'rejected')
                                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Rechazada</span>
                                                        @elseif($request->status == 'delivered')
                                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Entregada</span>
                                                        @endif
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-sm text-gray-600">
                                                        <span class="font-medium">Fecha:</span> {{ $request->created_at->format('d/m/Y H:i') }}
                                                    </p>
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        <span class="font-medium">Total Items:</span> {{ $request->items->count() }}
                                                    </p>
                                                </div>
                                            </div>
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
                    <a href="{{ route('infrastock.cleaning-staff.requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Crear nueva solicitud
                    </a>
                </div>
            @endif
        </div>
@endsection

@section('script')
<script>
    // Script específico para la vista de notificaciones
</script>
@endsection
