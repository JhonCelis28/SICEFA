<!--
    * @file operator-master.blade.php
    * @brief Plantilla de diseño principal para el Operario del módulo INFRASTOCK.
    *
    * Esta vista Blade define la estructura global del panel del operario, utilizando
    * Tailwind CSS para los estilos y Alpine.js para la interactividad. Incluye un sidebar
    * de navegación específico para el operario, una barra superior con información
    * del usuario como "Operario", y un área de contenido principal.
    *
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'INFRASTOCK - Operario')</title>
    <link rel="icon" href="{{ asset('images/Favicon2.png') }}" type="image/x-icon">

    <!-- Tailwind CSS CDN para la estilización global de la interfaz -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts: "Plus Jakarta Sans" para texto general y "Dancing Script" para eslóganes -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">

    <!-- Font Awesome para íconos vectoriales -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- SweetAlert2 CSS para alertas y notificaciones estéticas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* Oculta elementos que usan x-cloak de Alpine.js hasta que Alpine.js los procese */
        [x-cloak] { display: none; }
        /* Estilos para elementos del menú principal del sidebar cuando están activos */
        .sidebar-menu-item.active {
            background-color: #10B981; /* green-500 */
            color: white; /* text-white */
            font-weight: 600; /* semi-bold */
            border-radius: 0.5rem; /* rounded-lg */
        }
        /* Estilos generales para elementos del menú principal del sidebar */
        .sidebar-menu-item {
            padding: 0.75rem 1rem; /* p-3 px-4 */
            display: flex;
            align-items: center;
            transition: all 0.2s ease-in-out;
        }
        /* Estilos de hover para elementos del menú principal del sidebar */
        .sidebar-menu-item:hover {
            background-color: #D1FAE5; /* green-100 */
            color: #065F46; /* green-800 */
            border-radius: 0.5rem;
        }
        /* Estilos para elementos del submenú del sidebar */
        .sidebar-submenu-item {
            padding: 0.5rem 1.5rem; /* p-2 pl-6 */
            display: flex;
            align-items: center;
            transition: all 0.2s ease-in-out;
        }
        /* Estilos de hover para elementos del submenú del sidebar */
        .sidebar-submenu-item:hover {
            background-color: #ECFDF5; /* green-50 */
            color: #065F46; /* green-800 */
            border-radius: 0.5rem;
        }

        /* Custom scrollbar para el sidebar, mejorando la estética */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1; /* slate-300 */
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8; /* slate-400 */
        }
    </style>

    @yield('head')

</head>

