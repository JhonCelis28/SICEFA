<!--
    * @file base-master.blade.php
    * @brief Layout base compartido para todos los roles del módulo INFRASTOCK.
    *
    * Este layout contiene la estructura común (sidebar, navbar, footer) con el mismo diseño
    * para todos los roles. Cada rol extiende este layout y define su propio menú del sidebar.
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
    <title>@yield('title', 'INFRASTOCK')</title>
    <!-- Favicon - Logo del proyecto (múltiples tamaños para mejor visualización) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/logo.png') }}">
    <link rel="shortcut icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/logo.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('assets/img/logo.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('assets/img/logo.png') }}">
    <meta name="msapplication-TileImage" content="{{ asset('assets/img/logo.png') }}">

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
            color: white;
            font-weight: 600;
            border-bottom: 3px solid #10B981;
            border-radius: 0;
        }
        
        /* Estilos para iconos cuando el menú está activo */
        .sidebar-menu-item.active i {
            color: #ffffff !important;
            opacity: 1 !important;
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
        /* Estilos para elementos del submenú del sidebar - Diseño Moderno */
        .sidebar-submenu-item {
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            color: #B2F5EA;
            border-radius: 0.75rem;
            margin: 0.375rem 0.5rem;
            font-size: 0.875rem;
            position: relative;
            background: transparent;
        }
        /* Estilos de hover para elementos del submenú del sidebar */
        .sidebar-submenu-item:hover:not(.active) {
            background: rgba(16, 185, 129, 0.2);
            color: white;
            transform: translateX(8px);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2);
        }
        .sidebar-submenu-item.active {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.4) 0%, rgba(5, 150, 105, 0.3) 100%);
            color: white;
            font-weight: 700;
            border-left: 4px solid #10B981;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            transform: translateX(4px);
        }
        /* Estilo para los iconos del submenú - Puntos modernos */
        .sidebar-submenu-item i {
            font-size: 0.5rem;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: currentColor;
            opacity: 0.6;
            margin-right: 0.75rem;
            flex-shrink: 0;
        }
        .sidebar-submenu-item:hover:not(.active) i {
            opacity: 0.9;
            transform: scale(1.2);
        }
        .sidebar-submenu-item.active i {
            opacity: 1;
            background: #10B981;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
            transform: scale(1.3);
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
    sidebarHover: false,
    isDesktop: window.innerWidth >= 768,
    init() {
        // Verificar si estamos en desktop al inicializar (ya se estableció arriba)
        // Por defecto cerrado (solo iconos)
        this.sidebarOpen = false;
        this.sidebarHover = false;
        
        // Escucha el evento de redimensionamiento de la ventana
        window.addEventListener('resize', () => {
            this.isDesktop = window.innerWidth >= 768;
            if (!this.isDesktop) {
                this.sidebarOpen = false; // Cierra el sidebar en móviles
                this.sidebarHover = false;
            }
        });
    },
    // Función para alternar el estado de apertura/cierre del sidebar
    toggleSidebar() {
        this.sidebarOpen = !this.sidebarOpen;
    },
    // Función para manejar el hover del sidebar
    handleSidebarHover(enter) {
        if (this.isDesktop && !this.sidebarOpen) {
            this.sidebarHover = enter;
        }
    },
    // Computed: el sidebar está expandido si está abierto manualmente O si hay hover
    get isSidebarExpanded() {
        return this.sidebarOpen || this.sidebarHover;
    }
}" style="font-family: 'Plus Jakarta Sans', sans-serif;">

    <!-- Sidebar de navegación principal -->
    <div
        @mouseenter="handleSidebarHover(true)"
        @mouseleave="handleSidebarHover(false)"
        :class="{ 
            '-translate-x-full': !sidebarOpen && !isDesktop, 
            'md:w-20': !isSidebarExpanded && isDesktop, 
            'md:w-64': isSidebarExpanded && isDesktop
        }"
        class="fixed md:relative inset-y-0 left-0 z-40 bg-gradient-to-b from-green-800 to-green-900 shadow-2xl transform transition-all duration-300 ease-in-out border-r border-green-700 h-screen md:h-full flex flex-col md:w-20">
        
        <!-- Encabezado del Sidebar: Logo -->
        <div class="flex flex-col items-center justify-center bg-gradient-to-br from-green-900 to-green-800 text-white px-2 py-4 border-b border-green-700" style="min-height: 120px; height: 120px;">
            <div class="flex items-center justify-center w-full" style="height: 110px; min-height: 110px;">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" 
                    class="object-contain drop-shadow-lg"
                    style="width: 110px; height: 110px; max-width: 110px; max-height: 110px; filter: brightness(1.1);">
            </div>
        </div>

        <!-- Navegación del Sidebar: Contenido específico de cada rol -->
        <nav class="flex-1 px-3 py-6 space-y-1 sidebar-nav" style="overflow-y: auto; overflow-x: hidden; max-height: calc(100vh - 160px);">
            <ul class="space-y-1">
                <!-- Botón Volver a SICEFA -->
                <li>
                    <a href="{{ route('cefa.welcome') }}" class="sidebar-menu-item font-bold">
                        <i class="fas fa-puzzle-piece w-6 text-xl text-orange-500 opacity-90 hover:opacity-100 hover:text-orange-400 flex-shrink-0 transition-all duration-300 drop-shadow-sm" :class="{'mr-0': !isSidebarExpanded && isDesktop, 'mr-3': isSidebarExpanded || !isDesktop}"></i>
                        <span x-show="isSidebarExpanded || !isDesktop" 
                              x-cloak
                              x-transition:enter="transition ease-out duration-300" 
                              x-transition:enter-start="opacity-0 transform scale-x-0" 
                              x-transition:enter-end="opacity-100 transform scale-x-100" 
                              x-transition:leave="transition ease-in duration-200" 
                              x-transition:leave-start="opacity-100 transform scale-x-100" 
                              x-transition:leave-end="opacity-0 transform scale-x-0" 
                              class="origin-left whitespace-nowrap text-orange-500">Volver a SICEFA</span>
                    </a>
                </li>

                @yield('sidebar-menu')
            </ul>
        </nav>
    </div>

    <!-- Área de Contenido Principal -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Barra de Navegación Superior (Top Navbar) -->
        <header class="flex items-center justify-between h-16 bg-gradient-to-r from-white to-gray-50 border-b border-gray-200 px-6 shadow-md z-30 backdrop-blur-sm">
            <div class="flex items-center space-x-4">
                <!-- Botón de hamburguesa para alternar la visibilidad del sidebar (visible siempre) -->
                <button @click="toggleSidebar()" class="text-gray-600 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 rounded-lg p-2 transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <!-- Icono de hamburguesa (visible por defecto, se oculta cuando el sidebar está abierto en desktop) -->
                        <path x-show="!sidebarOpen || !isDesktop" 
                              stroke-linecap="round" 
                              stroke-linejoin="round" 
                              stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16"
                              style="display: block;"></path>
                        <!-- Icono de X (oculto por defecto, se muestra cuando el sidebar está abierto en desktop) -->
                        <path x-show="sidebarOpen && isDesktop" 
                              x-cloak
                              stroke-linecap="round" 
                              stroke-linejoin="round" 
                              stroke-width="2" 
                              d="M6 18L18 6M6 6l12 12"
                              style="display: none;"></path>
                    </svg>
                </button>
                <!-- Eslogan -->
                <span class="text-2xl font-semibold text-gray-700 hidden md:block" style="font-family: 'Dancing Script', cursive;">
                    Control Preciso, Gestión Eficiente
                </span>
            </div>

            <!-- Menú de usuario y notificaciones en la barra superior -->
            <div class="flex items-center space-x-4">
                @yield('navbar-notifications')
                
                @auth
                <!-- Menú desplegable de usuario autenticado -->
                <div x-data="{ dropdownOpen: false }" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center space-x-2 text-gray-800 hover:text-gray-900 focus:outline-none focus:text-gray-900 p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <img src="{{ asset('AdminLTE/dist/img/user2-160x160.jpg') }}" class="h-8 w-8 rounded-full object-cover" alt="User Image">
                        <div>
                            <span class="font-semibold text-base block text-left">{{ Auth::user()->nickname ?? Auth::user()->name }}</span>
                            <span class="text-xs text-gray-500 block text-left">@yield('user-role', 'Usuario')</span>
                        </div>
                        <i class="fas fa-angle-down ml-1 text-sm"></i>
                    </button>

                    <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-40 border border-gray-100">
                        @yield('navbar-user-menu')
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
                        @yield('breadcrumbs')
                    </ol>
                </nav>
            </div>

            {{-- Aquí se renderizará el contenido específico de cada vista hija --}}
            @yield('content')
        </main>

        <!-- Pie de página del Área de Contenido Principal -->
        <footer class="bg-white border-t-2 border-gray-300 shadow-lg p-4 flex justify-between items-center text-gray-600 text-sm">
            <div>
                <span class="font-bold">&copy; {{ date('Y') }} INFRASTOCK</span>
                @hasSection('footer-role')
                    @yield('footer-role')
                @endif
                . Todos los derechos reservados.
            </div>
            <div>
                <span class="font-bold">Versión 1.0</span>
            </div>
        </footer>

    </div>

    <!-- SweetAlert2 JS para mostrar mensajes de éxito/error/información -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Función global para notificaciones - debe estar antes de Alpine.js -->
    <script>
        // Función global para marcar notificación como leída
        window.markNotificationAsRead = function(notificationId) {
            console.log('Marcando notificación como leída:', notificationId);
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token no encontrado');
                return;
            }
            
            fetch(`/infrastock/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
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
                    // Marcar como leída visualmente
                    let notificationElement = document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (!notificationElement) {
                        notificationElement = document.querySelector(`[onclick*="${notificationId}"]`);
                    }
                    if (notificationElement) {
                        notificationElement.classList.remove('bg-blue-50');
                        notificationElement.classList.add('opacity-75');
                        // Remover el indicador de no leída
                        const unreadIndicator = notificationElement.querySelector('.w-2.h-2.bg-blue-500');
                        if (unreadIndicator) {
                            unreadIndicator.remove();
                        }
                    }
                    // Actualizar contador
                    if (typeof updateNotificationCount === 'function') {
                        updateNotificationCount();
                    }
                    
                    // Si hay una URL de redirección, redirigir
                    if (data.redirect_url) {
                        console.log('Redirigiendo a:', data.redirect_url);
                        setTimeout(function() {
                            window.location.href = data.redirect_url;
                        }, 300);
                    } else {
                        console.log('No hay URL de redirección');
                    }
                } else {
                    console.error('Error al marcar notificación:', data.message);
                }
            })
            .catch(error => {
                console.error('Error al marcar notificación como leída:', error);
                alert('Error al marcar la notificación como leída. Por favor, intenta de nuevo.');
            });
        };
    </script>

    <!-- Alpine.js CDN para añadir reactividad y funcionalidad al HTML -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
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
    </script>

    <!-- jQuery (requerido para DataTables) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    {{-- Sección para scripts adicionales específicos de cada vista hija --}}
    @yield('script')

</body>

</html>
