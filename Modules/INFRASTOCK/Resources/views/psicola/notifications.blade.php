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

@section('title', 'Notificaciones - PSICOLA INFRASTOCK')

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
                        <p class="text-2xl font-semibold text-gray-900">{{ $notifications->where('role', 'approved')->count() }}</p>
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
                        <p class="text-2xl font-semibold text-gray-900">{{ $notifications->where('role', 'rejected')->count() }}</p>
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
                        <p class="text-2xl font-semibold text-gray-900">{{ $notifications->where('role', 'delivered')->count() }}</p>
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
                        <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex items-start space-x-4">
                                <!-- Icono de notificación -->
                                <div class="flex-shrink-0">
                                    @if($notification->role == 'approved')
                                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check-circle text-green-600"></i>
                                        </div>
                                    @elseif($notification->role == 'rejected')
                                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-times-circle text-red-600"></i>
                                        </div>
                                    @elseif($notification->role == 'delivered')
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-truck text-blue-600"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Contenido de la notificación -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">
                                                @if($notification->role == 'approved')
                                                    Solicitud Aprobada
                                                @elseif($notification->role == 'rejected')
                                                    Solicitud Rechazada
                                                @elseif($notification->role == 'delivered')
                                                    Insumo Entregado
                                                @endif
                                            </h4>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ $notification->equipment->name ?? 'Insumo' }} - Cantidad: {{ $notification->amount }}
                                            </p>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-400">{{ $notification->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    
                                    @if($notification->description)
                                        <p class="text-sm text-gray-600 mt-2">{{ $notification->description }}</p>
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
                    <a href="{{ route('infrastock.psicola.requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
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
    console.log('Vista de Notificaciones cargada correctamente');
</script>
@endsection