<body class="flex h-screen bg-gray-100 font-sans" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    <!-- Sidebar de navegación principal -->
    <div x-data="{
        sidebarOpen: window.innerWidth >= 768,
        init() {
            window.addEventListener('resize', () => {
                if (window.innerWidth < 768) {
                    this.sidebarOpen = false;
                } else {
                    this.sidebarOpen = true;
                }
            });
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        }
    }"
        @mouseenter=""
        @mouseleave=""
        :class="{ '-translate-x-full': !sidebarOpen, 'md:w-64': sidebarOpen, 'md:w-20': false }"
        class="fixed inset-y-0 left-0 z-50 bg-green-800 shadow-xl transform transition-all duration-300 ease-in-out md:relative">
        
        <!-- Encabezado del Sidebar: Logo y Eslogan -->
        <div class="flex flex-col items-center justify-center h-32 bg-green-900 text-white"
            :class="{ 'py-4': !sidebarOpen, 'py-6': sidebarOpen }">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="w-auto"
                :class="{ 'h-16': !sidebarOpen, 'h-24': sidebarOpen }">
            <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left mt-1 text-center">
                <span class="text-lg font-bold text-green-200" style="font-family: 'Dancing Script', cursive;">Control Preciso, Gestión Eficiente</span>
            </div>
        </div>

        <!-- Navegación del Sidebar: Lista de enlaces específicos para Operario -->
        <nav class="flex-1 px-2 py-8 space-y-1 custom-scrollbar overflow-y-auto">
            <ul class="space-y-1">
                <!-- Dashboard Principal -->
                <li>
                    <a href="{{ route('infrastock.operator.dashboard') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.operator.dashboard')) active @endif">
                        <i class="fas fa-tachometer-alt w-6 mr-3 text-lg text-green-300" :class="{'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left">Dashboard</span>
                    </a>
                </li>

                <!-- Stock Disponible -->
                <li>
                    <a href="{{ route('infrastock.operator.stock') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.operator.stock')) active @endif">
                        <i class="fas fa-boxes w-6 mr-3 text-lg text-green-300" :class="{'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left">Stock Disponible</span>
                    </a>
                </li>

                <!-- Mis Solicitudes -->
                <li>
                    <a href="{{ route('infrastock.operator.requests.index') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.operator.requests.*')) active @endif">
                        <i class="fas fa-clipboard-list w-6 mr-3 text-lg text-green-300" :class="{'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left">Mis Solicitudes</span>
                    </a>
                </li>

                <!-- Notificaciones -->
                <li>
                    <a href="{{ route('infrastock.operator.notifications') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.operator.notifications')) active @endif">
                        <i class="fas fa-bell w-6 mr-3 text-lg text-green-300" :class="{'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left">Notificaciones</span>
                    </a>
                </li>

                <!-- Reporte de Sobrantes -->
                <li>
                    <a href="{{ route('infrastock.operator.surplus-report') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.operator.surplus-report') || Request::routeIs('infrastock.operator.surplus.*')) active @endif">
                        <i class="fas fa-file-alt w-6 mr-3 text-lg text-green-300" :class="{'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left">Reporte de Sobrantes</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Área de Contenido Principal -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Barra de Navegación Superior (Top Navbar) -->
        <header class="flex items-center justify-between h-16 bg-white border-b border-gray-200 px-6 shadow-sm z-40">
            <div class="flex items-center">
                <!-- Botón de hamburguesa para alternar la visibilidad del sidebar en dispositivos móviles -->
                <button @click="if(window.innerWidth < 768) toggleSidebar()" class="text-gray-500 focus:outline-none focus:text-gray-700 md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                </button>
                <!-- Título de la página actual o "Dashboard" por defecto -->
                <h1 class="text-xl font-bold text-gray-800 ml-4">@yield('title', 'Dashboard')</h1>
            </div>

            <!-- Menú de usuario y notificaciones en la barra superior -->
            <div class="flex items-center space-x-4">
                @auth
                <!-- Menú desplegable de usuario autenticado -->
                <div x-data="{ dropdownOpen: false, notificationsOpen: false }" class="relative flex items-center space-x-4">
                    <!-- Campanita de Notificaciones -->
                    <div class="relative">
                        <button @click="notificationsOpen = !notificationsOpen" class="relative p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-full transition-colors duration-200">
                            <i class="fas fa-bell text-lg"></i>
                            @if(isset($notificationCount) && $notificationCount > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $notificationCount }}</span>
                            @endif
                        </button>
                        
                        <!-- Dropdown de Notificaciones -->
                        <div x-show="notificationsOpen" @click.away="notificationsOpen = false" x-cloak class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Notificaciones</h3>
                            </div>
                            <div class="max-h-96 overflow-y-auto">
                                @if(isset($notifications) && $notifications->count() > 0)
                                    @foreach($notifications->take(5) as $notification)
                                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors duration-200">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    @if(isset($notification->data['type']))
                                                        @if($notification->data['type'] == 'request_approved')
                                                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-check text-green-600 text-sm"></i>
                                                            </div>
                                                        @elseif($notification->data['type'] == 'request_rejected')
                                                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-times text-red-600 text-sm"></i>
                                                            </div>
                                                        @else
                                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                                <i class="fas fa-bell text-blue-600 text-sm"></i>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-bell text-blue-600 text-sm"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notificación' }}</p>
                                                    <p class="text-xs text-gray-500 mt-1">{{ $notification->data['message'] ?? 'Sin mensaje' }}</p>
                                                    <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($notifications->count() > 5)
                                        <div class="p-4 text-center">
                                            <a href="{{ route('infrastock.operator.notifications') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">
                                                Ver todas las notificaciones ({{ $notifications->count() }})
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="p-8 text-center text-gray-500">
                                        <i class="fas fa-bell-slash text-3xl mb-3"></i>
                                        <p class="text-sm">No hay notificaciones</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Dropdown del Usuario -->
                    <div class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-gray-800 hover:text-gray-900 focus:outline-none focus:text-gray-900 p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                            <img src="{{ asset('AdminLTE/dist/img/user2-160x160.jpg') }}" class="h-8 w-8 rounded-full object-cover" alt="User Image">
                            <div>
                                <span class="font-semibold text-base block text-left">{{ Auth::user()->nickname ?? Auth::user()->name }}</span>
                                <span class="text-xs text-gray-500 block text-left">Operario</span>
                            </div>
                            <i class="fas fa-angle-down ml-1 text-sm"></i>
                        </button>

                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
                        <button onclick="openProfileModal()" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-user-circle mr-2 text-blue-500"></i> Editar Perfil</button>
                        <a href="{{ route('infrastock.operator.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-sign-out-alt mr-2 text-red-500"></i> Cerrar Sesión</a>
                        <form id="logout-form" action="{{ route('infrastock.operator.logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                </div>
                @endauth
                @guest
                <!-- Botón para iniciar sesión si el usuario no está autenticado -->
                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-md hover:bg-green-700 transition-colors duration-200">Iniciar Sesión</a>
                @endguest
            </div>
        </header>

        <!-- Área de Contenido Principal -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
            <!-- Migas de pan (Breadcrumbs) y Título de la Página -->
            <div class="mb-6">
                @hasSection('title')
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">@yield('title')</h1>
                @endif
                <nav class="text-sm font-medium text-gray-500" aria-label="breadcrumb">
                    <ol class="flex items-center space-x-2">
                        @if (!Request::routeIs('infrastock.operator.dashboard'))
                        <li class="flex items-center">
                            <a href="{{ route('infrastock.operator.dashboard') }}" class="text-green-600 hover:text-green-800">Dashboard</a>
                            <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        </li>
                        @endif
                        @hasSection('breadcrumb-items')
                            @yield('breadcrumb-items')
                        @endif
                    </ol>
                </nav>
            </div>

            {{-- Aquí se renderizará el contenido específico de cada vista hija --}}
            @yield('content')
        </main>

        <!-- Pie de página del Área de Contenido Principal -->
        <footer class="bg-white border-t border-gray-200 p-4 text-center text-gray-600 text-sm">
            &copy; {{ date('Y') }} INFRASTOCK - Operario. Todos los derechos reservados.
        </footer>

    </div>

    <!-- Modal de Perfil -->
    <div id="profileModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header del Modal -->
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="bg-green-100 p-3 rounded-full mr-4">
                            <i class="fas fa-user-circle text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Mi Perfil</h3>
                            <p class="text-gray-600">Gestiona tu información personal</p>
                        </div>
                    </div>
                    <button onclick="closeProfileModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Contenido del Modal -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Formulario de Edición -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="bg-blue-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-edit text-blue-600"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-800">Editar Información</h4>
                            </div>

                            <form id="profileForm" class="space-y-4">
                                @csrf
                                @method('PUT')
                                
                                <!-- Nombre -->
                                <div>
                                    <label for="modal_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-user mr-1 text-green-500"></i>
                                        Nombre Completo *
                                    </label>
                                    <input type="text" name="name" id="modal_name" required
                                           value="{{ auth()->user()->name }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200">
                                </div>

                                <!-- Correo Electrónico -->
                                <div>
                                    <label for="modal_email" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-envelope mr-1 text-green-500"></i>
                                        Correo Electrónico *
                                    </label>
                                    <input type="email" name="email" id="modal_email" required
                                           value="{{ auth()->user()->email }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200">
                                </div>

                                <!-- Apodo/Nickname -->
                                <div>
                                    <label for="modal_nickname" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-tag mr-1 text-green-500"></i>
                                        Apodo (Opcional)
                                    </label>
                                    <input type="text" name="nickname" id="modal_nickname"
                                           value="{{ auth()->user()->nickname }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200">
                                </div>

                                <!-- Contraseña -->
                                <div>
                                    <label for="modal_password" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-lock mr-1 text-green-500"></i>
                                        Nueva Contraseña (Opcional)
                                    </label>
                                    <input type="password" name="password" id="modal_password"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                                           placeholder="Deja vacío para mantener la contraseña actual">
                                </div>

                                <!-- Confirmar Contraseña -->
                                <div>
                                    <label for="modal_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-lock mr-1 text-green-500"></i>
                                        Confirmar Nueva Contraseña
                                    </label>
                                    <input type="password" name="password_confirmation" id="modal_password_confirmation"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                                           placeholder="Confirma tu nueva contraseña">
                                </div>

                                <!-- Botones -->
                                <div class="flex justify-end space-x-3 pt-4">
                                    <button type="button" onclick="closeProfileModal()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                                        <i class="fas fa-times mr-1"></i>
                                        Cancelar
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                                        <i class="fas fa-save mr-1"></i>
                                        Actualizar
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Información Actual -->
                        <div>
                            <div class="flex items-center mb-4">
                                <div class="bg-purple-100 p-2 rounded-full mr-3">
                                    <i class="fas fa-info-circle text-purple-600"></i>
                                </div>
                                <h4 class="text-lg font-semibold text-gray-800">Información Actual</h4>
                            </div>

                            <div class="space-y-4">
                                <!-- Avatar y Nombre -->
                                <div class="text-center p-4 bg-gray-50 rounded-lg">
                                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-user text-green-600 text-2xl"></i>
                                    </div>
                                    <h5 class="font-bold text-gray-900">{{ auth()->user()->name }}</h5>
                                    @if(auth()->user()->nickname)
                                        <p class="text-gray-600 text-sm">"{{ auth()->user()->nickname }}"</p>
                                    @endif
                                    <span class="inline-block mt-2 bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                        Operario
                                    </span>
                                </div>

                                <!-- Información Detallada -->
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-envelope text-gray-500"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Correo Electrónico</p>
                                            <p class="text-gray-900 text-sm">{{ auth()->user()->email }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-calendar text-gray-500"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Miembro desde</p>
                                            <p class="text-gray-900 text-sm">{{ auth()->user()->created_at->format('d/m/Y') }}</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                                        <i class="fas fa-shield-alt text-gray-500"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-700">Estado de la cuenta</p>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Activa
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estadísticas -->
                                <div class="border-t pt-4">
                                    <h6 class="font-semibold text-gray-900 mb-3">Estadísticas de Actividad</h6>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                                            <i class="fas fa-clipboard-list text-blue-600 text-lg mb-1"></i>
                                            <p class="text-xs font-medium text-gray-700">Solicitudes</p>
                                            <p class="text-sm font-bold text-blue-600">{{ \Modules\INFRASTOCK\Entities\Request::where('user_id', auth()->id())->count() }}</p>
                                        </div>
                                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                                            <i class="fas fa-file-alt text-orange-600 text-lg mb-1"></i>
                                            <p class="text-xs font-medium text-gray-700">Sobrantes</p>
                                            <p class="text-sm font-bold text-orange-600">{{ \Modules\INFRASTOCK\Entities\Surplus::where('user_id', auth()->id())->count() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS para mostrar mensajes de éxito/error/información -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js CDN para añadir reactividad y funcionalidad al HTML -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- JavaScript para el Modal de Perfil -->
    <script>
        function openProfileModal() {
            document.getElementById('profileModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeProfileModal() {
            document.getElementById('profileModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        document.getElementById('profileModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeProfileModal();
            }
        });

        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Actualizando...';
            submitButton.disabled = true;
            
            fetch('{{ route("infrastock.operator.profile.update") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Perfil Actualizado!',
                        text: 'Tu información ha sido actualizada exitosamente.',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        closeProfileModal();
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Error al actualizar el perfil');
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'Hubo un problema al actualizar tu perfil. Inténtalo de nuevo.',
                    confirmButtonText: 'Entendido'
                });
            })
            .finally(() => {
                submitButton.innerHTML = originalText;
                submitButton.disabled = false;
            });
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeProfileModal();
            }
        });
    </script>

    <script>
        @if(session('success'))
            Swal.fire({
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#10B981'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Error',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#EF4444'
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                title: 'Advertencia',
                text: '{{ session('warning') }}',
                icon: 'warning',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#F59E0B'
            });
        @endif

        @if(session('info'))
            Swal.fire({
                title: 'Información',
                text: '{{ session('info') }}',
                icon: 'info',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#3B82F6'
            });
        @endif
    </script>

    @yield('script')

</body>

</html>





