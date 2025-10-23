@extends('infrastock::layouts.cleaning-staff-master')

@section('title', 'Mi Perfil')

@section('breadcrumb-items')
<li class="flex items-center">
    <span class="text-gray-500">Mi Perfil</span>
</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header de la página -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Mi Perfil</h1>
                <p class="text-gray-600">Gestiona tu información personal y configuración de cuenta</p>
            </div>
            <div class="flex items-center space-x-2">
                <i class="fas fa-user-circle text-green-500 text-2xl"></i>
                <span class="text-sm text-gray-500">Información personal</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Formulario de Edición de Perfil -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center mb-6">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <i class="fas fa-edit text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Editar Información</h3>
                    <p class="text-gray-600">Actualiza tus datos personales</p>
                </div>
            </div>

            <form action="{{ route('infrastock.cleaning-staff.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user mr-2 text-green-500"></i>
                        Nombre Completo *
                    </label>
                    <input type="text" name="name" id="name" required
                           value="{{ old('name', $user->name) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('name') border-red-500 @enderror"
                           placeholder="Ingresa tu nombre completo">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Correo Electrónico -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope mr-2 text-green-500"></i>
                        Correo Electrónico *
                    </label>
                    <input type="email" name="email" id="email" required
                           value="{{ old('email', $user->email) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('email') border-red-500 @enderror"
                           placeholder="Ingresa tu correo electrónico">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Apodo/Nickname -->
                <div>
                    <label for="nickname" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag mr-2 text-green-500"></i>
                        Apodo (Opcional)
                    </label>
                    <input type="text" name="nickname" id="nickname"
                           value="{{ old('nickname', $user->nickname) }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('nickname') border-red-500 @enderror"
                           placeholder="Ingresa tu apodo o nombre de usuario">
                    @error('nickname')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contraseña -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-green-500"></i>
                        Nueva Contraseña (Opcional)
                    </label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200 @error('password') border-red-500 @enderror"
                           placeholder="Deja vacío para mantener la contraseña actual">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-green-500"></i>
                        Confirmar Nueva Contraseña
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                           placeholder="Confirma tu nueva contraseña">
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 pt-4">
                    <button type="reset" class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        <i class="fas fa-undo mr-2"></i>
                        Restablecer
                    </button>
                    <button type="submit" class="px-6 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Actualizar Perfil
                    </button>
                </div>
            </form>
        </div>

        <!-- Información del Perfil -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Información Actual</h3>
                        <p class="text-gray-600">Datos de tu cuenta</p>
                    </div>
                </div>
                <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                    Personal de Aseo
                </span>
            </div>

            <div class="space-y-6">
                <!-- Avatar y Nombre -->
                <div class="text-center">
                    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user text-green-600 text-3xl"></i>
                    </div>
                    <h4 class="text-xl font-bold text-gray-900">{{ $user->name }}</h4>
                    @if($user->nickname)
                        <p class="text-gray-600">"{{ $user->nickname }}"</p>
                    @endif
                </div>

                <!-- Información Detallada -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-envelope text-gray-500"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Correo Electrónico</p>
                            <p class="text-gray-900">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-calendar text-gray-500"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Miembro desde</p>
                            <p class="text-gray-900">{{ $user->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <i class="fas fa-clock text-gray-500"></i>
                        <div>
                            <p class="text-sm font-medium text-gray-700">Última actualización</p>
                            <p class="text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
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

                <!-- Estadísticas del Usuario -->
                <div class="border-t pt-6">
                    <h5 class="font-semibold text-gray-900 mb-4">Estadísticas de Actividad</h5>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">Solicitudes</p>
                            <p class="text-lg font-bold text-blue-600">{{ \Modules\INFRASTOCK\Entities\WarehouseMovement::where('user_id', $user->id)->count() }}</p>
                        </div>
                        <div class="text-center p-3 bg-orange-50 rounded-lg">
                            <i class="fas fa-boxes text-orange-600 text-xl mb-2"></i>
                            <p class="text-sm font-medium text-gray-700">Sobrantes</p>
                            <p class="text-lg font-bold text-orange-600">{{ \Modules\INFRASTOCK\Entities\Surplus::where('user_id', $user->id)->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validación de contraseña en tiempo real
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const confirmPassword = document.getElementById('password_confirmation');
    
    if (password.length > 0 && password.length < 8) {
        this.setCustomValidity('La contraseña debe tener al menos 8 caracteres');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Las contraseñas no coinciden');
    } else {
        this.setCustomValidity('');
    }
});
</script>
@endsection