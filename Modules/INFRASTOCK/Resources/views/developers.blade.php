<!--
    * @file developers.blade.php
    * @brief Vista de desarrolladores del módulo INFRASTOCK.
    *
    * Esta vista muestra información sobre el equipo de desarrollo y las herramientas utilizadas
    * en la construcción del sistema INFRASTOCK.
    *
    * @author Equipo de Desarrollo INFRASTOCK
    * @date 2025
-->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Desarrolladores - INFRASTOCK</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Equipo de desarrollo y herramientas utilizadas en el sistema INFRASTOCK">
    <!-- CDN de Tailwind CSS para estilos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome para íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Fuente de Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="flex flex-col min-h-screen bg-gray-50">

    <!-- HEADER / BANNER -->
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
                    <a href="{{ route('cefa.infrastock.index') }}#about" class="hover:text-green-200 transition-colors duration-300">Acerca de</a>
                    <a href="{{ route('cefa.infrastock.index') }}#features" class="hover:text-green-200 transition-colors duration-300">Funcionalidades</a>
                    <a href="{{ route('cefa.infrastock.index') }}#stats" class="hover:text-green-200 transition-colors duration-300">Estadísticas</a>
                    <a href="{{ route('cefa.infrastock.developers') }}" class="text-green-200 font-semibold border-b-2 border-green-200 pb-1">Desarrolladores</a>
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
                <a href="{{ route('cefa.infrastock.index') }}#about" class="block py-2 px-3 hover:bg-green-600 rounded-md transition-colors duration-300">Acerca de</a>
                <a href="{{ route('cefa.infrastock.index') }}#features" class="block py-2 px-3 hover:bg-green-600 rounded-md transition-colors duration-300">Funcionalidades</a>
                <a href="{{ route('cefa.infrastock.index') }}#stats" class="block py-2 px-3 hover:bg-green-600 rounded-md transition-colors duration-300">Estadísticas</a>
                <a href="{{ route('cefa.infrastock.developers') }}" class="block py-2 px-3 hover:bg-green-600 rounded-md transition-colors duration-300 bg-green-600">Desarrolladores</a>
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
    <section class="bg-gradient-to-br from-green-50 via-white to-green-50 py-16">
        <div class="max-w-7xl mx-auto px-6 text-center">
            <div class="inline-block mb-6 px-6 py-3 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                <i class="fas fa-code mr-2"></i>Equipo de Desarrollo
            </div>
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                Conoce al <span class="text-green-600">Equipo</span>
            </h1>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                El sistema INFRASTOCK fue desarrollado por un equipo comprometido de aprendices 
                del programa ADSO (Análisis y Desarrollo de Software) del Centro de Formación Agroindustrial "La Angostura".
            </p>
        </div>
    </section>

    <!-- TEAM SECTION -->
    <section class="bg-white py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Nuestro Equipo</h2>
                <p class="text-xl text-gray-600">Los desarrolladores detrás de INFRASTOCK</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Desarrollador 1 -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-blue-200">
                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full mx-auto mb-6 shadow-lg overflow-hidden border-4 border-blue-500 bg-blue-500 flex items-center justify-center relative">
                            <img src="{{ asset('modules/infrastock/images/developers/desarrollador1.jpg') }}" 
                                 alt="Desarrollador 1" 
                                 class="w-full h-full"
                                 style="object-fit: cover; object-position: center center; width: 100%; height: 100%; display: block;"
                                 onerror="this.onerror=null; this.src='{{ asset('modules/infrastock/images/developers/desarrollador1.png') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='flex';};">
                            <div class="absolute inset-0 w-full h-full flex items-center justify-center hidden bg-blue-500" style="display: none;">
                                <i class="fas fa-user-tie text-white text-4xl"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Desarrollador 1</h3>
                        <p class="text-blue-600 font-semibold mb-4">Full Stack Developer</p>
                        <p class="text-gray-600 text-sm mb-4">
                            Especializado en desarrollo backend y arquitectura de sistemas.
                        </p>
                        <div class="flex justify-center space-x-4">
                            <a href="#" class="text-blue-600 hover:text-blue-800 transition-colors">
                                <i class="fab fa-github text-2xl"></i>
                            </a>
                            <a href="#" class="text-blue-600 hover:text-blue-800 transition-colors">
                                <i class="fab fa-linkedin text-2xl"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Desarrollador 2 -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-green-200">
                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full mx-auto mb-6 shadow-lg overflow-hidden border-4 border-green-500 bg-green-500 flex items-center justify-center relative">
                            <img src="{{ asset('modules/infrastock/images/developers/desarrollador2.jpg') }}" 
                                 alt="Desarrollador 2" 
                                 class="w-full h-full"
                                 style="object-fit: cover; object-position: center center; width: 100%; height: 100%; display: block;"
                                 onerror="this.onerror=null; this.src='{{ asset('modules/infrastock/images/developers/desarrollador2.png') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='flex';};">
                            <div class="absolute inset-0 w-full h-full items-center justify-center hidden bg-blue-500" style="display: none;">
                                <i class="fas fa-user-tie text-white text-4xl"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Desarrollador 2</h3>
                        <p class="text-green-600 font-semibold mb-4">Frontend Developer</p>
                        <p class="text-gray-600 text-sm mb-4">
                            Experto en diseño de interfaces y experiencia de usuario.
                        </p>
                        <div class="flex justify-center space-x-4">
                            <a href="#" class="text-green-600 hover:text-green-800 transition-colors">
                                <i class="fab fa-github text-2xl"></i>
                            </a>
                            <a href="#" class="text-green-600 hover:text-green-800 transition-colors">
                                <i class="fab fa-linkedin text-2xl"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Desarrollador 3 -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-purple-200">
                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full mx-auto mb-6 shadow-lg overflow-hidden border-4 border-purple-500 bg-purple-500 flex items-center justify-center relative">
                            <img src="{{ asset('modules/infrastock/images/developers/desarrollador3.jpg') }}" 
                                 alt="Desarrollador 3" 
                                 class="w-full h-full object-contain object-center"
                                 style="object-fit: contain;"
                                 onerror="this.onerror=null; this.src='{{ asset('modules/infrastock/images/developers/desarrollador3.png') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='flex';};">
                            <div class="absolute inset-0 w-full h-full items-center justify-center hidden" style="display: none;">
                                <i class="fas fa-user-tie text-white text-4xl"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Desarrollador 3</h3>
                        <p class="text-purple-600 font-semibold mb-4">Backend Developer</p>
                        <p class="text-gray-600 text-sm mb-4">
                            Especialista en bases de datos y lógica de negocio.
                        </p>
                        <div class="flex justify-center space-x-4">
                            <a href="#" class="text-purple-600 hover:text-purple-800 transition-colors">
                                <i class="fab fa-github text-2xl"></i>
                            </a>
                            <a href="#" class="text-purple-600 hover:text-purple-800 transition-colors">
                                <i class="fab fa-linkedin text-2xl"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Desarrollador 4 -->
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-orange-200">
                    <div class="text-center">
                        <div class="w-24 h-24 rounded-full mx-auto mb-6 shadow-lg overflow-hidden border-4 border-orange-500 bg-orange-500 flex items-center justify-center relative">
                            <img src="{{ asset('modules/infrastock/images/developers/desarrollador4.jpg') }}" 
                                 alt="Desarrollador 4" 
                                 class="w-full h-full object-contain object-center"
                                 style="object-fit: contain;"
                                 onerror="this.onerror=null; this.src='{{ asset('modules/infrastock/images/developers/desarrollador4.png') }}'; this.onerror=function(){this.style.display='none'; this.nextElementSibling.style.display='flex';};">
                            <div class="absolute inset-0 w-full h-full items-center justify-center hidden" style="display: none;">
                                <i class="fas fa-user-tie text-white text-4xl"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Desarrollador 4</h3>
                        <p class="text-orange-600 font-semibold mb-4">Full Stack Developer</p>
                        <p class="text-gray-600 text-sm mb-4">
                            Desarrollador versátil con experiencia en múltiples tecnologías.
                        </p>
                        <div class="flex justify-center space-x-4">
                            <a href="#" class="text-orange-600 hover:text-orange-800 transition-colors">
                                <i class="fab fa-github text-2xl"></i>
                            </a>
                            <a href="#" class="text-orange-600 hover:text-orange-800 transition-colors">
                                <i class="fab fa-linkedin text-2xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- TECHNOLOGIES SECTION -->
    <section class="bg-gradient-to-b from-gray-50 to-white py-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Tecnologías Utilizadas</h2>
                <p class="text-xl text-gray-600">Stack tecnológico y herramientas que hicieron posible INFRASTOCK</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <!-- Backend -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="text-center">
                        <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-laravel text-red-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Laravel</h3>
                        <p class="text-gray-600 text-sm">Framework PHP para desarrollo backend robusto y escalable.</p>
                    </div>
                </div>

                <!-- Frontend -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-js text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">JavaScript</h3>
                        <p class="text-gray-600 text-sm">Lenguaje de programación para interactividad y dinamismo.</p>
                    </div>
                </div>

                <!-- CSS Framework -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="text-center">
                        <div class="bg-cyan-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-css3-alt text-cyan-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Tailwind CSS</h3>
                        <p class="text-gray-600 text-sm">Framework CSS utility-first para diseño moderno y responsive.</p>
                    </div>
                </div>

                <!-- Database -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-database text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">MySQL</h3>
                        <p class="text-gray-600 text-sm">Sistema de gestión de bases de datos relacionales.</p>
                    </div>
                </div>

                <!-- Version Control -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="text-center">
                        <div class="bg-gray-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-git-alt text-white text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Git & GitHub</h3>
                        <p class="text-gray-600 text-sm">Control de versiones y colaboración en el desarrollo.</p>
                    </div>
                </div>

                <!-- Icons -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="text-center">
                        <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fab fa-font-awesome text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Font Awesome</h3>
                        <p class="text-gray-600 text-sm">Biblioteca de iconos vectoriales para interfaces modernas.</p>
                    </div>
                </div>

                <!-- Charts -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="text-center">
                        <div class="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-bar text-yellow-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">DataTables</h3>
                        <p class="text-gray-600 text-sm">Plugin jQuery para tablas interactivas y funcionales.</p>
                    </div>
                </div>

                <!-- Alerts -->
                <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200">
                    <div class="text-center">
                        <div class="bg-pink-100 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-bell text-pink-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">SweetAlert2</h3>
                        <p class="text-gray-600 text-sm">Biblioteca para alertas y notificaciones elegantes.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ACKNOWLEDGMENTS SECTION -->
    <section class="bg-white py-20 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Agradecimientos</h2>
                <p class="text-xl text-gray-600">A todas las personas e instituciones que hicieron posible este proyecto</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <!-- Agradecimientos Institucionales -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-8 rounded-2xl shadow-lg border border-green-200">
                    <div class="flex items-center mb-6">
                        <div class="bg-green-500 w-16 h-16 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-university text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Institucionales</h3>
                    </div>
                    <ul class="space-y-4 text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                            <div>
                                <strong>Centro de Formación Agroindustrial "La Angostura"</strong>
                                <p class="text-sm">Por brindar las herramientas y el espacio para el desarrollo de este proyecto.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                            <div>
                                <strong>Programa ADSO</strong>
                                <p class="text-sm">Análisis y Desarrollo de Software - Por la formación técnica y profesional.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-600 mr-3 mt-1"></i>
                            <div>
                                <strong>Instructores y Coordinadores</strong>
                                <p class="text-sm">Por su guía, apoyo y conocimientos compartidos durante el desarrollo.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Agradecimientos Técnicos -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-8 rounded-2xl shadow-lg border border-blue-200">
                    <div class="flex items-center mb-6">
                        <div class="bg-blue-500 w-16 h-16 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-code text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900">Técnicos</h3>
                    </div>
                    <ul class="space-y-4 text-gray-700">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                            <div>
                                <strong>Comunidad Laravel</strong>
                                <p class="text-sm">Por el excelente framework y la documentación completa.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                            <div>
                                <strong>Tailwind CSS</strong>
                                <p class="text-sm">Por facilitar el diseño responsive y moderno de la interfaz.</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-blue-600 mr-3 mt-1"></i>
                            <div>
                                <strong>Comunidad Open Source</strong>
                                <p class="text-sm">Por todas las librerías y herramientas de código abierto utilizadas.</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- PROJECT INFO SECTION -->
    <section class="bg-gradient-to-br from-gray-50 to-white py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-200">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Información del Proyecto</h2>
                </div>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="bg-green-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-calendar-alt text-green-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Año de Desarrollo</h3>
                        <p class="text-gray-600">2025</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-blue-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-graduation-cap text-blue-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Programa</h3>
                        <p class="text-gray-600">ADSO - Análisis y Desarrollo de Software</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-purple-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-purple-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Equipo</h3>
                        <p class="text-gray-600">4 Desarrolladores</p>
                    </div>
                    <div class="text-center">
                        <div class="bg-orange-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-id-card text-orange-600 text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Ficha</h3>
                        <p class="text-gray-600">2995585</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-gradient-to-r from-green-800 to-green-700 text-white mt-auto">
        <div class="max-w-7xl mx-auto px-6 py-12">
            <div class="grid md:grid-cols-3 gap-8 mb-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">INFRASTOCK</h3>
                    <p class="text-green-100 mb-4">
                        Sistema de Gestión de Infraestructura y Stock del Centro de Formación Agroindustrial "La Angostura".
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-green-200 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-2xl"></i>
                        </a>
                        <a href="#" class="text-green-200 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-2xl"></i>
                        </a>
                        <a href="#" class="text-green-200 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-2xl"></i>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-xl font-bold mb-4">Enlaces Rápidos</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('cefa.infrastock.index') }}" class="text-green-200 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="{{ route('cefa.infrastock.index') }}#about" class="text-green-200 hover:text-white transition-colors">Acerca de</a></li>
                        <li><a href="{{ route('cefa.infrastock.index') }}#features" class="text-green-200 hover:text-white transition-colors">Funcionalidades</a></li>
                        <li><a href="{{ route('cefa.infrastock.index') }}#stats" class="text-green-200 hover:text-white transition-colors">Estadísticas</a></li>
                        <li><a href="{{ route('cefa.infrastock.developers') }}" class="text-green-200 hover:text-white transition-colors font-semibold">Desarrolladores</a></li>
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

    <!-- Script JavaScript para la funcionalidad del menú móvil -->
    <script>
        document.getElementById('mobile-menu-button').onclick = function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        };
    </script>

</body>
</html>
