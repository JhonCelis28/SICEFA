<!--
    * @file login.blade.php
    * @brief Vista de login para el Personal de Aseo del módulo INFRASTOCK.
    *
    * Esta vista presenta un formulario de login específico para el personal de aseo,
    * con diseño moderno y branding del módulo INFRASTOCK.
    * Utiliza Tailwind CSS para un diseño responsive y profesional.
    *
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Personal de Aseo INFRASTOCK</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full space-y-8">
        
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-broom text-green-600 text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Personal de Aseo</h2>
            <p class="text-green-100 text-lg">Sistema INFRASTOCK</p>
            <p class="text-green-200 text-sm mt-2">Inicia sesión para acceder a tu dashboard</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-xl shadow-2xl p-8">
            <form class="space-y-6" method="POST" action="{{ route('infrastock.cleaning-staff.login.post') }}" id="loginForm">
                @csrf
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-green-600"></i>
                        Correo Electrónico <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror"
                           placeholder="tu.email@ejemplo.com"
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-green-600"></i>
                        Contraseña <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="password" 
                               required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror"
                               placeholder="Tu contraseña">
                        <button type="button" 
                                onclick="togglePassword()" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i id="password-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" 
                               name="remember" 
                               type="checkbox" 
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Recordar sesión
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="#" class="font-medium text-green-600 hover:text-green-500">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Iniciar Sesión
                    </button>
                </div>

                <!-- Additional Links -->
                <div class="text-center space-y-2">
                    <p class="text-sm text-gray-600">
                        ¿No tienes cuenta? 
                        <a href="{{ route('infrastock.cleaning-staff.register') }}" class="font-medium text-green-600 hover:text-green-500">
                            Regístrate aquí
                        </a>
                    </p>
                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-xs text-gray-500">
                            <i class="fas fa-shield-alt mr-1"></i>
                            Acceso seguro y protegido
                        </p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-green-200 text-sm">
                <i class="fas fa-info-circle mr-1"></i>
                Si tienes problemas para acceder, contacta al administrador del sistema
            </p>
            <div class="mt-4 flex justify-center space-x-4">
                <a href="{{ route('cefa.home') }}" class="text-green-200 hover:text-white text-sm">
                    <i class="fas fa-home mr-1"></i>
                    Página Principal
                </a>
                <a href="{{ route('cefa.infrastock.index') }}" class="text-green-200 hover:text-white text-sm">
                    <i class="fas fa-cog mr-1"></i>
                    Panel Administrativo
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Auto-focus en el campo de email
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });

        // Mostrar mensajes de error si existen
        @if($errors->any())
            setTimeout(function() {
                const errorMessages = document.querySelectorAll('.text-red-500');
                errorMessages.forEach(function(error) {
                    error.style.display = 'block';
                });
            }, 100);
        @endif
    </script>
</body>
</html>
