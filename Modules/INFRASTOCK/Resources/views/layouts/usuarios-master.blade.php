<!--
    * @file usuarios-master.blade.php
    * @brief Plantilla de diseño principal para usuarios que solicitan insumos en INFRASTOCK.
    *
    * Extiende el layout base compartido y define el menú común para todos los roles
    * que solicitan insumos (PSICOLA, Operario, Personal de Aseo, Vigilancia, Ganadería,
    * Convivencia, Ciencias Básicas, Agroindustria).
-->
@extends('infrastock::layouts.base-master')

@php
    // Obtener el rol del usuario actual
    $userRoles = Auth::user()->roles->pluck('name')->toArray();
    $roleName = '';
    $routePrefix = '';
    
    // Roles específicos de usuarios (no administradores) - orden de prioridad
    $specificRoles = [
        'Aseo' => ['roleName' => 'Personal de Aseo', 'routePrefix' => 'cleaning-staff'],
        'Personal de Aseo' => ['roleName' => 'Personal de Aseo', 'routePrefix' => 'cleaning-staff'],
        'Psicola' => ['roleName' => 'PSICOLA', 'routePrefix' => 'psicola'],
        'Ciencias Basicas' => ['roleName' => 'Ciencias Básicas', 'routePrefix' => 'ciencias-basicas'],
        'Operario' => ['roleName' => 'Operario', 'routePrefix' => 'operator'],
        'Centro de Convivencia' => ['roleName' => 'Centro de Convivencia', 'routePrefix' => 'convivencia'],
        'Ganadería' => ['roleName' => 'Ganadería', 'routePrefix' => 'ganaderia'],
        'Vigilancia' => ['roleName' => 'Vigilancia', 'routePrefix' => 'vigilancia'],
        'Agroindustria' => ['roleName' => 'Agroindustria', 'routePrefix' => 'agroindustria'],
    ];
    
    // Buscar el primer rol específico que coincida (prioridad sobre Administrador)
    foreach ($specificRoles as $roleKey => $roleData) {
        if (in_array($roleKey, $userRoles)) {
            $roleName = $roleData['roleName'];
            $routePrefix = $roleData['routePrefix'];
            break;
        }
    }
    
    // Si no se encontró un rol específico, usar fallback
    if (empty($roleName)) {
        // Excluir roles administrativos del fallback
        $nonAdminRoles = array_filter($userRoles, function($role) {
            return !in_array($role, ['Administrador', 'Admin']);
        });
        
        if (!empty($nonAdminRoles)) {
            $roleName = reset($nonAdminRoles);
            $routePrefix = strtolower(str_replace(' ', '-', $roleName));
        } else {
            $roleName = 'Usuario';
            $routePrefix = 'usuario';
        }
    }
@endphp

@section('title', 'INFRASTOCK - ' . $roleName)

@section('user-role', $roleName)

@section('footer-role', ' - ' . $roleName)

@section('sidebar-menu')
    <!-- Dashboard Principal -->
    <li>
        <a href="{{ route('infrastock.' . $routePrefix . '.dashboard') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.' . $routePrefix . '.dashboard')) active @endif">
            <i class="fas fa-tachometer-alt w-6 text-xl text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
            <span x-show="isSidebarExpanded || !isDesktop" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-300" 
                  x-transition:enter-start="opacity-0 transform scale-x-0" 
                  x-transition:enter-end="opacity-100 transform scale-x-100" 
                  x-transition:leave="transition ease-in duration-200" 
                  x-transition:leave-start="opacity-100 transform scale-x-100" 
                  x-transition:leave-end="opacity-0 transform scale-x-0" 
                  class="origin-left whitespace-nowrap">Dashboard</span>
        </a>
    </li>

    <!-- Stock Disponible (solo si el rol tiene esta ruta) -->
    @php
        $hasStockRoute = in_array($routePrefix, ['operator', 'psicola', 'vigilancia', 'ganaderia', 'convivencia', 'ciencias-basicas', 'agroindustria']);
    @endphp
    @if($hasStockRoute)
    <li>
        <a href="{{ route('infrastock.' . $routePrefix . '.stock') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.' . $routePrefix . '.stock')) active @endif">
            <i class="fas fa-boxes w-6 text-xl text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
            <span x-show="isSidebarExpanded || !isDesktop" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-300" 
                  x-transition:enter-start="opacity-0 transform scale-x-0" 
                  x-transition:enter-end="opacity-100 transform scale-x-100" 
                  x-transition:leave="transition ease-in duration-200" 
                  x-transition:leave-start="opacity-100 transform scale-x-100" 
                  x-transition:leave-end="opacity-0 transform scale-x-0" 
                  class="origin-left whitespace-nowrap">Stock Disponible</span>
        </a>
    </li>
    @endif

    <!-- Mis Solicitudes -->
    <li>
        <a href="{{ route('infrastock.' . $routePrefix . '.requests.index') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.' . $routePrefix . '.requests.*')) active @endif">
            <i class="fas fa-clipboard-list w-6 text-xl text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
            <span x-show="isSidebarExpanded || !isDesktop" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-300" 
                  x-transition:enter-start="opacity-0 transform scale-x-0" 
                  x-transition:enter-end="opacity-100 transform scale-x-100" 
                  x-transition:leave="transition ease-in duration-200" 
                  x-transition:leave-start="opacity-100 transform scale-x-100" 
                  x-transition:leave-end="opacity-0 transform scale-x-0" 
                  class="origin-left whitespace-nowrap">Mis Solicitudes</span>
        </a>
    </li>

    <!-- Reporte de Sobrantes -->
    @php
        // cleaning-staff usa surplus.index, los demás usan surplus-report
        $surplusRoute = ($routePrefix === 'cleaning-staff') 
            ? 'infrastock.' . $routePrefix . '.surplus.index' 
            : 'infrastock.' . $routePrefix . '.surplus-report';
    @endphp
    <li>
        <a href="{{ route($surplusRoute) }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.' . $routePrefix . '.surplus-report') || Request::routeIs('infrastock.' . $routePrefix . '.surplus.*')) active @endif">
            <i class="fas fa-file-alt w-6 text-xl text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
            <span x-show="isSidebarExpanded || !isDesktop" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-300" 
                  x-transition:enter-start="opacity-0 transform scale-x-0" 
                  x-transition:enter-end="opacity-100 transform scale-x-100" 
                  x-transition:leave="transition ease-in duration-200" 
                  x-transition:leave-start="opacity-100 transform scale-x-100" 
                  x-transition:leave-end="opacity-0 transform scale-x-0" 
                  class="origin-left whitespace-nowrap">Reporte de Sobrantes</span>
        </a>
    </li>
