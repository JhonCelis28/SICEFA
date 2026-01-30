<!--
    * @file dashboard.blade.php
    * @brief Vista del panel de control principal para el módulo INFRASTOCK.
    *
    * Esta vista presenta un resumen interactivo y en tiempo real de los datos clave
    * del módulo INFRASTOCK, incluyendo estadísticas de insumos, herramientas y movimientos.
    * Utiliza Tailwind CSS para un diseño responsive y Chart.js para visualizar datos
    * de consumo por área y uso de herramientas por instructor.
    * Extiende la plantilla `master.blade.php` y define secciones para el título y scripts específicos.
    *
    * @param int $newSupplyRequestsCount Número de nuevas solicitudes de insumos pendientes.
    * @param float $suppliesPercentage Porcentaje de insumos en stock (o cantidad total).
    * @param int $toolsOnLoanCount Número de herramientas actualmente prestadas.
    * @param int $expiringSuppliesCount Número de insumos próximos a vencer.
    * @param array $areaNames Nombres de las áreas para el gráfico de consumo.
    * @param array $consumptionAmounts Cantidades consumidas por área para el gráfico.
    * @param array $instructorNames Nombres de los instructores para la lista de uso de herramientas.
    * @param array $loanCounts Conteo de préstamos por instructor para la lista de uso de herramientas.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Dashboard')

@section('content')

    <!-- Sección de tarjetas de resumen (Small Boxes) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-6">

        <!-- Tarjeta: Nuevas Solicitudes de Insumos -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $newSupplyRequestsCount }}</h3>
                <p class="text-base text-gray-500 mt-1">Nuevas Solicitudes</p>
            </div>
            <div class="text-green-500 text-4xl opacity-75">
                <i class="fas fa-bell"></i>
            </div>
            <a href="{{ route('infrastock.admin.supply-requests.index') }}" class="absolute bottom-0 left-0 right-0 bg-green-100 text-green-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Solicitudes <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>

        <!-- Tarjeta: Insumos en Stock (porcentaje o cantidad total) -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">
                    @if(is_numeric($suppliesPercentage) && $suppliesPercentage <= 100)
                        {{ $suppliesPercentage }}<sup class="text-lg">%</sup>
                    @else
                        {{ number_format($suppliesPercentage, 0) }}
                    @endif
                </h3>
                <p class="text-base text-gray-500 mt-1">Insumos en Stock</p>
            </div>
            <div class="text-blue-500 text-4xl opacity-75">
                <i class="fas fa-boxes"></i>
            </div>
            <a href="{{ route('infrastock.admin.supplies.index') }}" class="absolute bottom-0 left-0 right-0 bg-blue-100 text-blue-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Más Información <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>

        <!-- Tarjeta: Herramientas Prestadas -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $toolsOnLoanCount }}</h3>
                <p class="text-base text-gray-500 mt-1">Herramientas Prestadas</p>
            </div>
            <div class="text-yellow-500 text-4xl opacity-75">
                <i class="fas fa-tools"></i>
            </div>
            <a href="{{ route('infrastock.admin.loans.index') }}" class="absolute bottom-0 left-0 right-0 bg-yellow-100 text-yellow-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Herramientas <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>

        <!-- Tarjeta: Insumos Próximos a Vencer -->
        <div class="bg-white rounded-xl shadow-md p-6 flex items-center justify-between transition-transform transform hover:scale-105 duration-200 relative">
            <div>
                <h3 class="text-3xl font-extrabold text-gray-800">{{ $expiringSuppliesCount }}</h3>
                <p class="text-base text-gray-500 mt-1">Insumos Próximos a Vencer</p>
            </div>
            <div class="text-red-500 text-4xl opacity-75">
                <i class="fas fa-calendar-times"></i>
            </div>
            <a href="{{ route('infrastock.admin.supplies.index', ['filter_expiring' => true]) }}" class="absolute bottom-0 left-0 right-0 bg-red-100 text-red-700 text-center px-4 py-2 rounded-b-xl text-sm font-medium hover:brightness-95 transition-all duration-200">Ver Vencimientos <i class="fas fa-arrow-circle-right ml-1"></i></a>
        </div>
    </div>

    <!-- Sección de Gráficos y Listas -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Gráfico de Consumo de Insumos por Área -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Consumo de Insumos por Área</h3>
                    <a href="javascript:void(0);" class="px-4 py-2 bg-green-500 text-white rounded-md text-base font-medium hover:bg-green-600 transition-colors duration-200">Ver Reporte</a>
                </div>
                <div class="relative h-64">
                    <canvas id="consumption-chart"></canvas>
                </div>
            </div>
        </div>

        <!-- Lista de Uso de Herramientas por Instructor -->
        <div class="lg:col-span-5">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Uso de Herramientas por Instructor</h3>
                    <div class="flex space-x-3">
                        <a href="#" class="text-gray-500 hover:text-green-600 transition-colors duration-200 p-2 rounded-md hover:bg-gray-100"><i class="fas fa-download text-base"></i></a>
                        <a href="#" class="text-gray-500 hover:text-green-600 transition-colors duration-200 p-2 rounded-md hover:bg-gray-100"><i class="fas fa-bars text-base"></i></a>
                    </div>
                </div>
                <ul class="divide-y divide-gray-200">
                    @foreach($instructorNames as $key => $instructorName)
                        <li class="py-3 flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-lg font-bold text-gray-900">{{ $instructorName }}</p>
                                <p class="text-sm text-gray-600 mt-1">Herramientas Prestadas</p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <span class="inline-flex items-center px-4 py-1 rounded-full text-base font-semibold bg-green-100 text-green-800">{{ $loanCounts[$key] }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endsection

@section('script')
<!-- Script para integrar Chart.js y mostrar el gráfico de consumo de insumos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Configuración y renderizado del gráfico de barras para el consumo de insumos por área.
    var ctx1 = document.getElementById('consumption-chart').getContext('2d');
    var consumptionChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: {!! json_encode($areaNames) !!},
            datasets: [{
                label: 'Cantidad Consumida',
                backgroundColor: '#34D399', // green-400
                borderColor: '#10B981', // green-500
                data: {!! json_encode($consumptionAmounts) !!}
            }]
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                x: {
                    stacked: true,
                    grid: { display: false }
                },
                y: {
                    stacked: true,
                    beginAtZero: true,
                    grid: { color: '#e5e7eb' } // gray-200
                }
            }
        }
    });
</script>
@endsection