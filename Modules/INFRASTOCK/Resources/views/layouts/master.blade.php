<!--
    * @file master.blade.php
    * @brief Plantilla de diseño principal para el panel administrativo del módulo INFRASTOCK.
    *
    * Esta vista Blade define la estructura global del panel de administración, utilizando
    * Tailwind CSS para los estilos y Alpine.js para la interactividad. Incluye un sidebar
    * de navegación adaptable, una barra superior (navbar) con funcionalidades como búsqueda,
    * notificaciones y menú de usuario, un área de contenido principal y un pie de página.
    * Es la base para todas las vistas dentro del panel administrativo de INFRASTOCK.
    *
    * @param int $pendingSupplyRequestsCount Número de solicitudes de insumos pendientes.
    * @param int $expiringSuppliesCount Número de insumos próximos a vencer.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'INFRASTOCK - Panel Administrativo')</title>
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
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">

    <style>
        /* Oculta elementos que usan x-cloak de Alpine.js hasta que Alpine.js los procese */
        [x-cloak] { display: none; }
        
        /* Estilos para DataTables con Tailwind */
        .dataTables_wrapper {
            padding: 1rem 0;
        }
        
        .dataTables_filter input {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            margin-left: 0.5rem;
        }
        
        .dataTables_length select {
            padding: 0.5rem 2rem 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            margin: 0 0.5rem;
        }
        
        .dataTables_info {
            padding-top: 0.75rem;
            color: #6b7280;
        }
        
        .dataTables_paginate {
            padding-top: 0.75rem;
        }
        
        .dataTables_paginate .paginate_button {
            padding: 0.5rem 0.75rem;
            margin: 0 0.25rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            color: #374151;
            cursor: pointer;
        }
        
        .dataTables_paginate .paginate_button:hover {
            background-color: #f3f4f6;
            border-color: #9ca3af;
        }
        
        .dataTables_paginate .paginate_button.current {
            background-color: #10b981;
            color: white;
            border-color: #10b981;
        }
        
        .dataTables_paginate .paginate_button.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        /* Estilos para elementos del menú principal del sidebar cuando están activos */
        .sidebar-menu-item.active {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            font-weight: 600;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-left: 3px solid #34D399;
        }
        /* Estilos generales para elementos del menú principal del sidebar */
        .sidebar-menu-item {
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #D1FAE5;
            border-radius: 0.5rem;
            margin: 0.25rem 0;
            position: relative;
        }
        /* Estilos de hover para elementos del menú principal del sidebar */
        .sidebar-menu-item:hover:not(.active) {
            background: rgba(16, 185, 129, 0.15);
            color: white;
            transform: translateX(4px);
            border-left: 3px solid rgba(52, 211, 153, 0.5);
        }
        /* Estilos para elementos del submenú del sidebar */
        .sidebar-submenu-item {
            padding: 0.625rem 1.5rem;
            display: flex;
            align-items: center;
            transition: all 0.25s ease-in-out;
            color: #A7F3D0;
            border-radius: 0.375rem;
            margin: 0.125rem 0;
            font-size: 0.875rem;
        }
        /* Estilos de hover para elementos del submenú del sidebar */
        .sidebar-submenu-item:hover:not(.active) {
            background: rgba(16, 185, 129, 0.2);
            color: white;
            transform: translateX(4px);
            border-left: 2px solid rgba(52, 211, 153, 0.6);
        }
        .sidebar-submenu-item.active {
            background: rgba(16, 185, 129, 0.25);
            color: white;
            font-weight: 500;
            border-left: 2px solid #34D399;
        }

        /* Ocultar scrollbar pero mantener funcionalidad de scroll */
        .sidebar-nav {
            overflow-y: auto !important;
            overflow-x: hidden !important;
            -ms-overflow-style: none !important;  /* IE and Edge */
            scrollbar-width: none !important;  /* Firefox */
        }
        
        .sidebar-nav::-webkit-scrollbar {
            display: none !important;  /* Chrome, Safari, Opera */
            width: 0px !important;
            background: transparent !important;
        }
        
        .sidebar-nav::-webkit-scrollbar-track {
            display: none !important;
        }
        
        .sidebar-nav::-webkit-scrollbar-thumb {
            display: none !important;
        }

        /* Asegurar que los modales tengan el z-index más alto */
        .modal-overlay {
            z-index: 9999 !important;
        }
        
        .modal-content {
            z-index: 10000 !important;
        }

        /* Mejorar el comportamiento de x-cloak para evitar parpadeos */
        [x-cloak] { 
            display: none !important; 
        }
        
        /* Asegurar que los modales estén ocultos por defecto */
        .modal-overlay[x-cloak] {
            display: none !important;
        }
    </style>

    @yield('head')

</head>

