<!--
    * @file login.blade.php
    * @brief Vista estática del panel de administración dentro del módulo INFRASTOCK.
    *
    * Esta vista Blade presenta una estructura básica para un panel de administración,
    * incluyendo una barra lateral (sidebar), una cabecera (header) y un área de contenido principal.
    * Está diseñada utilizando Tailwind CSS para un estilo moderno y responsivo. Aunque el nombre
    * del archivo sugiere "login", su contenido actual simula un dashboard estático con tarjetas
    * de funcionalidades y elementos de navegación.
    *
    * @param string $title Título de la página que se muestra en la pestaña del navegador.
    * @param string $logo_path Ruta al archivo de imagen del logo del módulo INFRASTOCK.
    * @param string $admin_name Nombre del usuario administrador logueado (o 'Administrador' por defecto).
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>INFRASTOCK - Panel de Administración</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&display=swap" rel="stylesheet">
</head>

<body class="flex h-screen bg-gray-100">

    <!-- Barra Lateral (Sidebar) -->
    <aside class="w-64 bg-green-700 text-white flex flex-col">
        <!-- Contenedor del Logo y Nombre del Módulo -->
        <div class="flex items-center space-x-2 px-6 py-4 border-b border-green-600">
            <img src="{{ assets('images/images.png') }}" alt="Logo" class="h-10 w-auto">
            <span class="text-xl font-bold">INFRASTOCK</span>
        </div>

        <!-- Menú de Navegación de la Barra Lateral -->
        <nav class="flex-1 px-4 py-6 space-y-4">
            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-green-600">📂 Categorías</a>
            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-green-600">🏭 Áreas Productivas</a>
            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-green-600">🛠 Herramientas</a>
            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-green-600">📦 Insumos</a>
            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-green-600">📊 Reportes</a>
            <a href="#" class="block px-3 py-2 rounded-lg hover:bg-green-600">⚙️ Configuración</a>
        </nav>

        <!-- Sección de Cierre de Sesión -->
        <div class="px-6 py-4 border-t border-green-600">
            <a href="{{ route('logout') }}"
               class="block w-full text-center bg-white text-green-700 font-semibold py-2 rounded-lg shadow hover:bg-green-100 transition">
                Cerrar Sesión
            </a>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <div class="flex-1 flex flex-col">

        <!-- Cabecera (Header) del Contenido Principal -->
        <header class="bg-white shadow flex justify-between items-center px-6 py-4">
            <h1 class="text-2xl font-bold text-green-700">Panel de Administración</h1>

            <!-- Información y Perfil del Administrador -->
            <div class="flex items-center space-x-3">
                <span class="text-gray-700 font-medium">👤 {{ Auth::user()->name ?? 'Administrador' }}</span>
                <img src="https://ui-avatars.com/api/?name=Admin"
                     alt="Admin" class="h-10 w-10 rounded-full border">
            </div>
        </header>

        <!-- Cuerpo del Contenido Principal (Main) -->
        <main class="flex-1 p-6 bg-gray-50 overflow-y-auto">

            <!-- Contenedor de Tarjetas de Funcionalidades -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Tarjeta de Categorías -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold text-green-600 mb-2">📂 Categorías</h3>
                    <p class="text-gray-600 mb-4">Administra las categorías de insumos y herramientas.</p>
                    <a href="#" class="text-green-600 font-semibold hover:underline">Gestionar →</a>
                </div>

                <!-- Tarjeta de Áreas Productivas -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold text-green-600 mb-2">🏭 Áreas Productivas</h3>
                    <p class="text-gray-600 mb-4">Configura y controla las áreas donde se usan los insumos.</p>
                    <a href="#" class="text-green-600 font-semibold hover:underline">Gestionar →</a>
                </div>

                <!-- Tarjeta de Herramientas -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold text-green-600 mb-2">🛠 Herramientas</h3>
                    <p class="text-gray-600 mb-4">Controla las herramientas disponibles y en uso.</p>
                    <a href="#" class="text-green-600 font-semibold hover:underline">Gestionar →</a>
                </div>

                <!-- Tarjeta de Insumos -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold text-green-600 mb-2">📦 Insumos</h3>
                    <p class="text-gray-600 mb-4">Gestiona insumos, stock y alertas de vencimiento.</p>
                    <a href="#" class="text-green-600 font-semibold hover:underline">Gestionar →</a>
                </div>

                <!-- Tarjeta de Reportes -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold text-green-600 mb-2">📊 Reportes</h3>
                    <p class="text-gray-600 mb-4">Exporta información en PDF y Excel para análisis detallado.</p>
                    <a href="#" class="text-green-600 font-semibold hover:underline">Ver Reportes →</a>
                </div>

                <!-- Tarjeta de Configuración -->
                <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
                    <h3 class="text-xl font-semibold text-green-600 mb-2">⚙️ Configuración</h3>
                    <p class="text-gray-600 mb-4">Ajusta los parámetros generales del sistema.</p>
                    <a href="#" class="text-green-600 font-semibold hover:underline">Configurar →</a>
                </div>

            </div>
        </main>

        <!-- Pie de Página (Footer) -->
        <footer class="bg-green-700 text-white py-4 text-center">
            <p class="text-sm">&copy; {{ date('Y') }} INFRASTOCK - Todos los derechos reservados</p>
        </footer>
    </div>

</body>

</html>
