@extends('infrastock::layouts.master')

@section('title', 'Reporte de Herramientas por Instructor')

@section('breadcrumb-items')
    <li class="flex items-center">
        <a href="{{ route('cefa.infrastock.admin.dashboard') }}" class="text-green-600 hover:text-green-800">Dashboard</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
    <li class="flex items-center">
        <span class="text-gray-500">Herramientas por Instructor</span>
    </li>
@endsection

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Encabezado -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-tools text-blue-600 mr-3"></i>
                Uso de Herramientas por Instructor
            </h2>
            <p class="text-gray-600 mt-1">Análisis detallado del préstamo de herramientas por instructor</p>
        </div>
        <div class="flex items-center space-x-2">
            <!-- Filtros -->
            <form method="GET" class="flex items-center space-x-2">
                <select name="year" onchange="this.form.submit()" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <select name="month" onchange="this.form.submit()" class="px-3 py-2 bg-white border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Todo el año</option>
                    @foreach($availableMonths as $m)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ $monthNames[$m] }}</option>
                    @endforeach
                </select>
            </form>
            <!-- Exportar PDF -->
            <a href="{{ route('infrastock.admin.reports.tools-by-instructor.pdf', ['year' => $year, 'month' => $month]) }}" 
               class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 transition-colors duration-200 flex items-center"
               title="Exportar a PDF">
                <i class="fas fa-file-pdf mr-2"></i>PDF
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Préstamos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalLoans) }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-handshake text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-4 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Instructores Activos</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $totalInstructors }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Gráfico de Préstamos por Instructor -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                Distribución por Instructor
            </h3>
            <div class="relative h-64">
                <canvas id="instructorChart"></canvas>
            </div>
        </div>

        <!-- Top Herramientas Prestadas -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4 flex items-center">
                <i class="fas fa-wrench mr-2 text-yellow-500"></i>
                Top 10 Herramientas Más Prestadas
            </h3>
            @if($topTools->count() > 0)
            <div class="space-y-3 max-h-64 overflow-y-auto">
                @foreach($topTools as $index => $tool)
                <div class="flex items-center justify-between p-2 rounded-lg {{ $index < 3 ? 'bg-blue-50' : 'bg-gray-50' }}">
                    <div class="flex items-center">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold {{ $index < 3 ? 'bg-blue-400 text-white' : 'bg-gray-300 text-gray-700' }} mr-3">
                            {{ $index + 1 }}
                        </span>
                        <span class="text-sm font-medium text-gray-800">{{ $tool->tool->name ?? 'N/A' }}</span>
                    </div>
                    <span class="text-sm font-bold text-blue-600">{{ number_format($tool->total_loans) }}</span>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-gray-500 text-center py-4">No hay datos disponibles</p>
            @endif
        </div>
    </div>

    <!-- Tabla de Préstamos por Instructor -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                <i class="fas fa-table mr-2 text-blue-600"></i>
                Resumen por Instructor
            </h3>
        </div>
        @if($loansByInstructor->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Instructor</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total Préstamos</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase">% del Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($loansByInstructor as $index => $instructor)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <span class="text-sm font-medium text-gray-900">{{ $instructor->user_name }}</span>
                                    @if($instructor->user_nickname)
                                    <p class="text-xs text-gray-500">{{ $instructor->user_nickname }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ number_format($instructor->total_loans) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">
                            {{ $totalLoans > 0 ? number_format(($instructor->total_loans / $totalLoans) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <i class="fas fa-tools text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay datos</h3>
            <p class="text-gray-500">No se encontraron préstamos de herramientas para el período seleccionado.</p>
        </div>
        @endif
    </div>

    <!-- Detalle de Préstamos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700 flex items-center">
                <i class="fas fa-list mr-2 text-blue-600"></i>
                Detalle de Préstamos (Últimos 50)
            </h3>
        </div>
        @if($loanDetails->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Herramienta</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase">Instructor</th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-700 uppercase">Cantidad</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($loanDetails->take(50) as $detail)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $detail->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $detail->tool->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $detail->user->person->first_name ?? '' }} {{ $detail->user->person->first_last_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $detail->amount }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-12">
            <p class="text-gray-500">No hay préstamos para mostrar.</p>
        </div>
        @endif
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Datos para el gráfico
    const instructorData = {!! json_encode($loansByInstructor->pluck('total_loans')->toArray()) !!};
    const instructorLabels = {!! json_encode($loansByInstructor->pluck('user_name')->toArray()) !!};
    
    // Colores para el gráfico
    const colors = [
        '#3B82F6', '#8B5CF6', '#EC4899', '#10B981', '#F59E0B',
        '#EF4444', '#06B6D4', '#84CC16', '#F97316', '#6366F1'
    ];
    
    // Gráfico de barras horizontal
    new Chart(document.getElementById('instructorChart'), {
        type: 'bar',
        data: {
            labels: instructorLabels.slice(0, 10),
            datasets: [{
                label: 'Préstamos',
                data: instructorData.slice(0, 10),
                backgroundColor: colors.slice(0, Math.min(instructorData.length, 10)),
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: '#e5e7eb'
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection
