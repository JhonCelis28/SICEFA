<!--
    * @file index.blade.php
    * @brief Vista para listar todos los usuarios registrados en el sistema desde el panel administrativo.
    *
    * Esta vista presenta una tabla completa con todos los usuarios del sistema,
    * incluyendo filtros por rol, estado y búsqueda. Permite al administrador cambiar
    * el estado activo/inactivo de los usuarios y acceder a las opciones de edición.
    * Utiliza Tailwind CSS para un diseño responsive y moderno.
    * Extiende la plantilla `master.blade.php` del módulo INFRASTOCK.
    *
    * @param Collection $users Lista paginada de usuarios con sus relaciones.
    * @param Collection $roles Lista de roles disponibles para filtros.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Listado de Usuarios - INFRASTOCK')

@section('content')

<div class="container mx-auto px-4 py-6">
    <!-- Encabezado de la página -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-users text-green-600 mr-2"></i>
                Listado de Usuarios
            </h1>
            <p class="text-gray-600 mt-2">Gestiona todos los usuarios registrados en el sistema.</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-lg mb-6">
        <div class="bg-gray-50 px-6 py-4 rounded-t-xl">
            <h6 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-filter mr-2 text-green-600"></i>
                Filtros de Búsqueda
            </h6>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar Usuario</label>
                    <input type="text" 
                           id="search" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                           placeholder="Nombre, email o documento...">
                </div>
                
                <div>
                    <label for="role-filter" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Rol</label>
                    <select id="role-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por Estado</label>
                    <select id="status-filter" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        <option value="">Todos los estados</option>
                        <option value="active">Activos</option>
                        <option value="inactive">Inactivos</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
                    <button type="button" id="clear-filters" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i>
                        Limpiar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="bg-white rounded-xl shadow-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h6 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-table mr-2 text-green-600"></i>
                    Usuarios Registrados
                </h6>
                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">Total: {{ $users->total() }} usuarios</span>
            </div>
        </div>
        <div class="p-6">
            @if($users->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="users-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Documento</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Registro</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="user-row hover:bg-gray-50 transition-colors duration-200" 
                                    data-name="{{ strtolower($user->nickname ?? '') }}"
                                    data-email="{{ strtolower($user->email) }}"
                                    data-document="{{ $user->person->document_number ?? '' }}"
                                    data-role="{{ $user->roles->first()->name ?? '' }}"
                                    data-status="{{ $user->trashed() ? 'inactive' : 'active' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                                    <i class="fas fa-user text-green-600"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->nickname ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->person->telephone1 ?? 'Sin teléfono' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
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
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $user->person->document_number ?? 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 max-w-xs truncate">{{ $user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($user->roles->count() > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $user->roles->first()->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Sin rol
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            @if($user->trashed())
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Inactivo
                                                </span>
                                                <button class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs transition-colors duration-200 toggle-status" 
                                                        data-id="{{ $user->id }}" 
                                                        data-action="activate"
                                                        title="Activar usuario">
                                                    Activar
                                                </button>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Activo
                                                </span>
                                                <button class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs transition-colors duration-200 toggle-status" 
                                                        data-id="{{ $user->id }}" 
                                                        data-action="deactivate"
                                                        title="Desactivar usuario">
                                                    Inactivo
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>
                                            <div>{{ $user->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs">{{ $user->created_at->format('H:i') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('infrastock.admin.users.edit', $user->id) }}" 
                                               class="text-yellow-600 hover:text-yellow-900 transition-colors duration-200" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="text-red-600 hover:text-red-900 transition-colors duration-200 delete-user" 
                                                    data-id="{{ $user->id }}" 
                                                    data-name="{{ $user->nickname }}"
                                                    title="Eliminar permanentemente">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <div class="flex justify-center mt-6">
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay usuarios registrados</h3>
                    <p class="text-gray-500 mb-6">Comience registrando el primer usuario del sistema.</p>
                    <a href="{{ route('infrastock.admin.users.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                        <i class="fas fa-user-plus mr-2"></i>
                        Registrar Primer Usuario
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script cargado correctamente');
    
    // Event listeners para botones (fuera del setTimeout)
    setupButtonListeners();
    
    function setupButtonListeners() {
        console.log('Configurando event listeners para botones');
        
        // Cambiar estado de usuario
        document.addEventListener('click', function(e) {
            if (e.target.closest('.toggle-status')) {
                e.preventDefault();
                const button = e.target.closest('.toggle-status');
                const userId = button.dataset.id;
                const action = button.dataset.action;
                
                console.log('Botón de cambiar estado clickeado:', { userId, action });
                
                if (!userId || !action) {
                    alert('Error: No se encontraron los datos del usuario');
                    return;
                }
                
                // Verificar si el usuario tiene solicitudes pendientes (solo para desactivar)
                if (action === 'deactivate') {
                    fetch(`{{ route('infrastock.admin.users.check-requests', '') }}/${userId}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.has_pending_requests) {
                            alert(`No se puede desactivar al usuario porque tiene ${data.pending_count} solicitud(es) pendiente(s).\n\nPor favor, procese las solicitudes antes de desactivar el usuario.`);
                            return;
                        }
                        
                        // Proceder con el cambio de estado
                        proceedWithStatusChange(button, userId, action);
                    })
                    .catch(error => {
                        console.error('Error al verificar solicitudes:', error);
                        alert('Error al verificar las solicitudes del usuario');
                    });
                } else {
                    // Para activar, no necesitamos verificar solicitudes
                    proceedWithStatusChange(button, userId, action);
                }
            }
        });
        
        function proceedWithStatusChange(button, userId, action) {
            const actionText = action === 'activate' ? 'activar' : 'desactivar';
            
            if (confirm(`¿Está seguro de que desea ${actionText} este usuario?`)) {
                console.log('Usuario confirmó el cambio de estado');
                
                // Deshabilitar botón para evitar múltiples clics
                button.disabled = true;
                button.innerHTML = 'Procesando...';
                
                fetch(`{{ route('infrastock.admin.users.toggle-status', '') }}/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => {
                    console.log('Respuesta recibida:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);
                    if (data.success) {
                        alert(data.message || `Usuario ${actionText}do exitosamente`);
                        location.reload();
                    } else {
                        throw new Error(data.message || 'Error al cambiar el estado del usuario');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cambiar el estado del usuario: ' + error.message);
                    // Restaurar botón
                    button.disabled = false;
                    button.innerHTML = action === 'activate' ? 'Activar' : 'Inactivo';
                });
            } else {
                console.log('Usuario canceló el cambio de estado');
            }
        }

        // Eliminar usuario
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-user')) {
                e.preventDefault();
                const button = e.target.closest('.delete-user');
                const userId = button.dataset.id;
                const userName = button.dataset.name;
                
                console.log('Botón de eliminar clickeado:', { userId, userName });
                
                if (!userId || !userName) {
                    alert('Error: No se encontraron los datos del usuario');
                    return;
                }
                
                // Verificar si el usuario tiene solicitudes pendientes
                fetch(`{{ route('infrastock.admin.users.check-requests', '') }}/${userId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.has_pending_requests) {
                        alert(`No se puede eliminar al usuario "${userName}" porque tiene ${data.pending_count} solicitud(es) pendiente(s).\n\nPor favor, procese las solicitudes antes de eliminar el usuario.`);
                        return;
                    }
                    
                    // Mostrar alerta de confirmación
                    const confirmMessage = `¿Está seguro que desea eliminar permanentemente al usuario "${userName}"?\n\nEsta acción no se puede deshacer.`;
                    
                    if (confirm(confirmMessage)) {
                        console.log('Usuario confirmó la eliminación');
                        
                        // Deshabilitar botón para evitar múltiples clics
                        button.disabled = true;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        
                        // Realizar la eliminación
                        fetch(`{{ route('infrastock.admin.users.destroy', '') }}/${userId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                '_method': 'DELETE'
                            })
                        })
                        .then(response => {
                            console.log('Respuesta recibida:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Datos recibidos:', data);
                            if (data.success) {
                                alert('Usuario eliminado exitosamente');
                                location.reload();
                            } else {
                                throw new Error(data.message || 'Error al eliminar el usuario');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error al eliminar el usuario: ' + error.message);
                            // Restaurar botón
                            button.disabled = false;
                            button.innerHTML = '<i class="fas fa-trash"></i>';
                        });
                    } else {
                        console.log('Usuario canceló la eliminación');
                    }
                })
                .catch(error => {
                    console.error('Error al verificar solicitudes:', error);
                    alert('Error al verificar las solicitudes del usuario');
                });
            }
        });
        
        console.log('Event listeners configurados correctamente');
    }
    
    // Esperar un poco para asegurar que el DOM esté completamente cargado
    setTimeout(function() {
        const searchInput = document.getElementById('search');
        const roleFilter = document.getElementById('role-filter');
        const statusFilter = document.getElementById('status-filter');
        const clearFiltersBtn = document.getElementById('clear-filters');
        const tableRows = document.querySelectorAll('.user-row');

        console.log('Elementos encontrados:', {
            searchInput: !!searchInput,
            roleFilter: !!roleFilter,
            statusFilter: !!statusFilter,
            clearFiltersBtn: !!clearFiltersBtn,
            tableRows: tableRows.length
        });

        if (!searchInput || !roleFilter || !statusFilter || !clearFiltersBtn) {
            console.error('No se encontraron todos los elementos necesarios');
            return;
        }

        if (tableRows.length === 0) {
            console.error('No se encontraron filas de usuarios');
            return;
        }

        // Función para filtrar la tabla
        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const roleValue = roleFilter.value.toLowerCase().trim();
            const statusValue = statusFilter.value;
            
            console.log('Filtrando tabla:', { searchTerm, roleValue, statusValue });
            
            let visibleRows = 0;
            
            tableRows.forEach((row, index) => {
                const name = (row.dataset.name || '').toLowerCase();
                const email = (row.dataset.email || '').toLowerCase();
                const document = (row.dataset.document || '').toString();
                const role = (row.dataset.role || '').toLowerCase();
                const status = row.dataset.status || '';
                
                let showRow = true;
                
                // Filtro por búsqueda
                if (searchTerm) {
                    const matchesSearch = name.includes(searchTerm) || 
                                       email.includes(searchTerm) || 
                                       document.includes(searchTerm);
                    if (!matchesSearch) {
                        showRow = false;
                    }
                }
                
                // Filtro por rol
                if (roleValue && role !== roleValue) {
                    showRow = false;
                }
                
                // Filtro por estado
                if (statusValue && status !== statusValue) {
                    showRow = false;
                }
                
                row.style.display = showRow ? '' : 'none';
                if (showRow) visibleRows++;
                
                console.log(`Fila ${index}:`, { name, email, document, role, status, showRow });
            });
            
            console.log(`Filas visibles: ${visibleRows} de ${tableRows.length}`);
        }

        // Event listeners para los filtros
        searchInput.addEventListener('input', function() {
            console.log('Evento input en búsqueda');
            filterTable();
        });
        
        roleFilter.addEventListener('change', function() {
            console.log('Evento change en rol');
            filterTable();
        });
        
        statusFilter.addEventListener('change', function() {
            console.log('Evento change en estado');
            filterTable();
        });
        
        // Limpiar filtros
        clearFiltersBtn.addEventListener('click', function() {
            console.log('Limpiando filtros');
            searchInput.value = '';
            roleFilter.value = '';
            statusFilter.value = '';
            filterTable();
        });
        
        console.log('Event listeners configurados correctamente');
    }, 100);

});


// Función para mostrar notificaciones
function showNotification(type, message) {
    // Crear elemento de notificación
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Agregar al DOM
    document.body.appendChild(notification);
    
    // Remover automáticamente después de 5 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script>
@endsection