<body class="flex h-screen bg-gray-100 font-sans" x-data="{
    sidebarOpen: false,
    isDesktop: false,
    init() {
        // Verificar si estamos en desktop al inicializar
        this.isDesktop = window.innerWidth >= 768;
        // Por defecto cerrado (solo iconos)
        this.sidebarOpen = false;
        
        // Escucha el evento de redimensionamiento de la ventana
        window.addEventListener('resize', () => {
            this.isDesktop = window.innerWidth >= 768;
            if (!this.isDesktop) {
                this.sidebarOpen = false; // Cierra el sidebar en móviles
            }
        });
    },
    // Función para alternar el estado de apertura/cierre del sidebar
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    }
}" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    <!-- Sidebar de navegación principal -->
    <div
        :class="{ 
            '-translate-x-full': !sidebarOpen && !isDesktop, 
            'md:w-20': !sidebarOpen && isDesktop, 
            'md:w-64': sidebarOpen && isDesktop
        }"
        class="fixed md:relative inset-y-0 left-0 z-40 bg-gradient-to-b from-green-800 to-green-900 shadow-2xl transform transition-all duration-300 ease-in-out border-r border-green-700 h-screen md:h-full flex flex-col">
        
        <!-- Encabezado del Sidebar: Logo y Eslogan -->
        <div class="flex flex-col items-center justify-center bg-gradient-to-br from-green-900 to-green-800 text-white px-2 py-4 border-b border-green-700" style="min-height: 120px; height: 120px;">
            <div class="flex items-center justify-center w-full mb-3" style="height: 70px; min-height: 70px;">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" 
                    class="object-contain drop-shadow-lg"
                    style="width: 60px; height: 60px; max-width: 60px; max-height: 60px; filter: brightness(1.1);">
            </div>
            <div class="h-10 flex items-center justify-center" style="min-height: 40px; height: 40px;">
                <span x-show="sidebarOpen || !isDesktop" 
                      x-transition:enter="transition ease-out duration-300" 
                      x-transition:enter-start="opacity-0 transform scale-x-0" 
                      x-transition:enter-end="opacity-100 transform scale-x-100" 
                      x-transition:leave="transition ease-in duration-200" 
                      x-transition:leave-start="opacity-100 transform scale-x-100" 
                      x-transition:leave-end="opacity-0 transform scale-x-0" 
                      class="text-base font-semibold text-green-200 text-center px-2" 
                      style="font-family: 'Dancing Script', cursive; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Control Preciso, Gestión Eficiente</span>
            </div>
        </div>

        <!-- Sección de Perfil de Usuario (comentada, ya que la información se muestra en el navbar) -->
        {{-- @auth
        <div class="p-4 border-b border-green-700" x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="flex items-center space-x-3">
                <img src="{{ asset('AdminLTE/dist/img/user2-160x160.jpg') }}" class="h-12 w-12 rounded-full object-cover border-2 border-white" alt="User Image">
                <div>
                    <p class="text-base font-semibold text-white">{{ Auth::user()->nickname ?? Auth::user()->name }}</p>
                    <p class="text-xs text-green-200">Administrador</p>
                </div>
            </div>
        </div>
        @endauth --}}

        <!-- Navegación del Sidebar: Lista de enlaces a las diferentes secciones del módulo -->
        <nav class="flex-1 px-3 py-6 space-y-1 sidebar-nav" style="overflow-y: auto; overflow-x: hidden; max-height: calc(100vh - 160px);">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('cefa.infrastock.admin.dashboard') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('cefa.infrastock.admin.dashboard')) active @endif">
                        <i class="fas fa-tachometer-alt w-6 text-lg text-green-300 flex-shrink-0 transition-all duration-300" :class="{'mr-0': !sidebarOpen && isDesktop, 'mr-3': sidebarOpen || !isDesktop}"></i>
                        <span x-show="sidebarOpen || !isDesktop" 
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
                        <i class="fas fa-sitemap w-6 text-lg text-green-300 flex-shrink-0 transition-all duration-300" :class="{'mr-0': !sidebarOpen && isDesktop, 'mr-3': sidebarOpen || !isDesktop}"></i>
                        <span x-show="sidebarOpen || !isDesktop" 
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
                        <i class="fas fa-layer-group w-6 text-lg text-green-300 flex-shrink-0 transition-all duration-300" :class="{'mr-0': !sidebarOpen && isDesktop, 'mr-3': sidebarOpen || !isDesktop}"></i>
                        <span x-show="sidebarOpen || !isDesktop" 
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
                        <i class="fas fa-boxes w-6 text-lg text-green-300 flex-shrink-0 transition-all duration-300" :class="{'mr-0': !sidebarOpen && isDesktop, 'mr-3': sidebarOpen || !isDesktop}"></i>
                        <span x-show="sidebarOpen || !isDesktop" 
                              x-transition:enter="transition ease-out duration-300" 
                              x-transition:enter-start="opacity-0 transform scale-x-0" 
                              x-transition:enter-end="opacity-100 transform scale-x-100" 
                              x-transition:leave="transition ease-in duration-200" 
                              x-transition:leave-start="opacity-100 transform scale-x-100" 
                              x-transition:leave-end="opacity-0 transform scale-x-0" 
                              class="origin-left whitespace-nowrap flex-1">Insumos</span>
                        <i x-show="sidebarOpen || !isDesktop" 
                           :class="{ 'fa-angle-down': open, 'fa-angle-left': !open }" 
                           class="fas ml-auto transition-transform duration-200 text-green-300 flex-shrink-0"></i>
                    </a>
                    <ul x-show="open && (sidebarOpen || !isDesktop)" 
                        x-cloak 
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2"
                        class="ml-6 mt-1 space-y-1 bg-green-700/50 backdrop-blur-sm rounded-lg p-2 border border-green-600/30">
                        <li><a href="{{ route('infrastock.admin.supplies.index') }}" class="sidebar-submenu-item text-gray-200 @if(Request::routeIs(['infrastock.admin.supplies.index', 'infrastock.admin.supplies.create', 'infrastock.admin.supplies.edit'])) active @endif"><i class="far fa-circle text-xs mr-3"></i> Gestionar Insumos</a></li>
                        <li>
                            <a href="{{ route('infrastock.admin.supply-requests.index') }}" class="sidebar-submenu-item text-gray-200 @if(Request::routeIs('infrastock.admin.supply-requests.index')) active @endif">
                                <i class="far fa-circle text-xs mr-3"></i> Solicitudes
                                @if($pendingSupplyRequestsCount > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs font-semibold px-2 py-0.5 rounded-full">{{ $pendingSupplyRequestsCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('infrastock.admin.supplies.loans.index') }}" class="sidebar-submenu-item text-gray-200 @if(Request::routeIs('infrastock.admin.supplies.loans.*')) active @endif">
                                <i class="far fa-circle text-xs mr-3"></i> Préstamos de Insumos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('infrastock.admin.supply-returns.index') }}" class="sidebar-submenu-item text-gray-200 @if(Request::routeIs('infrastock.admin.supply-returns.*')) active @endif">
                                <i class="far fa-circle text-xs mr-3"></i> Devoluciones
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Herramientas - Enlace directo -->
                <li>
                    <a href="{{ route('infrastock.admin.tools.index') }}" class="sidebar-menu-item font-bold @if(Request::routeIs('infrastock.admin.tools.*')) active @endif">
                        <i class="fas fa-tools w-6 text-lg text-green-300 flex-shrink-0 transition-all duration-300" :class="{'mr-0': !sidebarOpen && isDesktop, 'mr-3': sidebarOpen || !isDesktop}"></i>
                        <span x-show="sidebarOpen || !isDesktop" 
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
                        <i class="fas fa-clipboard-list w-6 text-lg text-green-300 flex-shrink-0 transition-all duration-300" :class="{'mr-0': !sidebarOpen && isDesktop, 'mr-3': sidebarOpen || !isDesktop}"></i>
                        <span x-show="sidebarOpen || !isDesktop" 
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
                        <i class="fas fa-users w-6 text-lg text-green-300 flex-shrink-0 transition-all duration-300" :class="{'mr-0': !sidebarOpen && isDesktop, 'mr-3': sidebarOpen || !isDesktop}"></i>
                        <span x-show="sidebarOpen || !isDesktop" 
                              x-transition:enter="transition ease-out duration-300" 
                              x-transition:enter-start="opacity-0 transform scale-x-0" 
                              x-transition:enter-end="opacity-100 transform scale-x-100" 
                              x-transition:leave="transition ease-in duration-200" 
                              x-transition:leave-start="opacity-100 transform scale-x-100" 
                              x-transition:leave-end="opacity-0 transform scale-x-0" 
                              class="origin-left whitespace-nowrap">Gestión de Usuarios</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Área de Contenido Principal -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Barra de Navegación Superior (Top Navbar) -->
        <header class="flex items-center justify-between h-16 bg-gradient-to-r from-white to-gray-50 border-b border-gray-200 px-6 shadow-md z-30 backdrop-blur-sm">
            <div class="flex items-center">
                <!-- Botón de hamburguesa para alternar la visibilidad del sidebar (visible siempre) -->
                <button @click="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 rounded-lg p-2 transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path x-show="!sidebarOpen || !isDesktop" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="sidebarOpen && isDesktop" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Menú de usuario y notificaciones en la barra superior -->
            <div class="flex items-center space-x-4">
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
                            // Debug temporal - remover después
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
                @auth
                <!-- Menú desplegable de usuario autenticado -->
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-gray-800 hover:text-gray-900 focus:outline-none focus:text-gray-900 p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <img src="{{ asset('AdminLTE/dist/img/user2-160x160.jpg') }}" class="h-8 w-8 rounded-full object-cover" alt="User Image">
                        <div>
                            <span class="font-semibold text-base block text-left">{{ Auth::user()->nickname ?? Auth::user()->name }}</span>
                            <span class="text-xs text-gray-500 block text-left">Administrador</span> {{-- Rol del usuario (ej. Administrador) --}}
                        </div>
                        <i class="fas fa-angle-down ml-1 text-sm"></i>
                    </button>

                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-40 border border-gray-100">
                        <a href="{{ route('cefa.infrastock.admin.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-user-circle mr-2 text-blue-500"></i> Editar Perfil</a>
                        <a href="{{ route('infrastock.admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fas fa-sign-out-alt mr-2 text-red-500"></i> Cerrar Sesión</a>
                        <form id="logout-form" action="{{ route('infrastock.admin.logout') }}" method="POST" class="hidden">
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
                        @hasSection('title')
                            {{-- Se comenta el título de la página en el breadcrumb para evitar redundancia con el h1. --}}
                            {{-- <li class="text-gray-700">@yield('title')</li> --}}
                        @endif
                    </ol>
                </nav>
            </div>

            {{-- Aquí se renderizará el contenido específico de cada vista hija --}}
            @yield('content')
        </main>

        <!-- Pie de página del Área de Contenido Principal -->
        <footer class="bg-white border-t border-gray-200 p-4 text-left text-gray-600 text-sm">
            &copy; {{ date('Y') }} INFRASTOCK. Todos los derechos reservados.
        </footer>

    </div>

    <!-- SweetAlert2 JS para mostrar mensajes de éxito/error/información -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Alpine.js CDN para añadir reactividad y funcionalidad al HTML -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        // La lógica de Alpine.js para el layout se ha movido directamente al atributo x-data del div del sidebar.
        // La definición Alpine.data('layoutData', ...) ya no es necesaria aquí.
        /*
        document.addEventListener('alpine:init', () => {
            Alpine.data('layoutData', () => ({
                sidebarOpen: window.innerWidth >= 768 && (window.localStorage.getItem('sidebarPinned') !== 'false'),
                init() {
                    // Observa los cambios de tamaño de ventana para ajustar sidebarOpen
                    window.addEventListener('resize', () => {
                        if (window.innerWidth < 768) {
                            this.sidebarOpen = false; // Cierra en móvil
                        } else {
                            // Si hay un estado guardado, úsalo, de lo contrario, abre por defecto
                            this.sidebarOpen = (window.localStorage.getItem('sidebarPinned') === 'true' || (window.localStorage.getItem('sidebarPinned') === null));
                        }
                    });
                }
            }));
        });
        */

        // Lógica de SweetAlert2 para mostrar notificaciones basadas en mensajes de sesión de Laravel
        @if(session('success'))
            Swal.fire({
                title: '¡Éxito!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'Aceptar'
            });
        @endif
    
        @if(session('error'))
            Swal.fire({
                title: '¡Atención!',
                text: '{{ session('error') }}',
                icon: 'warning',
                iconColor: '#ff8c00',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#ff8c00',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showCloseButton: false,
                width: '450px',
                customClass: {
                    popup: 'swal2-popup-custom',
                    title: 'swal2-title-custom',
                    content: 'swal2-content-custom',
                    confirmButton: 'swal2-confirm-custom'
                }
            });
        @endif
    
        @if(session('info'))
            Swal.fire({
                title: 'Información',
                text: '{{ session('info') }}',
                icon: 'info',
                confirmButtonText: 'Entendido'
            });
        @endif

        @if(session('warning'))
            Swal.fire({
                title: 'Advertencia',
                text: '{{ session('warning') }}',
                icon: 'warning',
                confirmButtonText: 'Entendido'
            });
        @endif

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

        // Auto-refresh de notificaciones cada 30 segundos
        // DESHABILITADO: Causaba recargas inesperadas de la página
        // Si necesitas actualizar notificaciones, usa AJAX en lugar de recargar toda la página
        /*
        setInterval(function() {
            if (document.visibilityState === 'visible') {
                // Solo recargar si hay notificaciones no leídas
                const notificationCount = document.querySelector('.bg-red-500');
                if (notificationCount && parseInt(notificationCount.textContent) > 0) {
                    location.reload();
                }
            }
        }, 30000);
        */
    </script>

    <!-- jQuery (requerido para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    {{-- Sección para scripts adicionales específicos de cada vista hija --}}
    @yield('script')

</body>

</html>
