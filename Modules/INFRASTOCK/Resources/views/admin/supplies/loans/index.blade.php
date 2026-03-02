<!--
    * @file loans/index.blade.php
    * @brief Vista para la gestión de Préstamos de Insumos registrados por el Admin en el módulo INFRASTOCK.
    *
    * Esta vista Blade permite al administrador visualizar todos los préstamos de insumos
    * que ha registrado. Utiliza Tailwind CSS para un diseño moderno y responsive.
    * Extiende la plantilla `master.blade.php` y define el título y los ítems de las migas de pan.
    *
    * @param Illuminate\Pagination\LengthAwarePaginator $loans Colección paginada de préstamos de insumos.
    * @param Modules\INFRASTOCK\Entities\Equipment[] $equipments Colección de insumos disponibles.
    * @param App\Models\User[] $users Colección de usuarios disponibles.
    * @param Modules\INFRASTOCK\Entities\ProductiveUnitWarehouse[] $productiveUnitWarehouses Colección de unidades productivas/almacenes.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Préstamos de Insumos')

@section('breadcrumb-items')
    <!-- Ítem de migas de pan para "Insumos" -->
    <li class="flex items-center">
        <a href="{{ route('infrastock.admin.supplies.index') }}" class="text-green-600 hover:text-green-800">Insumos</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
    <!-- Ítem de migas de pan para "Préstamos de Insumos" -->
    <li class="flex items-center">
        <a href="{{ route('infrastock.admin.supplies.loans.index') }}" class="text-green-600 hover:text-green-800">Préstamos de Insumos</a>
    </li>
@endsection

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Préstamos de Insumos</h2>
                <p class="text-gray-600 mt-1">Gestión de préstamos de insumos registrados por el administrador</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('infrastock.admin.supplies.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-2"></i>Volver a Insumos
                </a>
            </div>
        </div>

        <!-- Nota informativa -->
        <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-400 rounded">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                <div>
                    <p class="text-sm text-blue-700 font-semibold mb-1">
                        <strong>Nota:</strong> Estos préstamos no cuentan como consumo ya que estos regresan a bodega.
                    </p>
                    <p class="text-xs text-blue-600">
                        Los préstamos registrados aquí son realizados directamente por el administrador y se descuentan del stock disponible.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabla de Préstamos -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                @if($loans->count() > 0)
                    <div class="overflow-x-auto">
                        <table id="loansTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Insumo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Prestatario</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Cantidad</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Lugar de Préstamo</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Fecha de Registro</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Estado</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($loans as $loan)
                                    <tr class="hover:bg-gray-100 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-gray-900">{{ $loan->equipment->name ?? 'N/A' }}</div>
                                            @if($loan->equipment && $loan->equipment->category)
                                                <div class="text-xs text-gray-500">
                                                    <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full">
                                                        {{ $loan->equipment->category->name }}
                                                    </span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                // Extraer el nombre del prestatario de la descripción
                                                $description = $loan->description ?? '';
                                                $borrowerName = '';
                                                if (preg_match('/Prestatario:\s*(.+?)\s*\|/', $description, $matches)) {
                                                    $borrowerName = trim($matches[1]);
                                                } else {
                                                    // Si no hay prestatario en la descripción, mostrar el usuario asociado
                                                    if ($loan->user && $loan->user->person) {
                                                        $borrowerName = ($loan->user->person->first_name ?? '') . ' ' . ($loan->user->person->first_last_name ?? '');
                                                    } else {
                                                        $borrowerName = 'Usuario #' . $loan->user_id;
                                                    }
                                                }
                                            @endphp
                                            <div class="text-sm text-gray-900 font-semibold">
                                                {{ $borrowerName ?: 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $loan->amount }} {{ $loan->equipment->unit_measure ?? 'unidades' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @php
                                                // Extraer el lugar de préstamo de la descripción
                                                $description = $loan->description ?? '';
                                                $location = '';
                                                if (preg_match('/Lugar:\s*(.+?)\s*\|/', $description, $matches)) {
                                                    $location = trim($matches[1]);
                                                } else {
                                                    $location = $description ?: 'N/A';
                                                }
                                            @endphp
                                            <div class="max-w-xs truncate" title="{{ $location }}">
                                                {{ $location }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @php
                                            $loanDate = '';
                                           if (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $loanDate)) {
                                            $date = \Carbon\Carbon::createFromFormat('d/m/Y', substr($loanDate, 0, 10));
                                            } else {
                                                $date = \Carbon\Carbon::parse($loanDate);
                                            }
                                            @endphp
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $loanDate }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $loan->created_at->format('d/m/Y H:i') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @if($loan->is_returned ?? false)
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    <i class="fas fa-check-circle mr-1"></i>Devuelto
                                                </span>
                                                @if($loan->extracted_return_date)
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        {{ $loan->return_date }}
                                                    </div>
                                                @endif
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>Pendiente
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            @if(!($loan->is_returned ?? false))
                                                <form action="{{ route('infrastock.admin.supplies.loans.return', $loan->id) }}" method="POST" class="inline-block return-form" data-loan-id="{{ $loan->id }}" data-equipment-name="{{ addslashes($loan->equipment->name ?? 'N/A') }}" data-amount="{{ $loan->amount }}">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 p-2 rounded hover:bg-green-50 transition-colors" title="Registrar Devolución">
                                                        <i class="fas fa-undo-alt"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-400" title="Ya devuelto">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div class="mt-4">
                        {{ $loans->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-hand-holding text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay préstamos registrados</h3>
                        <p class="text-gray-500 mb-4">Aún no se han registrado préstamos de insumos.</p>
                        <a href="{{ route('infrastock.admin.supplies.index') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <i class="fas fa-plus mr-2"></i>Ir a Insumos para registrar un préstamo
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
// Manejar envío de formularios de devolución
document.addEventListener('DOMContentLoaded', function() {
    // Agregar event listener a todos los formularios de devolución
    document.querySelectorAll('.return-form').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const loanId = form.getAttribute('data-loan-id');
            const equipmentName = form.getAttribute('data-equipment-name');
            const amount = form.getAttribute('data-amount');
            
            Swal.fire({
                title: '¿Confirmar devolución?',
                html: `
                    <div class="text-left">
                        <p><strong>Insumo:</strong> ${equipmentName}</p>
                        <p><strong>Cantidad:</strong> ${amount}</p>
                        <p class="mt-2">Se registrará la fecha y hora actual de devolución y se actualizará el stock.</p>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, registrar devolución',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviar el formulario
                    form.submit();
                }
            });
        });
    });
    
    // Verificar si hay mensajes de sesión
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '{{ session('success') }}',
            showConfirmButton: false,
            timer: 3000
        });
    @endif
    
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}',
            confirmButtonText: 'Entendido'
        });
    @endif
    
    // Inicializar DataTables si hay préstamos
    @if($loans->count() > 0)
        var table = $('#loansTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json',
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                paginate: {
                    first: "Primero",
                    last: "Último",
                    next: "Siguiente",
                    previous: "Anterior"
                }
            },
            pageLength: 15,
            order: [[0, 'desc']], // Ordenar por ID descendente
            responsive: true
        });
    @endif
});
</script>
@endsection

