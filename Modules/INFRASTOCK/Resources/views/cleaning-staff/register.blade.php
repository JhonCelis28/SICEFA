<!--
    * @file register.blade.php
    * @brief Vista de registro para el Personal de Aseo del módulo INFRASTOCK.
    *
    * Esta vista presenta un formulario de registro específico para el personal de aseo,
    * con validación del lado del cliente y diseño moderno.
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
    <title>Registro - Personal de Aseo INFRASTOCK</title>
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
    
    <div class="max-w-2xl w-full space-y-8">
        
        <!-- Header -->
        <div class="text-center">
            <div class="mx-auto h-20 w-20 bg-white rounded-full flex items-center justify-center shadow-lg mb-4">
                <i class="fas fa-broom text-green-600 text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-white mb-2">Registro de Personal de Aseo</h2>
            <p class="text-green-100 text-lg">Sistema INFRASTOCK</p>
            <p class="text-green-200 text-sm mt-2">Completa el formulario para crear tu cuenta</p>
        </div>

        <!-- Registration Form -->
        <div class="bg-white rounded-xl shadow-2xl p-8">
            <form class="space-y-6" method="POST" action="{{ route('infrastock.cleaning-staff.register.post') }}" id="registrationForm">
                @csrf
                
                <!-- Información Personal -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-green-600"></i>
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="first_name" 
                               id="first_name" 
                               required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('first_name') border-red-500 @enderror"
                               placeholder="Tu nombre"
                               value="{{ old('first_name') }}">
                        @error('first_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Primer Apellido -->
                    <div>
                        <label for="first_last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-green-600"></i>
                            Primer Apellido <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="first_last_name" 
                               id="first_last_name" 
                               required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('first_last_name') border-red-500 @enderror"
                               placeholder="Tu primer apellido"
                               value="{{ old('first_last_name') }}">
                        @error('first_last_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Segundo Apellido -->
                    <div>
                        <label for="second_last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-2 text-green-600"></i>
                            Segundo Apellido
                        </label>
                        <input type="text" 
                               name="second_last_name" 
                               id="second_last_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('second_last_name') border-red-500 @enderror"
                               placeholder="Tu segundo apellido (opcional)"
                               value="{{ old('second_last_name') }}">
                        @error('second_last_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo de Documento -->
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-2 text-green-600"></i>
                            Tipo de Documento <span class="text-red-500">*</span>
                        </label>
                        <select name="document_type" 
                                id="document_type" 
                                required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('document_type') border-red-500 @enderror">
                            <option value="">Selecciona el tipo</option>
                            <option value="CC" {{ old('document_type') == 'CC' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                            <option value="TI" {{ old('document_type') == 'TI' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                            <option value="CE" {{ old('document_type') == 'CE' ? 'selected' : '' }}>Cédula de Extranjería</option>
                            <option value="PA" {{ old('document_type') == 'PA' ? 'selected' : '' }}>Pasaporte</option>
                        </select>
                        @error('document_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Número de Documento -->
                    <div>
                        <label for="document_number" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-hashtag mr-2 text-green-600"></i>
                            Número de Documento <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="document_number" 
                               id="document_number" 
                               required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('document_number') border-red-500 @enderror"
                               placeholder="Número de documento"
                               value="{{ old('document_number') }}">
                        @error('document_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

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

                    <!-- Teléfono -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-phone mr-2 text-green-600"></i>
                            Teléfono
                        </label>
                        <input type="tel" 
                               name="phone" 
                               id="phone" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                               placeholder="Número de teléfono"
                               value="{{ old('phone') }}">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dirección -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt mr-2 text-green-600"></i>
                        Dirección
                    </label>
                    <input type="text" 
                           name="address" 
                           id="address" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('address') border-red-500 @enderror"
                           placeholder="Tu dirección de residencia"
                           value="{{ old('address') }}">
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseñas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Contraseña -->
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
                                   placeholder="Mínimo 8 caracteres">
                            <button type="button" 
                                    onclick="togglePassword('password')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-green-600"></i>
                            Confirmar Contraseña <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" 
                                   name="password_confirmation" 
                                   id="password_confirmation" 
                                   required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                   placeholder="Repite tu contraseña">
                            <button type="button" 
                                    onclick="togglePassword('password_confirmation')" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i id="password_confirmation-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-between items-center pt-6">
                    <a href="{{ route('infrastock.cleaning-staff.login') }}" 
                       class="text-green-600 hover:text-green-800 font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Ya tengo cuenta
                    </a>
                    
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-8 rounded-md transition duration-200">
                        <i class="fas fa-user-plus mr-2"></i>
                        Registrarse
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center">
            <p class="text-green-200 text-sm">
                <i class="fas fa-shield-alt mr-1"></i>
                Tus datos están protegidos y seguros
            </p>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const passwordIcon = document.getElementById(fieldId + '-icon');
            
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

        // Auto-focus en el primer campo
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('first_name').focus();
        });

        // Validación en tiempo real de confirmación de contraseña
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });

        // Validación en tiempo real de longitud de contraseña
        document.getElementById('password').addEventListener('input', function() {
            if (this.value && this.value.length < 8) {
                this.setCustomValidity('La contraseña debe tener al menos 8 caracteres');
            } else {
                this.setCustomValidity('');
            }
            
            // Revalidar confirmación si ya tiene valor
            const confirmPassword = document.getElementById('password_confirmation');
            if (confirmPassword.value) {
                confirmPassword.dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>
