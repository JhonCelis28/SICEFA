<!--
    * @file profile.blade.php
    * @brief Vista para la gestión del perfil del Personal de Aseo.
    *
    * Esta vista permite al usuario gestionar su información personal,
    * cambiar contraseña y configurar preferencias de notificaciones.
    * Incluye validación del lado del cliente y del servidor para
    * asegurar la integridad de los datos.
    * Utiliza Tailwind CSS para un diseño responsive y moderno.
    *
    * @param User $user Usuario autenticado actual.
    * @author [Tu Nombre/Equipo]
    * @date [Fecha de Creación/Última Modificación]
-->
@extends('infrastock::layouts.cleaning-staff-master')

@section('title', 'Mi Perfil - Personal de Aseo INFRASTOCK')

@section('content')
<!-- Breadcrumb -->
@section('breadcrumb-items')
<li class="text-gray-700">Mi Perfil</li>
@endsection
        
        <!-- Header -->
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Mi Perfil</h2>
            <p class="text-gray-600 mt-2">Gestiona tu información personal y configuración de cuenta.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Información Personal -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-6">Información Personal</h3>
                    
                    <form action="{{ route('infrastock.cleaning-staff.profile.update') }}" method="POST" id="profileForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Correo Electrónico <span class="text-red-500">*</span>
                                </label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email', $user->email) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                                       required>
                                @error('email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Teléfono
                                </label>
                                <input type="tel" 
                                       name="phone" 
                                       id="phone" 
                                       value="{{ old('phone', $user->person->phone ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('phone') border-red-500 @enderror">
                                @error('phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Dirección -->
                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Dirección
                                </label>
                                <input type="text" 
                                       name="address" 
                                       id="address" 
                                       value="{{ old('address', $user->person->address ?? '') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent @error('address') border-red-500 @enderror">
                                @error('address')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Botón de Guardar -->
                        <div class="flex justify-end mt-6">
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-md transition-colors duration-200">
                                <i class="fas fa-save mr-2"></i>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="space-y-6">
                
                <!-- Información de la Cuenta -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la Cuenta</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">ID de Usuario</span>
                            <span class="text-sm font-medium text-gray-900">#{{ $user->id }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Nombre</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->nickname ?? 'N/A' }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Rol</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Personal de Aseo
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Miembro desde</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas de Actividad -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Estadísticas de Actividad</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Solicitudes Totales</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->warehouseMovements()->where('item_type', 'equipment')->count() }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Solicitudes Pendientes</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->warehouseMovements()->where('item_type', 'equipment')->where('role', 'Solicitud')->count() }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Solicitudes Aprobadas</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->warehouseMovements()->where('item_type', 'equipment')->where('role', 'approved')->count() }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Insumos Entregados</span>
                            <span class="text-sm font-medium text-gray-900">{{ $user->warehouseMovements()->where('item_type', 'equipment')->where('role', 'delivered')->count() }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
@endsection

@section('script')
<script>
    // Script específico para la vista de perfil
    console.log('Vista de Perfil cargada correctamente');
</script>
@endsection
