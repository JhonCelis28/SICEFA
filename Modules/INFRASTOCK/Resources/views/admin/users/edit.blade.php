<!--
    * @file edit.blade.php
    * @brief Vista para editar un usuario existente desde el panel administrativo de INFRASTOCK.
    *
    * Esta vista presenta un formulario pre-poblado para que el administrador pueda
    * modificar la información de un usuario existente, incluyendo datos personales,
    * rol y estado activo/inactivo. Incluye validación del lado del cliente y del
    * servidor para asegurar la integridad de los datos.
    * Utiliza Tailwind CSS para un diseño responsive y moderno.
    * Extiende la plantilla `master.blade.php` del módulo INFRASTOCK.
    *
    * @param User $user Usuario específico a editar con sus relaciones cargadas.
    * @param Collection $roles Lista de roles disponibles para asignar al usuario.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.master')

@section('title', 'Editar Usuario - INFRASTOCK')

@section('content')

<div class="container mx-auto px-4 py-6">
    <!-- Encabezado de la página -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-user-edit text-green-600 mr-2"></i>
                Editar Usuario
            </h1>
            <p class="text-gray-600 mt-2">Modifique la información del usuario seleccionado.</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('infrastock.admin.users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver al Listado
            </a>
            <a href="{{ route('infrastock.admin.users.show', $user->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                <i class="fas fa-eye mr-2"></i>
                Ver Detalles
            </a>
        </div>
    </div>

    <!-- Formulario de Edición -->
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg">
            <div class="bg-yellow-600 text-white px-6 py-4 rounded-t-xl">
                <h5 class="text-lg font-semibold">
                    <i class="fas fa-user-edit mr-2"></i>
                    Información del Usuario
                </h5>
            </div>
            <div class="p-6">
                <form action="{{ route('infrastock.admin.users.update', $user->id) }}" method="POST" id="userForm">
                    @csrf
                    @method('PUT')
                    
                    <!-- Información Personal -->
                    <div class="mb-8">
                        <h6 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-4">
                            <i class="fas fa-user mr-2 text-green-600"></i>
                            Información Personal
                        </h6>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('first_name') border-red-500 @enderror" 
                                       id="first_name" 
                                       name="first_name" 
                                       value="{{ old('first_name', $user->person->first_name) }}" 
                                       required>
                                @error('first_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="first_last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Primer Apellido <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('first_last_name') border-red-500 @enderror" 
                                       id="first_last_name" 
                                       name="first_last_name" 
                                       value="{{ old('first_last_name', $user->person->first_last_name) }}" 
                                       required>
                                @error('first_last_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="second_last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Segundo Apellido
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('second_last_name') border-red-500 @enderror" 
                                       id="second_last_name" 
                                       name="second_last_name" 
                                       value="{{ old('second_last_name', $user->person->second_last_name) }}">
                                @error('second_last_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Información de Documento -->
                    <div class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Tipo de Documento <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('document_type') border-red-500 @enderror" 
                                        id="document_type" 
                                        name="document_type" 
                                        required>
                                    <option value="">Seleccione el tipo</option>
                                    @php
                                        $currentDocType = old('document_type', $user->person->document_type ?? '');
                                        $docTypeMap = [
                                            '1' => 'Cédula de Ciudadanía',
                                            '2' => 'Tarjeta de Identidad', 
                                            '3' => 'Cédula de Extranjería',
                                            '4' => 'Pasaporte',
                                            'Cédula de ciudadanía' => '1',
                                            'Cédula de Ciudadanía' => '1',
                                            'Tarjeta de identidad' => '2',
                                            'Tarjeta de Identidad' => '2',
                                            'Cédula de extranjería' => '3',
                                            'Cédula de Extranjería' => '3',
                                            'Pasaporte' => '4'
                                        ];
                                        $selectedValue = $docTypeMap[$currentDocType] ?? $currentDocType;
                                    @endphp
                                    <option value="1" {{ $selectedValue == '1' ? 'selected' : '' }}>Cédula de Ciudadanía</option>
                                    <option value="2" {{ $selectedValue == '2' ? 'selected' : '' }}>Tarjeta de Identidad</option>
                                    <option value="3" {{ $selectedValue == '3' ? 'selected' : '' }}>Cédula de Extranjería</option>
                                    <option value="4" {{ $selectedValue == '4' ? 'selected' : '' }}>Pasaporte</option>
                                </select>
                                @error('document_type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="document_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Número de Documento <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('document_number') border-red-500 @enderror" 
                                       id="document_number" 
                                       name="document_number" 
                                       value="{{ old('document_number', $user->person->document_number) }}" 
                                       required>
                                @error('document_number')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teléfono
                                </label>
                                <input type="tel" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('phone') border-red-500 @enderror" 
                                       id="phone" 
                                       name="phone" 
                                       value="{{ old('phone', $user->person->telephone1 == 'N/A' ? '' : $user->person->telephone1) }}">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Información de Contacto -->
                    <div class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Correo Electrónico <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $user->email) }}" 
                                       required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Dirección
                                </label>
                                <input type="text" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('address') border-red-500 @enderror" 
                                       id="address" 
                                       name="address" 
                                       value="{{ old('address', $user->person->address) }}">
                                @error('address')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Configuración del Usuario -->
                    <div class="mb-8">
                        <h6 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-4">
                            <i class="fas fa-cog mr-2 text-green-600"></i>
                            Configuración del Usuario
                        </h6>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="role_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Rol <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('role_id') border-red-500 @enderror" 
                                        id="role_id" 
                                        name="role_id" 
                                        required>
                                    <option value="">Seleccione un rol</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" 
                                                {{ old('role_id', $user->roles->first()->id ?? '') == $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                                    Estado <span class="text-red-500">*</span>
                                </label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('is_active') border-red-500 @enderror" 
                                        id="is_active" 
                                        name="is_active" 
                                        required>
                                    <option value="1" {{ old('is_active', !$user->trashed() ? '1' : '0') == '1' ? 'selected' : '' }}>Activo</option>
                                    <option value="0" {{ old('is_active', !$user->trashed() ? '1' : '0') == '0' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                                @error('is_active')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-8">
                        <h6 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-4">
                            <i class="fas fa-lock mr-2 text-green-600"></i>
                            Cambiar Contraseña
                        </h6>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                <p class="text-blue-700 text-sm">
                                    <strong>Nota:</strong> Deje estos campos vacíos si no desea cambiar la contraseña actual.
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nueva Contraseña
                                </label>
                                <input type="password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('password') border-red-500 @enderror" 
                                       id="password" 
                                       name="password">
                                @error('password')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirmar Nueva Contraseña
                                </label>
                                <input type="password" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" 
                                       id="password_confirmation" 
                                       name="password_confirmation">
                            </div>
                        </div>
                    </div>

                    <!-- Información del Usuario -->
                    <div class="mb-8">
                        <h6 class="text-lg font-semibold text-gray-800 border-b border-gray-200 pb-2 mb-4">
                            <i class="fas fa-info-circle mr-2 text-gray-600"></i>
                            Información del Sistema
                        </h6>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ID de Usuario</label>
                                <p class="text-gray-900 font-medium">{{ $user->id }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Registro</label>
                                <p class="text-gray-900">{{ $user->created_at->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('infrastock.admin.users.show', $user->id) }}" 
                           class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Actualizar Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-focus en el primer campo
    document.getElementById('first_name').focus();
    
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
});
</script>
@endsection