<!--
    * @file master.blade.php
    * @brief Plantilla de diseño principal para el panel administrativo del módulo INFRASTOCK.
    *
    * Extiende el layout base compartido y define el menú específico del administrador.
    *
    * @param int $pendingSupplyRequestsCount Número de solicitudes de insumos pendientes.
    * @param int $expiringSuppliesCount Número de insumos próximos a vencer.
-->
@extends('infrastock::layouts.base-master')

@section('title', 'INFRASTOCK - Panel Administrativo')

@section('user-role', 'Administrador')

@section('footer-role', '')

@section('sidebar-menu')
    <li>
        <a href="{{ route('cefa.infrastock.admin.dashboard') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('cefa.infrastock.admin.dashboard')) active @endif">
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

    <!-- Áreas Productivas - Enlace directo -->
    <li>
        <a href="{{ route('infrastock.admin.areas.index') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.admin.areas.*')) active @endif">
            <i class="fas fa-sitemap w-6 text-xl text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
            <span x-show="isSidebarExpanded || !isDesktop" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-300" 
                  x-transition:enter-start="opacity-0 transform scale-x-0" 
                  x-transition:enter-end="opacity-100 transform scale-x-100" 
                  x-transition:leave="transition ease-in duration-200" 
                  x-transition:leave-start="opacity-100 transform scale-x-100" 
                  x-transition:leave-end="opacity-0 transform scale-x-0" 
                  class="origin-left whitespace-nowrap">Áreas Productivas</span>
        </a>
    </li>

    <!-- Categorías - Enlace directo -->
    <li>
        <a href="{{ route('infrastock.admin.categories.index') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.admin.categories.*')) active @endif">
            <i class="fas fa-layer-group w-6 text-xl text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
            <span x-show="isSidebarExpanded || !isDesktop" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-300" 
                  x-transition:enter-start="opacity-0 transform scale-x-0" 
                  x-transition:enter-end="opacity-100 transform scale-x-100" 
                  x-transition:leave="transition ease-in duration-200" 
                  x-transition:leave-start="opacity-100 transform scale-x-100" 
                  x-transition:leave-end="opacity-0 transform scale-x-0" 
                  class="origin-left whitespace-nowrap">Categorías</span>
        </a>
    </li>
    
    <!-- Grupo de navegación para Insumos -->
    <li x-data="{ open: false }">
        <a @click="open = !open" class="sidebar-menu-item font-bold cursor-pointer @if(Request::routeIs(['infrastock.admin.supplies.*', 'infrastock.admin.supply-requests.*', 'infrastock.admin.supply-returns.*'])) active @endif">
            <i class="fas fa-boxes w-6 text-xl text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
            <span x-show="isSidebarExpanded || !isDesktop" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-300" 
                  x-transition:enter-start="opacity-0 transform scale-x-0" 
                  x-transition:enter-end="opacity-100 transform scale-x-100" 
                  x-transition:leave="transition ease-in duration-200" 
                  x-transition:leave-start="opacity-100 transform scale-x-100" 
                  x-transition:leave-end="opacity-0 transform scale-x-0" 
                  class="origin-left whitespace-nowrap flex-1">Insumos</span>
            <i x-show="isSidebarExpanded || !isDesktop" 
               x-cloak
               :class="{ 'fa-angle-down': open, 'fa-angle-left': !open }" 
               class="fas ml-auto transition-transform duration-200 text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 drop-shadow-sm"></i>
        </a>
        <ul x-show="open && (isSidebarExpanded || !isDesktop)" 
            x-cloak 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-2"
            class="ml-4 mt-3 mb-2 space-y-2">
            <li><a href="{{ route('infrastock.admin.supplies.index') }}" class="sidebar-submenu-item @if(Request::routeIs(['infrastock.admin.supplies.index', 'infrastock.admin.supplies.create', 'infrastock.admin.supplies.edit'])) active @endif"><i></i> Gestionar Insumos</a></li>
            <li>
                <a href="{{ route('infrastock.admin.supply-requests.index') }}" class="sidebar-submenu-item @if(Request::routeIs('infrastock.admin.supply-requests.index')) active @endif">
                    <i></i> Solicitudes
                    @if($pendingSupplyRequestsCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">{{ $pendingSupplyRequestsCount }}</span>
                    @endif
                </a>
            </li>
            <li>
                <a href="{{ route('infrastock.admin.supplies.loans.index') }}" class="sidebar-submenu-item @if(Request::routeIs('infrastock.admin.supplies.loans.*')) active @endif">
                    <i></i> Préstamos de Insumos
                </a>
            </li>
            <li>
                <a href="{{ route('infrastock.admin.supply-returns.index') }}" class="sidebar-submenu-item @if(Request::routeIs('infrastock.admin.supply-returns.*')) active @endif">
                    <i></i> Devoluciones
                </a>
            </li>
        </ul>
    </li>

    <!-- Herramientas - Enlace directo -->
    <li>
        <a href="{{ route('infrastock.admin.tools.index') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.admin.tools.*')) active @endif">
            <i class="fas fa-tools w-6 text-xl text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
            <span x-show="isSidebarExpanded || !isDesktop" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-300" 
                  x-transition:enter-start="opacity-0 transform scale-x-0" 
                  x-transition:enter-end="opacity-100 transform scale-x-100" 
                  x-transition:leave="transition ease-in duration-200" 
                  x-transition:leave-start="opacity-100 transform scale-x-100" 
                  x-transition:leave-end="opacity-0 transform scale-x-0" 
                  class="origin-left whitespace-nowrap">Herramientas</span>
        </a>
    </li>

    <!-- Préstamos y Devoluciones - Enlace directo -->
    <li>
        <a href="{{ route('infrastock.admin.loans.index') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.admin.loans.*')) active @endif">
            <i class="fas fa-clipboard-list w-6 text-xl text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
            <span x-show="isSidebarExpanded || !isDesktop" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-300" 
                  x-transition:enter-start="opacity-0 transform scale-x-0" 
                  x-transition:enter-end="opacity-100 transform scale-x-100" 
                  x-transition:leave="transition ease-in duration-200" 
                  x-transition:leave-start="opacity-100 transform scale-x-100" 
                  x-transition:leave-end="opacity-0 transform scale-x-0" 
                  class="origin-left whitespace-nowrap">Préstamos y Devoluciones</span>
        </a>
    </li>

    <!-- Gestión de Usuarios - Enlace directo -->
    <li>
        <a href="{{ route('infrastock.admin.users.index') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.admin.users.*')) active @endif">
            <i class="fas fa-users w-6 text-xl text-white opacity-90 hover:opacity-100 hover:text-green-200 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
            <span x-show="isSidebarExpanded || !isDesktop" 
                  x-cloak
                  x-transition:enter="transition ease-out duration-300" 
                  x-transition:enter-start="opacity-0 transform scale-x-0" 
                  x-transition:enter-end="opacity-100 transform scale-x-100" 
                  x-transition:leave="transition ease-in duration-200" 
                  x-transition:leave-start="opacity-100 transform scale-x-100" 
                  x-transition:leave-end="opacity-0 transform scale-x-0" 
                  class="origin-left whitespace-nowrap">Gestión de Usuarios</span>
        </a>
    </li>
@endsection

@section('navbar-notifications')
    <!-- Icono de notificaciones con contador y dropdown -->
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" class="relative text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 p-2.5 rounded-lg hover:bg-gray-100 transition-all duration-200 group">
            <i class="fas fa-bell text-xl group-hover:text-green-600 transition-colors duration-200"></i>
            @php
                $unreadCount = isset($notifications) ? $notifications->whereNull('read_at')->count() : 0;
            @endphp
            @if($unreadCount > 0)
                <span class="absolute top-0 right-0 -mt-1 -mr-1 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
            @endif
        </button>
        <!-- Dropdown de notificaciones -->
        <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-40 border border-gray-100 max-h-96 overflow-y-auto">
            <div class="px-4 py-2 text-sm text-gray-700 border-b border-gray-100 font-semibold">
                <i class="fas fa-bell mr-2"></i>Notificaciones
            </div>
            @php
                $notificationsAvailable = isset($notifications) && is_object($notifications) && method_exists($notifications, 'count');
                $notificationsCount = $notificationsAvailable ? $notifications->count() : 0;
                $supplyExpiringCount = $notificationsAvailable ? $notifications->where('type', 'supply_expiring')->count() : 0;
                if ($notificationsCount > 0) {
                    \Log::info('Vista master: Notificaciones disponibles: ' . $notificationsCount . ', supply_expiring: ' . $supplyExpiringCount);
                }
            @endphp
            @if($notificationsAvailable && $notificationsCount > 0)
                @foreach($notifications->take(5) as $notification)
                    <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50 cursor-pointer {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50' }}" 
                         onclick="markNotificationAsRead('{{ $notification->id }}')">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                @if($notification->type === 'request_created')
                                    <i class="fas fa-plus-circle text-green-500 text-lg"></i>
                                @elseif($notification->type === 'request_approved')
                                    <i class="fas fa-check-circle text-blue-500 text-lg"></i>
                                @elseif($notification->type === 'request_rejected')
                                    <i class="fas fa-times-circle text-red-500 text-lg"></i>
                                @elseif($notification->type === 'supply_expiring')
                                    <i class="fas fa-exclamation-triangle text-orange-500 text-lg"></i>
                                @elseif($notification->type === 'surplus_reported')
                                    <i class="fas fa-undo-alt text-purple-500 text-lg"></i>
                                @elseif($notification->type === 'loan_created')
                                    <i class="fas fa-tools text-indigo-500 text-lg"></i>
                                @elseif($notification->type === 'loan_approved')
                                    <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                @elseif($notification->type === 'loan_rejected')
                                    <i class="fas fa-times-circle text-red-500 text-lg"></i>
                                @else
                                    <i class="fas fa-bell text-gray-500 text-lg"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notificación' }}</p>
                                <p class="text-sm text-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
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
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-800">Ver todas las notificaciones</a>
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
    <a href="{{ route('cefa.infrastock.admin.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-user-circle mr-2 text-blue-500"></i> Editar Perfil</a>
    <a href="{{ route('infrastock.admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-sign-out-alt mr-2 text-red-500"></i> Cerrar Sesión</a>
    <form id="logout-form" action="{{ route('infrastock.admin.logout') }}" method="POST" class="hidden">
        @csrf
    </form>
@endsection

@section('breadcrumbs')
    {{-- Muestra el enlace "Dashboard" en las migas de pan solo si no estamos ya en el dashboard --}}
    @if (!Request::routeIs('cefa.infrastock.admin.dashboard'))
    <li class="flex items-center">
        <a href="{{ route('cefa.infrastock.admin.dashboard') }}" class="text-green-600 hover:text-green-800">Dashboard</a>
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
                    
                    // Si hay una URL de redirección (para cualquier tipo de notificación), redirigir
                    if (data.redirect_url) {
                        // Pequeño delay para que se vea el cambio visual antes de redirigir
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
                return !el.classList.contains('opacity-75') && 
                       el.querySelector('.w-2.h-2.bg-blue-500');
            }).length;
            
            const countBadge = document.querySelector('.bg-red-500');
            if (unreadCount > 0) {
                if (countBadge) {
                    countBadge.textContent = unreadCount;
                } else {
                    // Crear badge si no existe
                    const bellButton = document.querySelector('button[class*="fa-bell"]');
                    if (bellButton) {
                        const badge = document.createElement('span');
                        badge.className = 'absolute top-0 right-0 -mt-1 -mr-1 bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full';
                        badge.textContent = unreadCount;
                        bellButton.appendChild(badge);
                    }
                }
            } else {
                if (countBadge) {
                    countBadge.remove();
                }
            }
        }
    </script>
    @yield('additional-scripts')
@endsection
