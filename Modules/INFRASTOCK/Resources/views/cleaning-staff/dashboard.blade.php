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
            <a href="{{ route('infrastock.cleaning-staff.requests.index') }}?status=pending" class="absolute bottom-0 left-0 right-0 bg-yellow-100 text-yellow-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Pendientes <i class="fas fa-arrow-circle-right ml-1"></i></a>
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
            <a href="{{ route('infrastock.cleaning-staff.requests.index') }}?status=approved" class="absolute bottom-0 left-0 right-0 bg-green-100 text-green-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Aprobadas <i class="fas fa-arrow-circle-right ml-1"></i></a>
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
            <a href="{{ route('infrastock.cleaning-staff.requests.index') }}?status=rejected" class="absolute bottom-0 left-0 right-0 bg-red-100 text-red-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Rechazadas <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>
    </div>


    <!-- Sección del Insumo Más Solicitado -->
    <div class="mt-6">
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Insumo Más Solicitado</h3>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-trophy text-yellow-500 text-xl"></i>
                    <span class="text-sm text-gray-500">Estadística general</span>
                </div>
            </div>
            
            @if($mostRequestedSupplyData)
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-6 border border-yellow-200">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="bg-yellow-100 p-3 rounded-full">
                                    <i class="fas fa-box text-yellow-600 text-xl"></i>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-bold text-gray-900">{{ $mostRequestedSupplyData['equipment']->name }}</h4>
                                    <p class="text-gray-600">{{ $mostRequestedSupplyData['equipment']->category->name ?? 'Sin categoría' }}</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-chart-line text-blue-500"></i>
                                        <span class="text-sm font-medium text-gray-700">Total de Solicitudes</span>
                                    </div>
                                    <p class="text-2xl font-bold text-blue-600 mt-2">{{ $mostRequestedSupplyData['request_count'] }}</p>
                                </div>
                                
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-cubes text-green-500"></i>
                                        <span class="text-sm font-medium text-gray-700">Cantidad Total Solicitada</span>
                                    </div>
                                    <p class="text-2xl font-bold text-green-600 mt-2">{{ $mostRequestedSupplyData['total_amount_requested'] }} {{ $mostRequestedSupplyData['equipment']->unit ?? 'unidades' }}</p>
                                </div>
                                
                                <div class="bg-white rounded-lg p-4 shadow-sm">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-warehouse text-purple-500"></i>
                                        <span class="text-sm font-medium text-gray-700">Stock Actual</span>
                                    </div>
                                    <p class="text-2xl font-bold text-purple-600 mt-2">{{ $mostRequestedSupplyData['equipment']->stock }} {{ $mostRequestedSupplyData['equipment']->unit ?? 'unidades' }}</p>
                                </div>
                            </div>
                            
                            @if($mostRequestedSupplyData['equipment']->description)
                                <div class="mt-4 p-4 bg-white rounded-lg shadow-sm">
                                    <h5 class="font-medium text-gray-700 mb-2">Descripción:</h5>
                                    <p class="text-gray-600">{{ $mostRequestedSupplyData['equipment']->description }}</p>
                                </div>
                            @endif
                            
                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span><i class="fas fa-calendar mr-1"></i> Última actualización: {{ $mostRequestedSupplyData['equipment']->updated_at->format('d/m/Y') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($mostRequestedSupplyData['equipment']->stock > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Disponible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Agotado
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="ml-6">
                            <div class="bg-yellow-100 p-4 rounded-lg text-center">
                                <i class="fas fa-crown text-yellow-600 text-3xl mb-2"></i>
                                <p class="text-sm font-medium text-yellow-800">#1 Más Solicitado</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-lg">
                    <i class="fas fa-chart-bar text-gray-400 text-4xl mb-4"></i>
                    <h4 class="text-lg font-medium text-gray-900 mb-2">No hay datos suficientes</h4>
                    <p class="text-gray-500 mb-4">Aún no se han registrado suficientes solicitudes para determinar el insumo más solicitado.</p>
                    <a href="{{ route('infrastock.cleaning-staff.requests.create') }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        Crear primera solicitud
                    </a>
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