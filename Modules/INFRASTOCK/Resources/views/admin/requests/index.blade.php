@extends('infrastock::layouts.master')

@section('title', 'Gestión de Solicitudes')

@section('content')
<div class="container-fluid">
    <!-- Header de la página -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <p class="text-muted">Administra las solicitudes de todos los usuarios</p>
        </div>
        <div class="d-flex align-items-center">
            <span class="badge badge-warning mr-2">
                <i class="fas fa-clock mr-1"></i>
                {{ $requests->where('status', 'pending')->count() }} Pendientes
            </span>
            <a href="{{ route('infrastock.test.notification') }}" class="btn btn-info btn-sm ml-2">
                <i class="fas fa-bell mr-1"></i> Probar Notificación
            </a>
        </div>
    </div>

    <!-- Tabla de solicitudes -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-clipboard-list mr-2"></i>
                Lista de Solicitudes
            </h6>
        </div>
        <div class="card-body">
            @if($requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th class="font-bold">Solicitante</th>
                                <th class="font-bold">Insumos</th>
                                <th class="font-bold">Cantidad Total</th>
                                <th class="font-bold">Unidad Productiva</th>
                                <th class="font-bold">Almacén</th>
                                <th class="font-bold">Fecha Solicitud</th>
                                <th class="font-bold">Estado</th>
                                <th class="font-bold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $request)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ asset('AdminLTE/dist/img/user2-160x160.jpg') }}" 
                                                 class="rounded-circle mr-2" width="30" height="30" alt="User">
                                            <div>
                                                @php
                                                    $roleName = 'Usuario';
                                                    if ($request->user && $request->user->relationLoaded('roles')) {
                                                        $userRoles = $request->user->roles->pluck('name')->toArray();
                                                    } else {
                                                        // Si los roles no están cargados, cargarlos manualmente
                                                        $user = $request->user;
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
                                                            // Si tiene roles pero no es uno de los esperados, mostrar el primero
                                                            $roleName = $userRoles[0] ?? 'Usuario';
                                                        }
                                                    }
                                                @endphp
                                                <div class="font-weight-bold">{{ $request->user->name ?? 'Usuario' }}</div>
                                                <small class="text-muted">{{ $request->user->email ?? 'N/A' }}</small>
                                                <div>
                                                    <span class="badge badge-info badge-sm">{{ $roleName }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @foreach($request->items->take(3) as $item)
                                                <div class="font-weight-bold">{{ $item->equipment->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $item->equipment->category->name ?? 'Sin categoría' }}</small>
                                                @if(!$loop->last)<br>@endif
                                            @endforeach
                                            @if($request->items->count() > 3)
                                                <small class="text-info">y {{ $request->items->count() - 3 }} más...</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $request->items->sum('requested_amount') }} unidades
                                        </span>
                                    </td>
                                    <td>{{ $request->productiveUnitWarehouse->productiveUnit->name ?? 'N/A' }}</td>
                                    <td>{{ $request->productiveUnitWarehouse->warehouse->name ?? 'N/A' }}</td>
                                    <td>
                                        <div>
                                            <div>{{ $request->created_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @switch($request->status)
                                            @case('pending')
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    Pendiente
                                                </span>
                                                @break
                                            @case('approved')
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check mr-1"></i>
                                                    Aprobada
                                                </span>
                                                @break
                                            @case('rejected')
                                                <span class="badge badge-danger">
                                                    <i class="fas fa-times mr-1"></i>
                                                    Rechazada
                                                </span>
                                                @break
                                            @default
                                                <span class="badge badge-secondary">{{ $request->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>
                                        @if($request->status === 'pending')
                                            <div class="btn-group" role="group">
                                                <form method="POST" action="{{ route('infrastock.admin.requests.approve', $request->id) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" 
                                                            onclick="return confirm('¿Estás seguro de aprobar esta solicitud?')"
                                                            title="Aprobar solicitud">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="showRejectModal({{ $request->id }})"
                                                        title="Rechazar solicitud">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">
                                                <i class="fas fa-info-circle" title="Solicitud ya procesada"></i>
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div class="d-flex justify-content-center">
                    {{ $requests->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list text-gray-400" style="font-size: 4rem;"></i>
                    <h4 class="text-gray-600 mt-3">No hay solicitudes</h4>
                    <p class="text-gray-500">No se han encontrado solicitudes de insumos.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para rechazar solicitud -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rejectModalLabel">Rechazar Solicitud</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="rejection_reason">Motivo del rechazo:</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" 
                                  placeholder="Por favor, explica el motivo del rechazo..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar Solicitud</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts adicionales -->
<script>
    // Auto-refresh cada 30 segundos para nuevas solicitudes
    setInterval(function() {
        if (document.visibilityState === 'visible') {
            location.reload();
        }
    }, 30000);

    // Función para mostrar el modal de rechazo
    function showRejectModal(requestId) {
        const form = document.getElementById('rejectForm');
        form.action = `/infrastock/admin/requests/${requestId}/reject`;
        $('#rejectModal').modal('show');
    }
</script>
@endsection
