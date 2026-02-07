<!--
    * @file dashboard.blade.php
    * @brief Vista del dashboard principal para Instructores en el módulo INFRASTOCK.
    *
    * Esta vista presenta un resumen interactivo y en tiempo real de las funcionalidades
    * específicas para instructores, incluyendo estadísticas de préstamos de herramientas,
    * notificaciones y herramientas más prestadas.
    * Utiliza Tailwind CSS para un diseño responsive.
    * Extiende la plantilla `instructor-master.blade.php` y define secciones para el título y scripts específicos.
    *
    * @param int $activeLoans Número de préstamos activos del instructor.
    * @param int $totalLoans Número total de préstamos del instructor.
    * @param int $returnedLoans Número de préstamos devueltos del instructor.
    * @param array $mostLoanedTools Datos de la herramienta más prestada.
    * @param Collection $notifications Notificaciones recientes del instructor.
    * @param Collection $recentLoans Préstamos recientes del instructor.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.instructor-master')

@section('title', 'Dashboard - Instructor INFRASTOCK')

@section('content')

    <!-- Sección de tarjetas de resumen (Small Boxes) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-6">

        <!-- Tarjeta: Préstamos Activos -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $activeLoans }}</h3>
                <p class="text-base text-gray-500 mt-1">Préstamos Activos</p>
            </div>
            <div class="text-blue-500 text-4xl opacity-75">
                <i class="fas fa-tools"></i>
            </div>
            <a href="{{ route('infrastock.instructor.my-loans') }}?role=Préstamo" class="absolute bottom-0 left-0 right-0 bg-blue-100 text-blue-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Activos <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>

        <!-- Tarjeta: Total de Préstamos -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $totalLoans }}</h3>
                <p class="text-base text-gray-500 mt-1">Total de Préstamos</p>
            </div>
            <div class="text-green-500 text-4xl opacity-75">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <a href="{{ route('infrastock.instructor.my-loans') }}" class="absolute bottom-0 left-0 right-0 bg-green-100 text-green-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Todos <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>

        <!-- Tarjeta: Devoluciones -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $returnedLoans }}</h3>
                <p class="text-base text-gray-500 mt-1">Devoluciones</p>
            </div>
            <div class="text-purple-500 text-4xl opacity-75">
                <i class="fas fa-check-circle"></i>
            </div>
            <a href="{{ route('infrastock.instructor.my-loans') }}?role=Devolución" class="absolute bottom-0 left-0 right-0 bg-purple-100 text-purple-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Devoluciones <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>

        <!-- Tarjeta: Notificaciones -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $notificationCount ?? 0 }}</h3>
                <p class="text-base text-gray-500 mt-1">Notificaciones</p>
            </div>
            <div class="text-yellow-500 text-4xl opacity-75">
                <i class="fas fa-bell"></i>
            </div>
            <a href="{{ route('infrastock.instructor.notifications') }}" class="absolute bottom-0 left-0 right-0 bg-yellow-100 text-yellow-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Notificaciones <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>
    </div>

    <!-- Sección de Gráficos y Listas -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Herramienta Más Prestada -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Herramienta Más Prestada</h3>
                    <a href="{{ route('infrastock.instructor.my-loans') }}" class="px-4 py-2 bg-green-500 text-white rounded-md text-base font-medium hover:bg-green-600 transition-colors duration-200">Ver Préstamos</a>
                </div>
                <div class="relative">
                    @if($mostLoanedTools && $mostLoanedTools['tool'])
                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg p-6 border border-yellow-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-4">
                                        <div class="bg-yellow-100 p-3 rounded-full">
                                            <i class="fas fa-tools text-yellow-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-2xl font-bold text-gray-900">{{ $mostLoanedTools['tool']->nombre ?? 'N/A' }}</h4>
                                            @if($mostLoanedTools['tool']->placa)
                                                <p class="text-gray-600">Placa: {{ $mostLoanedTools['tool']->placa }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div class="bg-white rounded-lg p-4 shadow-sm">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-chart-line text-blue-500"></i>
                                                <span class="text-sm font-medium text-gray-700">Total de Préstamos</span>
                                            </div>
                                            <p class="text-2xl font-bold text-blue-600 mt-2">{{ $mostLoanedTools['loan_count'] }}</p>
                                        </div>
                                        
                                        @if($mostLoanedTools['tool']->descripcion_actual)
                                        <div class="bg-white rounded-lg p-4 shadow-sm">
                                            <div class="flex items-center space-x-2">
                                                <i class="fas fa-info-circle text-green-500"></i>
                                                <span class="text-sm font-medium text-gray-700">Descripción</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-2">{{ Str::limit($mostLoanedTools['tool']->descripcion_actual, 50) }}</p>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    @if($mostLoanedTools['tool']->atributos)
                                        <div class="mt-4 p-4 bg-white rounded-lg shadow-sm">
                                            <h5 class="font-medium text-gray-700 mb-2">Atributos:</h5>
                                            <p class="text-gray-600">{{ Str::limit($mostLoanedTools['tool']->atributos, 100) }}</p>
                                        </div>
                                    @endif
                                    
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            @if($mostLoanedTools['tool']->fecha_adquisicion)
                                                <span><i class="fas fa-calendar mr-1"></i> Adquirida: {{ \Carbon\Carbon::parse($mostLoanedTools['tool']->fecha_adquisicion)->format('d/m/Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="ml-6">
                                    <div class="bg-yellow-100 p-4 rounded-lg text-center">
                                        <i class="fas fa-crown text-yellow-600 text-3xl mb-2"></i>
                                        <p class="text-sm font-medium text-yellow-800">#1 Más Prestada</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 bg-gray-50 rounded-lg">
                            <i class="fas fa-tools text-gray-400 text-4xl mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No hay datos suficientes</h4>
                            <p class="text-gray-500 mb-4">Aún no has registrado préstamos de herramientas.</p>
                            <a href="{{ route('infrastock.instructor.my-loans') }}" class="inline-flex items-center px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition-colors duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Registrar primer préstamo
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Lista de Préstamos Recientes / Notificaciones -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Préstamos Recientes</h3>
                    <div class="flex space-x-3">
                        <a href="{{ route('infrastock.instructor.my-loans') }}" class="text-gray-500 hover:text-green-600 transition-colors duration-200 p-2 rounded-md hover:bg-gray-100"><i class="fas fa-tools text-base"></i></a>
                        <a href="{{ route('infrastock.instructor.my-loans') }}" class="text-gray-500 hover:text-green-600 transition-colors duration-200 p-2 rounded-md hover:bg-gray-100"><i class="fas fa-bars text-base"></i></a>
                    </div>
                </div>
                <ul class="divide-y divide-gray-200">
                    @forelse(($recentLoans ?? collect())->take(5) as $loan)
                        <li class="py-3 flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-lg font-bold text-gray-900">{{ $loan->tool->nombre ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-600 mt-1">Préstamo de herramienta</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $loan->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Activo</span>
                            </div>
                        </li>
                    @empty
                        <li class="py-8 text-center">
                            <i class="fas fa-tools text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500">No hay préstamos recientes</p>
                        </li>
                    @endforelse
                </ul>
                @if(($recentLoans ?? collect())->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('infrastock.instructor.my-loans') }}" class="text-green-600 hover:text-green-700 font-medium text-sm">
                            Ver todos los préstamos <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('script')
<!-- Script para funcionalidades adicionales si es necesario -->
<script>
    console.log('Dashboard del Instructor cargado correctamente');
</script>
@endsection
