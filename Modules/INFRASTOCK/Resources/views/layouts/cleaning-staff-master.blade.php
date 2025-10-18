<!--
    * @file cleaning-staff-master.blade.php
    * @brief Plantilla de diseño principal para el Personal de Aseo del módulo INFRASTOCK.
    *
    * Esta vista Blade define la estructura global del panel del personal de aseo, utilizando
    * Tailwind CSS para los estilos y Alpine.js para la interactividad. Incluye un sidebar
    * de navegación específico para el personal de aseo, una barra superior con información
    * del usuario como "Personal de Aseo", y un área de contenido principal.
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
    <title>@yield('title', 'INFRASTOCK - Personal de Aseo')</title>
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
        sidebarOpen: window.innerWidth >= 768, // El sidebar está abierto por defecto en escritorio, cerrado en móvil.
        init() {
            // Escucha el evento de redimensionamiento de la ventana para ajustar el estado del sidebar.
            window.addEventListener('resize', () => {
                if (window.innerWidth < 768) {
                    this.sidebarOpen = false; // Cierra el sidebar automáticamente en dispositivos móviles.
                } else {
                    this.sidebarOpen = true; // Abre el sidebar automáticamente en dispositivos de escritorio.
                }
            });
        },
        // Función para alternar el estado de apertura/cierre del sidebar.
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        }
    }"
        @mouseenter="" {{-- Evento mouseenter vacio para anular comportamiento previo --}}
        @mouseleave="" {{-- Evento mouseleave vacio para anular comportamiento previo --}}
        :class="{ '-translate-x-full': !sidebarOpen, 'md:w-64': sidebarOpen, 'md:w-20': false }" {{-- Clases dinámicas para controlar el ancho y la visibilidad del sidebar --}}
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

        <!-- Navegación del Sidebar: Lista de enlaces específicos para Personal de Aseo -->
        <nav class="flex-1 px-2 py-8 space-y-1 custom-scrollbar overflow-y-auto">
            <ul class="space-y-1">
                <!-- Dashboard Principal -->
                <li>
                    <a href="{{ route('infrastock.cleaning-staff.dashboard') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.cleaning-staff.dashboard')) active @endif">
                        <i class="fas fa-tachometer-alt w-6 mr-3 text-lg text-green-300" :class="{'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left">Dashboard</span>
                    </a>
                </li>

                <!-- Gestión de Solicitudes -->
                <li x-data="{ open: false }">
                    <a @click="open = !open" class="sidebar-menu-item font-bold cursor-pointer @if(Request::routeIs('infrastock.cleaning-staff.requests.*')) active @endif">
                        <i class="fas fa-clipboard-list w-6 mr-3 text-lg text-green-300" :class="{'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left">Mis Solicitudes</span>
                        <i x-show="sidebarOpen" :class="{ 'fa-angle-down': open, 'fa-angle-left': !open }" class="fas ml-auto transition-transform duration-200 text-green-300"></i>
                    </a>
                    <ul x-show="open && sidebarOpen" x-cloak class="ml-6 mt-1 space-y-1 bg-green-700 rounded-lg p-1">
                        <li><a href="{{ route('infrastock.cleaning-staff.requests.create') }}" class="sidebar-submenu-item text-gray-200 @if(Request::routeIs('infrastock.cleaning-staff.requests.create')) active @endif"><i class="far fa-circle text-xs mr-3"></i> Nueva Solicitud</a></li>
                        <li><a href="{{ route('infrastock.cleaning-staff.requests.index') }}" class="sidebar-submenu-item text-gray-200 @if(Request::routeIs('infrastock.cleaning-staff.requests.index')) active @endif"><i class="far fa-circle text-xs mr-3"></i> Ver Mis Solicitudes</a></li>
                    </ul>
                </li>

                <!-- Notificaciones -->
                <li>
                    <a href="{{ route('infrastock.cleaning-staff.notifications') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.cleaning-staff.notifications')) active @endif">
                        <i class="fas fa-bell w-6 mr-3 text-lg text-green-300" :class="{'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left">Notificaciones</span>
                    </a>
                </li>

                <!-- Mi Perfil -->
                <li>
                    <a href="{{ route('infrastock.cleaning-staff.profile') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.cleaning-staff.profile')) active @endif">
                        <i class="fas fa-user w-6 mr-3 text-lg text-green-300" :class="{'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left">Mi Perfil</span>
                    </a>
                </li>

                <!-- Reportes -->
                <li x-data="{ open: false }">
                    <a @click="open = !open" class="sidebar-menu-item font-bold cursor-pointer @if(Request::routeIs(['infrastock.cleaning-staff.surplus-report', 'infrastock.cleaning-staff.supply-history'])) active @endif">
                        <i class="fas fa-chart-bar w-6 mr-3 text-lg text-green-300" :class="{'mr-0': !sidebarOpen, 'mr-3': sidebarOpen }"></i>
                        <span x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-x-0" x-transition:enter-end="opacity-100 transform scale-x-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-x-100" x-transition:leave-end="opacity-0 transform scale-x-0" class="origin-left">Reportes</span>
                        <i x-show="sidebarOpen" :class="{ 'fa-angle-down': open, 'fa-angle-left': !open }" class="fas ml-auto transition-transform duration-200 text-green-300"></i>
                    </a>
                    <ul x-show="open && sidebarOpen" x-cloak class="ml-6 mt-1 space-y-1 bg-green-700 rounded-lg p-1">
                        <li><a href="{{ route('infrastock.cleaning-staff.surplus-report') }}" class="sidebar-submenu-item text-gray-200 @if(Request::routeIs('infrastock.cleaning-staff.surplus-report')) active @endif"><i class="far fa-circle text-xs mr-3"></i> Reporte de Sobrantes</a></li>
                        <li><a href="{{ route('infrastock.cleaning-staff.supply-history') }}" class="sidebar-submenu-item text-gray-200 @if(Request::routeIs('infrastock.cleaning-staff.supply-history')) active @endif"><i class="far fa-circle text-xs mr-3"></i> Historial de Insumos</a></li>
                    </ul>
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
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-gray-800 hover:text-gray-900 focus:outline-none focus:text-gray-900 p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <img src="{{ asset('AdminLTE/dist/img/user2-160x160.jpg') }}" class="h-8 w-8 rounded-full object-cover" alt="User Image">
                        <div>
                            <span class="font-semibold text-base block text-left">{{ Auth::user()->nickname ?? Auth::user()->name }}</span>
                            <span class="text-xs text-gray-500 block text-left">Personal de Aseo</span> {{-- Rol específico del Personal de Aseo --}}
                        </div>
                        <i class="fas fa-angle-down ml-1 text-sm"></i>
                    </button>

                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-100">
                        <a href="{{ route('infrastock.cleaning-staff.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-user-circle mr-2 text-blue-500"></i> Editar Perfil</a>
                        <a href="{{ route('infrastock.cleaning-staff.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-sign-out-alt mr-2 text-red-500"></i> Cerrar Sesión</a>
                        <form id="logout-form" action="{{ route('infrastock.cleaning-staff.logout') }}" method="POST" class="hidden">
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
                        {{-- Muestra el enlace "Dashboard" en las migas de pan solo si no estamos ya en el dashboard --}}
                        @if (!Request::routeIs('infrastock.cleaning-staff.dashboard'))
                        <li class="flex items-center">
                            <a href="{{ route('infrastock.cleaning-staff.dashboard') }}" class="text-green-600 hover:text-green-800">Dashboard</a>
                            <svg class="h-4 w-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        </li>
                        @endif
                        @hasSection('breadcrumb-items')
                            {{-- Aquí se insertan ítems adicionales para las migas de pan definidos en las vistas hijas --}}
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
            &copy; {{ date('Y') }} INFRASTOCK - Personal de Aseo. Todos los derechos reservados.
        </footer>

    </div>

    <!-- SweetAlert2 JS para mostrar mensajes de éxito/error/información -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js CDN para añadir reactividad y funcionalidad al HTML -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // Lógica de SweetAlert2 para mostrar notificaciones basadas en mensajes de sesión de Laravel
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
