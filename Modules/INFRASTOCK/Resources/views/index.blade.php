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
    <title>INFRASTOCK - Inicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CDN de Tailwind CSS para estilos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fuente de Google Fonts para un estilo más atractivo -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
</head>

<body class="flex flex-col min-h-screen bg-gray-50">

    <!-- HEADER / BANNER -->
    <!-- Encabezado principal y barra de navegación. Adaptable a pantallas móviles. -->
    <header class="bg-green-800 text-gray-100 shadow-xl">
        <div class="max-w-7xl mx-auto flex justify-between items-center py-4 px-6">
            <!-- Logo de la aplicación INFRASTOCK -->
            <div class="flex items-center space-x-2">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-14 w-auto">
                <span class="text-3xl font-extrabold tracking-tight">INFRASTOCK</span>
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
                    <a href="#contact" class="hover:text-green-200 transition-colors duration-300">Contacto</a>
                </nav>
                <!-- Botón condicional: "Inicia sesión" para usuarios no autenticados o "Administrador" para autenticados -->
                <div>
                    @guest
                        <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('login') }}">Inicia sesión</a>
                    @else
                        <a class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg shadow-md transition-colors duration-300" href="{{ route('cefa.infrastock.admin.dashboard') }}">Administrador</a>
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
                <a href="#contact" class="block py-2 px-3 hover:bg-green-600 rounded-md transition-colors duration-300">Contacto</a>
                <!-- Botón condicional para el menú móvil -->
                @guest
                    <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('login') }}">Inicia sesión</a>
                @else
                    <a class="block bg-green-600 hover:bg-green-700 text-white px-3 py-2 rounded-md text-center transition-colors duration-300" href="{{ route('cefa.infrastock.admin.dashboard') }}">Administrador</a>
                @endguest
            </nav>
        </div>
    </header>

    <!-- HERO SECTION -->
    <!-- Sección principal que introduce el sistema con un título y descripción. -->
    <section class="bg-green-100 flex-1">
        <div class="max-w-7xl mx-auto px-6 py-16 flex flex-col md:flex-row items-center gap-10">
            <!-- Contenido de texto de la sección Hero -->
            <div class="flex-1">
                <h1 class="text-4xl md:text-5xl font-bold text-green-700 mb-4">
                    Control Preciso, Gestión Eficiente
                </h1>
                <p class="text-gray-700 mb-6 text-lg">
                    Bienvenido al sistema de gestión de <span class="font-semibold">Infraestructura y Stock</span>. 
                    Optimiza el control de insumos, herramientas y materiales con reportes claros y ágiles.
                </p>
                <a href="{{ route('login') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl shadow-lg font-semibold transition">
                    Ir al Login
                </a>
            </div>

            <!-- Imagen ilustrativa para la sección Hero -->
            <div class="flex-1 flex justify-center">
                <img src="https://img.freepik.com/free-vector/data-report-concept-illustration_114360-885.jpg"
                     alt="Gestión" class="rounded-2xl shadow-lg max-h-96 object-contain">
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <!-- Sección que destaca las características principales de INFRASTOCK. -->
    <section id="features" class="bg-white py-16">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <h2 class="text-3xl font-bold text-green-700 mb-12">¿Qué ofrece INFRASTOCK?</h2>

            <!-- Tarjetas de características con íconos y descripciones -->
            <div class="grid md:grid-cols-3 gap-8">
                <div class="p-6 border rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold text-green-600 mb-2">Gestión de Insumos</h3>
                    <p class="text-gray-600">Controla el inventario de herramientas y materiales de manera clara y ordenada.</p>
                </div>
                <div class="p-6 border rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold text-green-600 mb-2">Alertas Inteligentes</h3>
                    <p class="text-gray-600">Recibe notificaciones sobre bajo stock y vencimientos de insumos críticos.</p>
                </div>
                <div class="p-6 border rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold text-green-600 mb-2">Reportes Dinámicos</h3>
                    <p class="text-gray-600">Exporta datos en PDF o Excel para un análisis más detallado.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <!-- Sección que explica el propósito y los beneficios del sistema INFRASTOCK. -->
    <section id="about" class="bg-green-50 py-16">
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center gap-10">
            <!-- Imagen ilustrativa para la sección Acerca de -->
            <div class="flex-1">
                <img src="https://img.freepik.com/free-vector/business-analytics-team-concept-illustration_114360-801.jpg"
                     alt="Acerca de" class="rounded-2xl shadow-lg max-h-80 object-contain">
            </div>
            <!-- Contenido de texto de la sección Acerca de -->
            <div class="flex-1">
                <h2 class="text-3xl font-bold text-green-700 mb-4">¿Por qué INFRASTOCK?</h2>
                <p class="text-gray-700 text-lg mb-6">
                    Este sistema está diseñado para garantizar un control eficiente de los recursos de infraestructura, 
                    optimizando el tiempo y reduciendo pérdidas en la gestión de materiales.
                </p>
                <a href="{{ route('cefa.infrastock.admin.dashboard') }}"
                   class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl shadow-md font-semibold transition">
                    Acceder al Sistema
                </a>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <!-- Pie de página con información de derechos de autor y enlaces útiles. -->
    <footer class="bg-green-700 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-8 flex flex-col md:flex-row justify-between items-center">
            <strong>Copyright &copy; 2025 <a target="blank" href="https://centroagroindustrial.blogspot.com/">Centro de Formación Agroindustrial "La Angostura</a>. All rights reserved.</strong>
                <div class="float-right d-none d-sm-inline"> ADSO </div>
            </div>
        </div>
    </footer>

    <!-- Script JavaScript para la funcionalidad del menú móvil -->
    <script>
        document.getElementById('mobile-menu-button').onclick = function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        };
    </script>

</body>
</html>
