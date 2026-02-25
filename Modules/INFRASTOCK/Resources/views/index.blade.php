<!--
    * @file index.blade.php
    * @brief Vista principal (landing page) del módulo INFRASTOCK.
    *
    * Esta vista es la página de aterrizaje del módulo INFRASTOCK, diseñada para ser responsive
    * utilizando Tailwind CSS. Presenta una interfaz moderna con un encabezado adaptable (navbar)
    * que cambia el texto del botón de "Inicia sesión" a "Administrador" dependiendo del estado
    * de autenticación del usuario. Incluye secciones descriptivas (Hero, Características, Acerca de)
    * y un pie de página.
    *
    * @param No aplica directamente, pero el contenido se adapta según el estado de autenticación de Laravel.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>INFRASTOCK - Sistema de Gestión de Infraestructura y Stock</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema integral de gestión de insumos, herramientas y materiales para el Centro de Formación Agroindustrial La Angostura">
    <!-- CDN de Tailwind CSS para estilos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Fuente de Google Fonts para un estilo más atractivo -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen bg-gray-50">

    <!-- HEADER / BANNER -->
    <!-- Encabezado principal y barra de navegación. Adaptable a pantallas móviles. -->
    <header class="bg-gradient-to-r from-green-800 to-green-700 text-gray-100 shadow-xl sticky top-0 z-50">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo de la aplicación INFRASTOCK -->
            <div class="flex items-center space-x-3">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo SICEFA" class="h-20 w-auto drop-shadow-lg brightness-110 contrast-110" onerror="this.style.display='none';">
                <div>
                    <span class="text-3xl font-extrabold tracking-tight block">INFRASTOCK</span>
                    <span class="text-sm text-green-200">Sistema de Gestión Integral</span>
                </div>
            </div>

            <!-- Botón de menú tipo hamburguesa para la navegación en dispositivos móviles -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-100 focus:outline-none">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-4 6h4"></path></svg>
                </button>
            </div>

            <!-- Navegación y Botón Login/Administrador (visible en escritorio) -->
            <div class="hidden md:flex items-center space-x-6">
                <nav class="flex space-x-6 text-lg font-medium">
                    <a href="{{ route('cefa.infrastock.index') }}" class="hover:text-green-200 transition-colors duration-300">Inicio</a>
                    <a href="#about" class="hover:text-green-200 transition-colors duration-300">Acerca de</a>
                    <a href="#features" class="hover:text-green-200 transition-colors duration-300">Funcionalidades</a>
                    <a href="#stats" class="hover:text-green-200 transition-colors duration-300">Estadísticas</a>
                    <a href="{{ route('cefa.infrastock.developers') }}" class="hover:text-green-200 transition-colors duration-300">Desarrolladores</a>
                </nav>

                <!-- Botón condicional: "Inicia sesión" para usuarios no autenticados o dashboard específico para autenticados -->
                <div>
                    @guest
                        <a class="bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg shadow-md transition-all duration-300 font-semibold" href="{{ route('login') }}?redirect_to={{ urlencode(route('infrastock.post-login')) }}">
                            <i class="fas fa-sign-in-alt mr-2"></i>Inicia sesión
                        </a>
                    @else
                        @php
                            $userRoles = Auth::user()->roles->pluck('name')->toArray();
                        @endphp
                        @if(in_array('Aseo', $userRoles))
                            <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('infrastock.cleaning-staff.dashboard') }}">Personal de Aseo</a>
                        @elseif(in_array('Operario', $userRoles))
                            <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('infrastock.operator.dashboard') }}">Operario</a>
                        @elseif(in_array('Centro de Convivencia', $userRoles))
                            <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('infrastock.convivencia.dashboard') }}">Centro de Convivencia</a>
                        @elseif(in_array('Ganadería', $userRoles))
                            <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('infrastock.ganaderia.dashboard') }}">Ganadería</a>
                        @elseif(in_array('Vigilancia', $userRoles))
                            <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('infrastock.vigilancia.dashboard') }}">Vigilancia</a>
                        @elseif(in_array('Agroindustria', $userRoles))
                            <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('infrastock.agroindustria.dashboard') }}">Agroindustria</a>
                        @elseif(in_array('Ciencias Basicas', $userRoles))
                            <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('infrastock.ciencias-basicas.dashboard') }}">Ciencias Basicas</a>
                        @elseif(in_array('Psicola', $userRoles))
                            <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('infrastock.psicola.dashboard') }}">Psicola</a>
                        @elseif(in_array('Instructor', $userRoles))
                            <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('infrastock.instructor.dashboard') }}">Instructor</a>
                        @else
                            <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('cefa.infrastock.admin.dashboard') }}">Administrador</a>
                        @endif
                    @endguest
                </div>
            </div>
        </div>

        <!-- Menú móvil (se muestra/oculta con el botón de hamburguesa) -->
        <div id="mobile-menu" class="hidden md:hidden bg-green-700 px-6 py-3">
            <nav class="flex flex-col space-y-3 text-lg font-medium">
                <a href="{{ route('cefa.infrastock.index') }}" class="block py-2 px-3 hover:bg-green-600 rounded-md transition-colors duration-300">Inicio</a>
                <a href="#about" class="block py-2 px-3 hover:bg-green-600 rounded-md transition-colors duration-300">Acerca de</a>
                <a href="#features" class="block py-2 px-3 hover:bg-green-600 rounded-md transition-colors duration-300">Funcionalidades</a>
                <a href="#stats" class="block py-2 px-3 hover:bg-green-600 rounded-md transition-colors duration-300">Estadísticas</a>
                <a href="{{ route('cefa.infrastock.developers') }}" class="block py-2 px-3 hover:bg-green-600 rounded-md transition-colors duration-300">Desarrolladores</a>
                <!-- Botón condicional para el menú móvil -->
                @guest
                    <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('login') }}?redirect_to={{ urlencode(route('infrastock.post-login')) }}">Inicia sesión</a>
                @else
                    @php
                        $userRoles = Auth::user()->roles->pluck('name')->toArray();
                    @endphp
                    @if(in_array('Aseo', $userRoles))
                        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('infrastock.cleaning-staff.dashboard') }}">Personal de Aseo</a>
                    @elseif(in_array('Operario', $userRoles))
                        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('infrastock.operator.dashboard') }}">Operario</a>
                    @elseif(in_array('Centro de Convivencia', $userRoles))
                        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('infrastock.convivencia.dashboard') }}">Centro de Convivencia</a>
                    @elseif(in_array('Ganadería', $userRoles))
                        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('infrastock.ganaderia.dashboard') }}">Ganadería</a>
                    @elseif(in_array('Vigilancia', $userRoles))
                        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('infrastock.vigilancia.dashboard') }}">Vigilancia</a>
                    @elseif(in_array('Agroindustria', $userRoles))
                        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('infrastock.agroindustria.dashboard') }}">Agroindustria</a>
                    @elseif(in_array('Ciencias Basicas', $userRoles))
                        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('infrastock.ciencias-basicas.dashboard') }}">Ciencias Basicas</a>
                    @elseif(in_array('Psicola', $userRoles))
                        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('infrastock.psicola.dashboard') }}">Psicola</a>
                    @elseif(in_array('Instructor', $userRoles))
                        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('infrastock.instructor.dashboard') }}">Instructor</a>
                    @else
                        <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('cefa.infrastock.admin.dashboard') }}">Administrador</a>
                    @endif
                @endguest
            </nav>
        </div>
    </header>

    <!-- HERO SECTION -->
    <!-- Sección principal que introduce el sistema con un título y descripción. -->
    <section class="bg-gradient-to-br from-green-50 via-white to-green-50 py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <!-- Contenido de texto de la sección Hero -->
                <div class="flex-1 text-center md:text-left">
                    <div class="inline-block mb-4 px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                        <i class="fas fa-check-circle mr-2"></i>Sistema Integral de Gestión
                    </div>
                    <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Control Preciso,<br>
                        <span class="text-green-600">Gestión Eficiente</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Bienvenido al <strong class="text-green-700">Sistema de Gestión de Infraestructura y Stock</strong> del 
                        Centro de Formación Agroindustrial "La Angostura". 
                        Optimiza el control de insumos, herramientas y materiales con reportes claros y ágiles.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        @guest
                            <a href="{{ route('login') }}?redirect_to={{ urlencode(route('infrastock.post-login')) }}"
                               class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-xl shadow-lg font-semibold transition-all duration-300 transform hover:scale-105 text-center">
                                <i class="fas fa-sign-in-alt mr-2"></i>Acceder al Sistema
                            </a>
                        @else
                            <a href="{{ route('cefa.infrastock.admin.dashboard') }}"
                               class="bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-xl shadow-lg font-semibold transition-all duration-300 transform hover:scale-105 text-center">
                                <i class="fas fa-tachometer-alt mr-2"></i>Ir al Dashboard
                            </a>
                        @endguest
                        <a href="#features"
                           class="bg-white hover:bg-gray-50 text-green-700 border-2 border-green-600 px-8 py-4 rounded-xl shadow-md font-semibold transition-all duration-300 text-center">
                            <i class="fas fa-info-circle mr-2"></i>Conocer más
                        </a>
                    </div>
                </div>

                <!-- Imagen ilustrativa para la sección Hero -->
                <div class="flex-1 flex justify-center">
                    <div class="relative">
                        <div class="absolute inset-0 bg-green-200 rounded-3xl transform rotate-6 opacity-20"></div>
                        <img src="https://img.freepik.com/free-vector/data-report-concept-illustration_114360-885.jpg"
                             alt="Gestión de Inventario" class="relative rounded-3xl shadow-2xl max-h-96 object-contain">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATISTICS SECTION -->
    <!-- Sección que muestra estadísticas del sistema -->
    <section id="stats" class="bg-white py-16 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Estadísticas del Sistema</h2>
                <p class="text-xl text-gray-600">Datos en tiempo real de nuestro inventario</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                <!-- Tarjeta de Insumos Totales -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-blue-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-boxes text-white text-2xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-blue-700 mb-2">{{ number_format($totalSupplies) }}</h3>
                        <p class="text-sm font-semibold text-blue-600">Insumos Registrados</p>
                    </div>
                </div>

                <!-- Tarjeta de Insumos Disponibles -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-green-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-check-circle text-white text-2xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-green-700 mb-2">{{ number_format($availableSupplies) }}</h3>
                        <p class="text-sm font-semibold text-green-600">Insumos Disponibles</p>
                    </div>
                </div>

                <!-- Tarjeta de Herramientas -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-purple-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tools text-white text-2xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-purple-700 mb-2">{{ number_format($totalTools) }}</h3>
                        <p class="text-sm font-semibold text-purple-600">Herramientas</p>
                    </div>
                </div>

                <!-- Tarjeta de Categorías -->
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-orange-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-tags text-white text-2xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-orange-700 mb-2">{{ number_format($totalCategories) }}</h3>
                        <p class="text-sm font-semibold text-orange-600">Categorías</p>
                    </div>
                </div>

                <!-- Tarjeta de Áreas -->
                <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-indigo-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-sitemap text-white text-2xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-indigo-700 mb-2">{{ number_format($totalAreas) }}</h3>
                        <p class="text-sm font-semibold text-indigo-600">Áreas Productivas</p>
                    </div>
                </div>

                <!-- Tarjeta de Usuarios -->
                <div class="bg-gradient-to-br from-teal-50 to-teal-100 p-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="text-center">
                        <div class="bg-teal-500 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-white text-2xl"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-teal-700 mb-2">{{ number_format($totalUsers) }}</h3>
                        <p class="text-sm font-semibold text-teal-600">Usuarios Activos</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <!-- Sección que destaca las características principales de INFRASTOCK. -->
    <section id="features" class="bg-gradient-to-b from-gray-50 to-white py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">¿Qué ofrece INFRASTOCK?</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Un sistema completo diseñado para optimizar la gestión de recursos de infraestructura
                </p>
            </div>

            <!-- Tarjetas de características con íconos y descripciones -->
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-boxes text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Gestión de Insumos</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Controla el inventario de herramientas y materiales de manera clara y ordenada. 
                        Registra, categoriza y monitorea todos tus insumos en un solo lugar.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-bell text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Alertas Inteligentes</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Recibe notificaciones automáticas sobre bajo stock, vencimientos de insumos críticos 
                        y movimientos importantes del inventario.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Reportes Dinámicos</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Genera reportes detallados y exporta datos en PDF o Excel para un análisis 
                        más profundo de tu inventario y consumo.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="bg-orange-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-hand-holding text-orange-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Préstamos y Devoluciones</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Gestiona préstamos de herramientas e insumos con seguimiento completo, 
                        control de fechas y estados de devolución.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-user-shield text-indigo-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Control de Accesos</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Sistema de roles y permisos que permite diferentes niveles de acceso según 
                        el perfil del usuario (Administrador, Operario, Personal de Aseo, etc.).
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="bg-teal-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-sitemap text-teal-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Áreas Productivas</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Organiza tu inventario por áreas productivas, facilitando la gestión 
                        y el seguimiento de recursos por departamento o sección.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Stock Mínimo</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Configura niveles mínimos de stock para cada insumo y recibe alertas 
                        cuando sea necesario realizar nuevas compras.
                    </p>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100">
                    <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-calendar-alt text-yellow-600 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Control de Vencimientos</h3>
                    <p class="text-gray-600 leading-relaxed">
                        Registra fechas de vencimiento y recibe notificaciones automáticas 
                        cuando los insumos estén próximos a vencer.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <!-- Sección que explica el propósito y los beneficios del sistema INFRASTOCK. -->
    <section id="about" class="bg-gradient-to-br from-green-50 to-white py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <!-- Imagen ilustrativa para la sección Acerca de -->
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-0 bg-green-200 rounded-3xl transform -rotate-6 opacity-20"></div>
                        <img src="https://img.freepik.com/free-vector/business-analytics-team-concept-illustration_114360-801.jpg"
                             alt="Acerca de INFRASTOCK" class="relative rounded-3xl shadow-2xl max-h-96 object-contain">
                    </div>
                </div>
                <!-- Contenido de texto de la sección Acerca de -->
                <div class="flex-1">
                    <div class="inline-block mb-4 px-4 py-2 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                        <i class="fas fa-info-circle mr-2"></i>Sobre el Sistema
                    </div>
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">¿Por qué INFRASTOCK?</h2>
                    <p class="text-lg text-gray-700 mb-6 leading-relaxed">
                        Este sistema está diseñado para garantizar un <strong>control eficiente</strong> de los recursos 
                        de infraestructura del Centro de Formación Agroindustrial "La Angostura", 
                        optimizando el tiempo y reduciendo pérdidas en la gestión de materiales.
                    </p>
                    <div class="space-y-4 mb-8">
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Trazabilidad Completa</h4>
                                <p class="text-gray-600">Seguimiento detallado de cada movimiento y consumo de insumos.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Interfaz Intuitiva</h4>
                                <p class="text-gray-600">Diseño moderno y fácil de usar para todos los niveles de usuario.</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 text-xl mr-3 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Tiempo Real</h4>
                                <p class="text-gray-600">Actualización instantánea de inventarios y disponibilidad de recursos.</p>
                            </div>
                        </div>
                    </div>
                    @guest
                        <a href="{{ route('login') }}?redirect_to={{ urlencode(route('infrastock.post-login')) }}"
                           class="inline-block bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-xl shadow-lg font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-rocket mr-2"></i>Acceder al Sistema
                        </a>
                    @else
                        <a href="{{ route('cefa.infrastock.admin.dashboard') }}"
                           class="inline-block bg-green-600 hover:bg-green-700 text-white px-8 py-4 rounded-xl shadow-lg font-semibold transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-tachometer-alt mr-2"></i>Ir al Dashboard
                        </a>
                    @endguest
                </div>
            </div>
        </div>
    </section>

    <!-- ROLES SECTION -->
    <!-- Sección que muestra los diferentes roles del sistema -->
    <section class="bg-white py-16 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Perfiles de Usuario</h2>
                <p class="text-xl text-gray-600">Diferentes roles para diferentes necesidades</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-xl border border-green-200">
                    <div class="text-center">
                        <i class="fas fa-user-shield text-green-600 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Administrador</h3>
                        <p class="text-gray-600 text-sm">Control total del sistema, gestión de usuarios, aprobación de solicitudes y configuración general.</p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-xl border border-blue-200">
                    <div class="text-center">
                        <i class="fas fa-user-cog text-blue-600 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Operario</h3>
                        <p class="text-gray-600 text-sm">Visualización de stock, creación de solicitudes de insumos y reporte de sobrantes.</p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-6 rounded-xl border border-purple-200">
                    <div class="text-center">
                        <i class="fas fa-broom text-purple-600 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Personal de Aseo</h3>
                        <p class="text-gray-600 text-sm">Solicitud de insumos de limpieza y gestión de materiales de aseo.</p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-6 rounded-xl border border-orange-200">
                    <div class="text-center">
                        <i class="fas fa-chalkboard-teacher text-orange-600 text-4xl mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Instructor</h3>
                        <p class="text-gray-600 text-sm">Préstamo de herramientas para actividades académicas y talleres.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <!-- Pie de página con información de derechos de autor y enlaces útiles. -->
    <footer class="bg-gradient-to-r from-green-800 to-green-700 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">INFRASTOCK</h3>
                    <p class="text-green-100 mb-4">
                        Sistema de Gestión de Infraestructura y Stock del Centro de Formación Agroindustrial "La Angostura".
                    </p>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('cefa.infrastock.index') }}" class="text-green-200 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="#about" class="text-green-200 hover:text-white transition-colors">Acerca de</a></li>
                        <li><a href="#features" class="text-green-200 hover:text-white transition-colors">Funcionalidades</a></li>
                        <li><a href="#stats" class="text-green-200 hover:text-white transition-colors">Estadísticas</a></li>
                        <li><a href="{{ route('cefa.infrastock.developers') }}" class="text-green-200 hover:text-white transition-colors">Desarrolladores</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Contacto</h3>
                    <p class="text-green-100 mb-2">
                        <i class="fas fa-building mr-2"></i>Centro de Formación Agroindustrial "La Angostura"
                    </p>
                    <p class="text-green-100 mb-2">
                        <i class="fas fa-globe mr-2"></i>
                        <a href="https://centroagroindustrial.blogspot.com/" target="_blank" class="hover:text-white transition-colors">
                            centroagroindustrial.blogspot.com
                        </a>
                    </p>
                </div>
            </div>
            <div class="border-t border-green-600 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-green-200 mb-4 md:mb-0">
                    Copyright &copy; {{ date('Y') }} 
                    <a target="_blank" href="https://centroagroindustrial.blogspot.com/" class="hover:text-white transition-colors">
                        Centro de Formación Agroindustrial "La Angostura"
                    </a>. 
                    Todos los derechos reservados.
                </p>
                <div class="text-green-200">
                    <span class="font-semibold">ADSO</span> - Análisis y Desarrollo de Software
                </div>
            </div>
        </div>
    </footer>

    <!-- CONTENEDOR FIJO -->
    <div class="fixed bottom-6 right-6 z-50">
        
        <!-- OPCIONES (posición absoluta encima del botón) -->
        <div id="manual-options" class="absolute bottom-16 right-0 hidden space-y-2">
            <a href="{{ asset('modules/infrastock/manuales/Manual_Administrador.pdf') }}" target="_blank" class="block bg-green-700 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 text-sm whitespace-nowrap">Manual Administrador</a>
            <a href="{{ asset('modules/infrastock/manuales/Manual_Area_Solicitante.pdf') }}" target="_blank" class="block bg-green-700 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 text-sm whitespace-nowrap">Manual Área Solicitante</a>
            <a href="{{ asset('modules/infrastock/manuales/Manual_Instructor.pdf') }}" target="_blank" class="block bg-green-700 text-white px-4 py-2 rounded-lg shadow-md hover:bg-green-600 transition duration-300 text-sm whitespace-nowrap">Manual Instructor</a>
        </div>

        <!-- BOTÓN PRINCIPAL (ya no se mueve) -->
        <button onclick="toggleManuals()" title="Manuales" class="bg-green-700 hover:bg-green-600 text-white w-14 h-14 rounded-full flex items-center justify-center shadow-lg transition duration-300">
            <img src="{{ asset('assets/img/soporte.png') }}" alt="Manuales" class="w-6 h-6">
        </button>
    </div>

    <!-- Script JavaScript para la funcionalidad del menú móvil -->
    <script>
        document.getElementById('mobile-menu-button').onclick = function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        };

        function toggleManuals() {
            document.getElementById('manual-options').classList.toggle('hidden');
        }

        // Smooth scroll para los enlaces de navegación
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

</body>
</html>
