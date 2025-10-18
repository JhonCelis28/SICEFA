<!--
    * @file show.blade.php
    * @brief Vista para mostrar los detalles de un usuario específico desde el panel administrativo.
    *
    * Esta vista presenta toda la información detallada de un usuario específico,
    * incluyendo datos personales, información de contacto, rol asignado y estadísticas
    * de actividad. Utiliza Tailwind CSS para un diseño responsive y moderno.
    * Extiende la plantilla `master.blade.php` del módulo INFRASTOCK.
    *
    * @param User $user Usuario específico con sus relaciones cargadas.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Detalles del Usuario - INFRASTOCK')

@section('content')

<div class="container mx-auto px-4 py-6">
    <!-- Encabezado de la página -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-user text-green-600 mr-2"></i>
                Detalles del Usuario
            </h1>
            <p class="text-gray-600 mt-2">Información completa del usuario seleccionado.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('infrastock.admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al Listado
            </a>
            <a href="{{ route('infrastock.admin.users.edit', $user->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                <i class="fas fa-edit mr-2"></i>
                Editar Usuario
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información Personal -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="bg-blue-600 text-white px-6 py-4 rounded-t-xl">
                    <h6 class="text-lg font-semibold">
                        <i class="fas fa-user mr-2"></i>
                        Información Personal
                    </h6>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre Completo</label>
                            <p class="text-gray-900 font-medium">
                                {{ $user->person->first_name ?? 'N/A' }} 
                                {{ $user->person->first_last_name ?? 'N/A' }} 
                                {{ $user->person->second_last_name ?? '' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apodo/Nickname</label>
                            <p class="text-gray-900 font-medium">{{ $user->nickname ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Documento</label>
                            <p class="text-gray-900">
                                @switch($user->person->document_type ?? '')
                                    @case('1')
                                        Cédula de Ciudadanía
                                        @break
                                    @case('2')
                                        Tarjeta de Identidad
                                        @break
                                    @case('3')
                                        Cédula de Extranjería
                                        @break
                                    @case('4')
                                        Pasaporte
                                        @break
                                    @default
                                        {{ $user->person->document_type ?? 'N/A' }}
                                @endswitch
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Documento</label>
                            <p class="text-gray-900">{{ $user->person->document_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                            <p class="text-gray-900">{{ $user->person->phone ?? 'Sin teléfono' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <p class="text-gray-900">{{ $user->person->address ?? 'Sin dirección' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Cuenta -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="bg-green-600 text-white px-6 py-4 rounded-t-xl">
                    <h6 class="text-lg font-semibold">
                        <i class="fas fa-envelope mr-2"></i>
                        Información de Cuenta
                    </h6>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo Electrónico</label>
                            <p class="text-gray-900 font-medium">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado de la Cuenta</label>
                            <p class="text-gray-900">
                                @if($user->trashed())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i>
                                        Inactivo
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>
                                        Activo
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                            <p class="text-gray-900">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Última Actualización</label>
                            <p class="text-gray-900">{{ $user->updated_at->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="space-y-6">
            <!-- Rol y Permisos -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="bg-blue-600 text-white px-6 py-4 rounded-t-xl">
                    <h6 class="text-lg font-semibold">
                        <i class="fas fa-user-tag mr-2"></i>
                        Rol y Permisos
                    </h6>
                </div>
                <div class="p-6">
                    @if($user->roles->count() > 0)
                        @foreach($user->roles as $role)
                            <div class="flex items-center mb-3">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-user-tag text-blue-600"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $role->description ?? 'Sin descripción' }}</div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-500">
                            <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                            <p class="text-sm">Sin roles asignados</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Estadísticas de Actividad -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="bg-yellow-600 text-white px-6 py-4 rounded-t-xl">
                    <h6 class="text-lg font-semibold">
                        <i class="fas fa-chart-bar mr-2"></i>
                        Estadísticas de Actividad
                    </h6>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="border-r border-gray-200 pr-4">
                                <h4 class="text-2xl font-bold text-blue-600">{{ $user->warehouseMovements()->count() }}</h4>
                                <p class="text-xs text-gray-500">Solicitudes Totales</p>
                            </div>
                        </div>
                        <div class="text-center">
                            <h4 class="text-2xl font-bold text-green-600">{{ $user->warehouseMovements()->where('role', 'approved')->count() }}</h4>
                            <p class="text-xs text-gray-500">Solicitudes Aprobadas</p>
                        </div>
                    </div>
                    <div class="border-t border-gray-200 mt-4 pt-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="border-r border-gray-200 pr-4">
                                    <h4 class="text-2xl font-bold text-yellow-600">{{ $user->warehouseMovements()->where('role', 'Solicitud')->count() }}</h4>
                                    <p class="text-xs text-gray-500">Solicitudes Pendientes</p>
                                </div>
                            </div>
                            <div class="text-center">
                                <h4 class="text-2xl font-bold text-red-600">{{ $user->warehouseMovements()->where('role', 'rejected')->count() }}</h4>
                                <p class="text-xs text-gray-500">Solicitudes Rechazadas</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-xl shadow-lg">
                <div class="bg-gray-600 text-white px-6 py-4 rounded-t-xl">
                    <h6 class="text-lg font-semibold">
                        <i class="fas fa-bolt mr-2"></i>
                        Acciones Rápidas
                    </h6>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <a href="{{ route('infrastock.admin.users.edit', $user->id) }}" class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition-colors duration-200 flex items-center justify-center">
                            <i class="fas fa-edit mr-2"></i>
                            Editar Usuario
                        </a>
                        
                        @if($user->trashed())
                            <button type="button" class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors duration-200 toggle-status" data-id="{{ $user->id }}" data-action="activate">
                                <i class="fas fa-check mr-2"></i>
                                Activar Usuario
                            </button>
                        @else
                            <button type="button" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors duration-200 toggle-status" data-id="{{ $user->id }}" data-action="deactivate">
                                <i class="fas fa-times mr-2"></i>
                                Desactivar Usuario
                            </button>
                        @endif
                        
                        <button type="button" class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors duration-200 delete-user" data-id="{{ $user->id }}" data-name="{{ $user->nickname }}">
                            <i class="fas fa-trash mr-2"></i>
                            Eliminar Permanentemente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar Usuario -->
<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="deleteModal">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div class="mt-2 px-7 py-3">
                <h3 class="text-lg font-medium text-gray-900 text-center">Confirmar Eliminación</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 text-center">
                        ¿Está seguro de que desea eliminar permanentemente al usuario <strong id="user-name"></strong>?
                    </p>
                    <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mr-2 mt-0.5"></i>
                            <p class="text-yellow-700 text-xs">
                                <strong>Advertencia:</strong> Esta acción no se puede deshacer. Se eliminarán todos los datos relacionados con este usuario.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-center space-x-4 px-4 py-3">
                <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors duration-200" onclick="closeDeleteModal()">
                    <i class="fas fa-times mr-2"></i>
                    Cancelar
                </button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                        <i class="fas fa-trash mr-2"></i>
                        Eliminar Permanentemente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cambiar estado de usuario
    document.querySelectorAll('.toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            const action = this.dataset.action;
            
            if (confirm(`¿Está seguro de que desea ${action === 'activate' ? 'activar' : 'desactivar'} este usuario?`)) {
                fetch(`{{ url('infrastock/admin/users') }}/${userId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cambiar el estado del usuario');
                });
            }
        });
    });

    // Eliminar usuario
    document.querySelectorAll('.delete-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.id;
            const userName = this.dataset.name;
            
            document.getElementById('user-name').textContent = userName;
            document.getElementById('delete-form').action = `{{ url('infrastock/admin/users') }}/${userId}`;
            document.getElementById('deleteModal').classList.remove('hidden');
        });
    });
});

// Función para cerrar el modal
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
</script>
@endsection