@endsection

@section('navbar-notifications')
    <!-- Icono de notificaciones con contador y dropdown -->
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="relative text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 p-2.5 rounded-lg hover:bg-gray-100 transition-all duration-200 group">
            <i class="fas fa-bell text-xl group-hover:text-green-600 transition-colors duration-200"></i>
            @if(isset($notificationCount) && $notificationCount > 0)
                <span class="absolute top-0 right-0 -mt-1 -mr-1 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $notificationCount }}</span>
            @endif
        </button>
        <!-- Dropdown de notificaciones -->
        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-40 border border-gray-100 max-h-96 overflow-y-auto">
            <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-100 font-semibold">
                <i class="fas fa-bell mr-2"></i>Notificaciones
            </div>
            @if(isset($notifications) && $notifications->count() > 0)
                @foreach($notifications->take(5) as $notification)
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50' }}" 
                         onclick="markNotificationAsRead('{{ $notification->id }}')">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                @switch($notification->type)
                                    @case('request_approved')
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-check text-green-600 text-sm"></i>
                                        </div>
                                        @break
                                    @case('request_rejected')
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-times text-red-600 text-sm"></i>
                                        </div>
                                        @break
                                    @default
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-bell text-blue-600 text-sm"></i>
                                        </div>
                                @endswitch
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notificación' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notification->data['message'] ?? 'Sin mensaje' }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            @if(!$notification->read_at)
                                <div class="flex-shrink-0">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
                @if($notifications->count() > 5)
                    <div class="px-4 py-2 text-center">
                        <a href="{{ route('infrastock.' . $routePrefix . '.notifications') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">
                            Ver todas las notificaciones ({{ $notifications->count() }})
                        </a>
                    </div>
                @endif
            @else
                <div class="px-4 py-4 text-center text-gray-500">
                    <i class="fas fa-bell-slash text-2xl mb-2"></i>
                    <p>No hay notificaciones nuevas</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('navbar-user-menu')
    <a href="{{ route('infrastock.' . $routePrefix . '.profile') }}" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-user-circle mr-2 text-blue-500"></i> Editar Perfil</a>
    <a href="{{ route('infrastock.' . $routePrefix . '.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-sign-out-alt mr-2 text-red-500"></i> Cerrar Sesión</a>
    <form id="logout-form" action="{{ route('infrastock.' . $routePrefix . '.logout') }}" method="POST" class="hidden">
        @csrf
    </form>
@endsection

@section('breadcrumbs')
    {{-- Muestra el enlace "Dashboard" en las migas de pan solo si no estamos ya en el dashboard --}}
    @if (!Request::routeIs('infrastock.' . $routePrefix . '.dashboard'))
    <li class="flex items-center">
        <a href="{{ route('infrastock.' . $routePrefix . '.dashboard') }}" class="text-green-600 hover:text-green-800">Dashboard</a>
        <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
    </li>
    @endif
    @hasSection('breadcrumb-items')
        {{-- Aquí se insertan ítems adicionales para las migas de pan definidos en las vistas hijas --}}
        @yield('breadcrumb-items')
    @endif
@endsection

@section('script')
    <script>
        // Función para marcar notificación como leída
        function markNotificationAsRead(notificationId) {
            fetch(`/infrastock/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Marcar como leída visualmente
                    const notificationElement = document.querySelector(`[onclick*="${notificationId}"]`);
                    if (notificationElement) {
                        const notificationDiv = notificationElement.closest('.px-4.py-3');
                        if (notificationDiv) {
                            notificationDiv.classList.remove('bg-blue-50');
                            notificationDiv.classList.add('opacity-75');
                            // Remover el indicador de no leída
                            const unreadIndicator = notificationDiv.querySelector('.w-2.h-2.bg-blue-500');
                            if (unreadIndicator) {
                                unreadIndicator.remove();
                            }
                        }
                    }
                    // Actualizar contador
                    updateNotificationCount();
                    
                    // Si hay una URL de redirección, redirigir
                    if (data.redirect_url) {
                        setTimeout(function() {
                            window.location.href = data.redirect_url;
                        }, 300);
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        // Función para actualizar el contador de notificaciones
        function updateNotificationCount() {
            const unreadNotifications = document.querySelectorAll('.px-4.py-3:not(.opacity-75)');
            const unreadCount = Array.from(unreadNotifications).filter(el => {
                return el.querySelector('.w-2.h-2.bg-blue-500') !== null;
            }).length;
            
            const badge = document.querySelector('.bg-red-500');
            if (badge) {
                if (unreadCount > 0) {
                    badge.textContent = unreadCount;
                    badge.style.display = 'block';
                } else {
                    badge.style.display = 'none';
                }
            }
        }
    </script>
    @yield('additional-scripts')
@endsection
