<!--
    * @file dashboard.blade.php
    * @brief Vista del dashboard principal para el Personal de Aseo en el módulo INFRASTOCK.
    *
    * Esta vista presenta un resumen interactivo y en tiempo real de las funcionalidades
    * específicas para el personal de aseo, incluyendo estadísticas de solicitudes,
    * notificaciones, insumos disponibles y acceso rápido a todas las funcionalidades.
    * Utiliza Tailwind CSS para un diseño responsive y Chart.js para visualizar datos.
    * Extiende la plantilla `master.blade.php` y define secciones para el título y scripts específicos.
    *
    * @param int $pendingRequests Número de solicitudes pendientes del usuario.
    * @param int $approvedRequests Número de solicitudes aprobadas del usuario.
    * @param int $deliveredRequests Número de solicitudes entregadas del usuario.
    * @param int $rejectedRequests Número de solicitudes rechazadas del usuario.
    * @param Collection $recentRequests Solicitudes recientes del usuario.
    * @param Collection $notifications Notificaciones recientes del usuario.
    * @param Collection $availableSupplies Insumos disponibles para solicitar.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.cleaning-staff-master')

@section('title', 'Dashboard - Personal de Aseo INFRASTOCK')

@section('content')

    <!-- Sección de tarjetas de resumen (Small Boxes) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <!-- Tarjeta: Solicitudes Pendientes -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $pendingRequests }}</h3>
                <p class="text-base text-gray-500 mt-1">Solicitudes Pendientes</p>
            </div>
            <div class="text-yellow-500 text-4xl opacity-75">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{ route('infrastock.cleaning-staff.requests.index') }}" class="absolute bottom-0 left-0 right-0 bg-yellow-100 text-yellow-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Solicitudes <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>

        <!-- Tarjeta: Solicitudes Aprobadas -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $approvedRequests }}</h3>
                <p class="text-base text-gray-500 mt-1">Solicitudes Aprobadas</p>
            </div>
            <div class="text-green-500 text-4xl opacity-75">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('infrastock.cleaning-staff.requests.index') }}" class="absolute bottom-0 left-0 right-0 bg-green-100 text-green-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Aprobadas <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>

        <!-- Tarjeta: Solicitudes Entregadas -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $deliveredRequests }}</h3>
                <p class="text-base text-gray-500 mt-1">Solicitudes Entregadas</p>
            </div>
            <div class="text-blue-500 text-4xl opacity-75">
                <i class="fas fa-truck"></i>
            </div>
            <a href="{{ route('infrastock.cleaning-staff.requests.index') }}" class="absolute bottom-0 left-0 right-0 bg-blue-100 text-blue-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Entregas <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>

        <!-- Tarjeta: Solicitudes Rechazadas -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $rejectedRequests }}</h3>
                <p class="text-base text-gray-500 mt-1">Solicitudes Rechazadas</p>
            </div>
            <div class="text-red-500 text-4xl opacity-75">
                <i class="fas fa-times-circle"></i>
            </div>
            <a href="{{ route('infrastock.cleaning-staff.requests.index') }}" class="absolute bottom-0 left-0 right-0 bg-red-100 text-red-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Rechazadas <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>
    </div>

    <!-- Sección de Gráficos y Listas -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Lista de Notificaciones Recientes -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Notificaciones Recientes</h3>
                    <a href="{{ route('infrastock.cleaning-staff.notifications') }}" class="px-4 py-2 bg-green-500 text-white rounded-md text-base font-medium hover:bg-green-600 transition-colors duration-200">Ver Todas</a>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($notifications as $notification)
                        <li class="py-3 flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-lg font-bold text-gray-900">{{ $notification->equipment->name ?? 'Insumo' }}</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    @switch($notification->role)
                                        @case('approved')
                                            <span class="text-green-600">✓ Aprobado</span>
                                            @break
                                        @case('rejected')
                                            <span class="text-red-600">✗ Rechazado</span>
                                            @break
                                        @case('delivered')
                                            <span class="text-blue-600">📦 Entregado</span>
                                            @break
                                        @default
                                            {{ ucfirst($notification->role) }}
                                    @endswitch
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">
                                    {{ $notification->amount }}
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="py-3 text-center text-gray-500">
                            <i class="fas fa-bell-slash text-2xl mb-2"></i>
                            <p>No hay notificaciones recientes</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Lista de Solicitudes Recientes -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Solicitudes Recientes</h3>
                    <div class="flex space-x-3">
                        <a href="{{ route('infrastock.cleaning-staff.requests.create') }}" class="text-gray-500 hover:text-green-600 transition-colors duration-200 p-2 rounded-md hover:bg-gray-100"><i class="fas fa-plus text-base"></i></a>
                        <a href="{{ route('infrastock.cleaning-staff.requests.index') }}" class="text-gray-500 hover:text-green-600 transition-colors duration-200 p-2 rounded-md hover:bg-gray-100"><i class="fas fa-list text-base"></i></a>
                    </div>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse($recentRequests as $request)
                        <li class="py-3 flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-lg font-bold text-gray-900">{{ $request->equipment->name ?? 'Insumo' }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $request->productiveUnitWarehouse->productiveUnit->name ?? 'Área' }}</p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold 
                                    @switch($request->role)
                                        @case('Solicitud')
                                            bg-yellow-100 text-yellow-800
                                            @break
                                        @case('approved')
                                            bg-green-100 text-green-800
                                            @break
                                        @case('rejected')
                                            bg-red-100 text-red-800
                                            @break
                                        @case('delivered')
                                            bg-blue-100 text-blue-800
                                            @break
                                        @default
                                            bg-gray-100 text-gray-800
                                    @endswitch">
                                    {{ $request->amount }}
                                </span>
                            </div>
                        </li>
                    @empty
                        <li class="py-3 text-center text-gray-500">
                            <i class="fas fa-clipboard-list text-2xl mb-2"></i>
                            <p>No tienes solicitudes recientes</p>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Sección de Acciones Rápidas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Acciones Rápidas -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Acciones Rápidas</h3>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('infrastock.cleaning-staff.requests.create') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors duration-200">
                    <i class="fas fa-plus-circle text-2xl text-green-600 mb-2"></i>
                    <span class="text-sm font-medium text-green-800">Nueva Solicitud</span>
                </a>
                <a href="{{ route('infrastock.cleaning-staff.notifications') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                    <i class="fas fa-bell text-2xl text-blue-600 mb-2"></i>
                    <span class="text-sm font-medium text-blue-800">Notificaciones</span>
                </a>
                <a href="{{ route('infrastock.cleaning-staff.profile') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                    <i class="fas fa-user text-2xl text-purple-600 mb-2"></i>
                    <span class="text-sm font-medium text-purple-800">Mi Perfil</span>
                </a>
                <a href="{{ route('infrastock.cleaning-staff.supply-history') }}" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors duration-200">
                    <i class="fas fa-history text-2xl text-orange-600 mb-2"></i>
                    <span class="text-sm font-medium text-orange-800">Historial</span>
                </a>
            </div>
        </div>

        <!-- Información de Stock Disponible -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Insumos Disponibles</h3>
                <span class="text-sm text-gray-500">{{ $availableSupplies->count() }} insumos disponibles</span>
            </div>
            @if($availableSupplies->count() > 0)
                <div class="space-y-3 max-h-64 overflow-y-auto">
                    @foreach($availableSupplies->take(8) as $supply)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $supply->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $supply->category->name ?? 'Sin categoría' }}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-lg font-bold text-green-600">{{ $supply->stock }}</span>
                                <p class="text-xs text-gray-500">disponible</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($availableSupplies->count() > 8)
                    <div class="text-center mt-4">
                        <a href="{{ route('infrastock.cleaning-staff.supply-history') }}" class="text-green-600 hover:text-green-700 font-medium">
                            Ver todos los insumos disponibles ({{ $availableSupplies->count() }})
                        </a>
                    </div>
                @endif
            @else
                <div class="text-center text-gray-500 py-8">
                    <i class="fas fa-box-open text-3xl mb-2"></i>
                    <p>No hay insumos disponibles</p>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('script')
<!-- Script para funcionalidades adicionales si es necesario -->
<script>
    // Aquí se pueden agregar scripts específicos para el personal de aseo
    console.log('Dashboard del Personal de Aseo cargado correctamente');
</script>
@endsection